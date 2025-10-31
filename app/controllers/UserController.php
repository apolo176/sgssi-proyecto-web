<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function dd(...$vars)
{
    foreach ($vars as $v) {
        echo "<pre>";
        var_dump($v);
        echo "</pre>";
    }
    die();
}
class UserController
{

    /* -------------------------------- REGISTER --------------------------- */
    public static function showRegister()
    {
        include __DIR__ . '/../views/register.html';
    }
    public static function processRegisterForm() // Metodo que procesa el formulario de registro
    {
        $hostname = "db";
        $username = "admin";
        $password = "test";
        $db = "database";

        $conn = new mysqli($hostname, $username, $password, $db);
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $nombreapellido = $_POST['nombreapellido'];
        $dni = $_POST['DNI'];
        $telefono = $_POST['telefono'];
        $fecha = $_POST['fechanac'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        self::register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password);
        $conn->close();
    }

    private static function register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password) //Metodo que registra el usuario en la base de datos si no existe
    {

        // Consulta para verificar si el DNI ya existe
        $sql = "SELECT U.DNI FROM usuarios AS U WHERE U.DNI = '$dni'";
        $resultado = $conn->query($sql);
        if (!$resultado) {
            echo "Error en la consulta SQL: " . $conn->error;
            return;
        }

        if ($resultado->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'El DNI ya está registrado'
            ]);
            return;
        }

        // Consulta para verificar si el email ya existe
        $sql = "SELECT U.EMAIL FROM usuarios AS U WHERE U.EMAIL = '$email'";
        $resultado2 = $conn->query($sql);
        if (!$resultado2) {
            echo "Error en la consulta SQL: " . $conn->error;
            return;
        }

        if ($resultado2->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo ya está registrado'
            ]);;
            return;
        }

        // Inserción del nuevo usuario
        $stmt = $conn->prepare("
        INSERT INTO usuarios (nombre, dni, telefono, fechaNacimiento, email, contrasena)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
        $stmt->bind_param("ssssss", $nombreapellido, $dni, $telefono, $fecha, $email, $password);

        if ($stmt->execute()) {
            $newUserId = $conn->insert_id;

            $stmt = $conn->prepare("SELECT id, nombre, email, dni, telefono, fechaNacimiento FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $newUserId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registro hecho correctamente',
                    'user' => $user
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registro hecho correctamente, pero no se pudo recuperar el usuario'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $conn->error
            ]);
        }
    }




    /*-------------------------------LOGIN--------------------------------- */
    public static function showLogin()
    {
        include __DIR__ . '/../views/login.html';
    }
    public static function processLoginForm() // Metodo que procesa el formulario de login
    {
        $hostname = "db";
        $username = "admin";
        $password = "test";
        $db = "database";

        $conn = new mysqli($hostname, $username, $password, $db);
        if ($conn->connect_error) {
            error_log("DB connection failed: " . $conn->connect_error);
            echo "Error interno, inténtalo más tarde.";
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo "Por favor completa todos los campos.";
            return;
        }

        self::login($conn, $email, $password);
        $conn->close();
    }


    private static function login($conn, $email, $password)
{
    // --- CONFIGURACIÓN ---
    $MAX_ATTEMPTS = 5;        // Intentos máximos por ventana
    $WINDOW_SECONDS = 60;     // Duración de la ventana (en segundos)
    $now = time();

    // 1️⃣ Buscar usuario
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        // Usuario no encontrado → no sumamos intentos, solo mensaje genérico
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos.']);
        $stmt->close();
        return;
    }

    $userId = $row['id'];
    $failedCount = (int)($row['login_failed_count'] ?? 0);
    $windowStart = $row['login_window_start'] ? (int)$row['login_window_start'] : null;

    // 2️⃣ Reiniciar ventana si ha pasado más de 60 segundos
    if ($windowStart === null || ($now - $windowStart) > $WINDOW_SECONDS) {
        $failedCount = 0;
        $windowStart = $now;

        $update = $conn->prepare("UPDATE usuarios SET login_failed_count = 0, login_window_start = ? WHERE id = ?");
        $update->bind_param("ii", $windowStart, $userId);
        $update->execute();
        $update->close();
    }

    // 3️⃣ Comprobar si ha superado el límite de intentos
    if ($failedCount >= $MAX_ATTEMPTS && $windowStart !== null && ($now - $windowStart) < $WINDOW_SECONDS) {
        $wait = $WINDOW_SECONDS - ($now - $windowStart);
        echo json_encode([
            'success' => false,
            'message' => "Demasiados intentos fallidos. Espera {$wait} segundos antes de volver a intentarlo."
        ]);
        $stmt->close();
        return;
    }

    if ($failedCount >= $MAX_ATTEMPTS && ($now - $windowStart) >= $WINDOW_SECONDS) {
        $failedCount = 0;
        $windowStart = null;
        $reset = $conn->prepare("UPDATE usuarios SET login_failed_count = 0, login_window_start = NULL WHERE id = ?");
        $reset->bind_param("i", $userId);
        $reset->execute();
        $reset->close();
    }


    // 4️⃣ Verificar contraseña
    if (trim($password) === trim($row['contrasena'])) {
        // ✅ Login correcto → resetear contadores
        $reset = $conn->prepare("UPDATE usuarios SET login_failed_count = 0, login_window_start = NULL WHERE id = ?");
        $reset->bind_param("i", $userId);
        $reset->execute();
        $reset->close();

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $row['id'],
                'nombre' => $row['nombre']
            ]
        ]);
    } else {
        // ❌ Login fallido → incrementar contador
        // ❌ Login fallido → incrementar contador
        $failedCount++;
        $windowStart = $now; // reiniciar el inicio del bloqueo al último fallo

        $update = $conn->prepare("UPDATE usuarios SET login_failed_count = ?, login_window_start = ? WHERE id = ?");
        $update->bind_param("iii", $failedCount, $windowStart, $userId);
        $update->execute();
        $update->close();

        // Calcular intentos restantes
        $remaining = $MAX_ATTEMPTS - $failedCount;

        // Si ya llegó o superó el límite, mostrar mensaje de bloqueo directamente
        if ($remaining <= 0) {
            echo json_encode([
                'success' => false,
                'message' => "Demasiados intentos fallidos. Espera {$WINDOW_SECONDS} segundos antes de volver a intentarlo."
            ]);
        } else {
            // Mostrar mensaje de intentos restantes
            echo json_encode([
                'success' => false,
                'message' => "Usuario o contraseña incorrectos. Te quedan {$remaining} intento" . ($remaining === 1 ? "" : "s") . " antes del bloqueo."
            ]);
        }

    }

    $stmt->close();
}

    /*-------------------------------User Details--------------------------------- */
    public static function showDetails()
    {
        include __DIR__ . '/../views/userDetails.html';
    }
    public function showUserData($user)
    {
        if (!$user) {
            echo json_encode(null);
            return;
        }

        $conn = new mysqli("db", "admin", "test", "database");
        if ($conn->connect_error)
            die("Database connection failed: " . $conn->connect_error);

        $user = intval($user); // seguridad
        $sql = "SELECT * FROM usuarios WHERE id = $user";
        $resultado = $conn->query($sql);

        if ($resultado && $row = $resultado->fetch_assoc()) {
            header('Content-Type: application/json');
            echo json_encode($row);
        } else {
            echo json_encode(null);
        }

        $conn->close();
    }
    /*-------------------------------User Details--------------------------------- */
    public function showModifyForm()
    {
        include __DIR__ . '/../views/modifyUser.html';
    }
    public function modifyUser($payload)
    {

        if (!$payload || !is_array($payload)) {
            echo "Datos inválidos o incompletos";
            return;
        }

        // Si no hay campos modificados, salimos
        if (empty($payload)) {
            echo "No hay campos para actualizar";
            return;
        }

        $conn = new mysqli("db", "admin", "test", "database");
        if ($conn->connect_error) {
            echo "Error de conexión: " . $conn->connect_error;
            return;
        }

        // Construcción dinámica del UPDATE
        $updates = [];
        $params = [];
        $types = '';

        foreach ($payload as $col => $val) {
            $updates[] = "$col = ?";
            $params[] = $val;
            $types .= 's';
        }

        $sql = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = ?";
        $params[] = $payload['id'];
        $types .= 's';

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "Error preparando la consulta: " . $conn->error;
            return;
        }

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "Datos actualizados correctamente";
        } else {
            echo "Error al actualizar los datos: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
