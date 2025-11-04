<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Madrid'); // Establecer zona horaria

// Debugging function
function dd(...$vars) {
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
    public static function showRegister() {
        self::startSecureSession(); // Asegura que la sesión esté iniciada
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Genera un token CSRF si no existe
        }
        $csrf_token = $_SESSION['csrf_token'];
        include __DIR__ . '/../views/register.php';
    }

    public static function processRegisterForm() {
        self::startSecureSession();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/i', $token)) { 
            header('HTTP/1.1 400 Bad Request'); // Validación básica del token CSRF
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o malformado']);
            exit;
        }
        if (!hash_equals($_SESSION['csrf_token'], $token)) { // Verificación del token CSRF
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o ausente']);
            exit;
        }

        $hostname = "db";
        $username = "admin";
        $password = "test";
        $db = "database";

        $conn = new mysqli($hostname, $username, $password, $db); // Conexión a la base de datos
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $nombreapellido = $_POST['nombreapellido']; // Recupera los datos del formulario
        $dni = $_POST['DNI'];
        $telefono = $_POST['telefono'];
        $fecha = $_POST['fechanac'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        self::register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password); // Llama a la función de registro
        $conn->close();
    }

    private static function register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password) {
        $stmt = $conn->prepare("SELECT U.DNI FROM usuarios AS U WHERE U.DNI = ?"); // Verifica si el DNI ya está registrado
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado']);
            return;
        }

        $stmt = $conn->prepare("SELECT U.EMAIL FROM usuarios AS U WHERE U.EMAIL = ?"); // Verifica si el email ya está registrado
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado2 = $stmt->get_result();
        if ($resultado2->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashea la contraseña
        // Inserta el nuevo usuario en la base de datos
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nombre, dni, telefono, fechaNacimiento, email, contrasena)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $nombreapellido, $dni, $telefono, $fecha, $email, $hashedPassword);

        if ($stmt->execute()) { 
            $newUserId = $conn->insert_id;
            $stmt = $conn->prepare("SELECT id, nombre, email, dni, telefono, fechaNacimiento FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $newUserId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) { // Recupera los datos del nuevo usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['logged_in'] = true;
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
            echo json_encode(['success' => false, 'message' => 'Error al registrar usuario: ' . $conn->error]);
        }
    }

    /*------------------------------- LOGIN --------------------------------- */
    public static function showLogin() {
        self::startSecureSession(); // Asegura que la sesión esté iniciada
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Genera un token CSRF si no existe
        }
        $csrf_token = $_SESSION['csrf_token'];
        include __DIR__ . '/../views/login.php';
    }

    public static function processLoginForm() {
        self::startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/i', $token)) { // Validación básica del token CSRF
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o malformado']);
            exit;
        }
        if (!hash_equals($_SESSION['csrf_token'], $token)) { // Verificación del token CSRF
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o ausente']);
            exit;
        }

        $hostname = "db";
        $username = "admin";
        $password = "test";
        $db = "database";

        $conn = new mysqli($hostname, $username, $password, $db); // Conexión a la base de datos
        if ($conn->connect_error) {
            error_log("DB connection failed: " . $conn->connect_error);
            echo "Error interno, inténtalo más tarde.";
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (empty($email) || empty($password)) { // Verificación de campos vacíos
            echo "Por favor completa todos los campos.";
            return;
        }

        self::login($conn, $email, $password); // Llama a la función de login
        $conn->close();
    }

    private static function login($conn, $email, $password) {
        $MAX_ATTEMPTS = 5; // Número máximo de intentos permitidos
        $WINDOW_SECONDS = 60; // Ventana de tiempo para los intentos
        $now = time(); // Tiempo actual en segundos

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE EMAIL = ?"); // Recupera el usuario por email
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$row = $result->fetch_assoc()) { // Usuario no encontrado
            echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos.']);
            $stmt->close();
            return;
        }

        $userId = $row['id'];
        $failedCount = (int)($row['login_fallidos'] ?? 0);
        $windowStart = $row['momento_login'] ? (int)$row['momento_login'] : null;

        if ($windowStart === null || ($now - $windowStart) > $WINDOW_SECONDS) { // Reinicia el contador si la ventana ha expirado
            $failedCount = 0;
            $windowStart = $now;
            $update = $conn->prepare("UPDATE usuarios SET login_fallidos = 0, momento_login = ? WHERE id = ?");
            $update->bind_param("ii", $windowStart, $userId);
            $update->execute();
            $update->close();
        }

        if ($failedCount >= $MAX_ATTEMPTS && $windowStart !== null && ($now - $windowStart) < $WINDOW_SECONDS) { // Bloquea el login si se exceden los intentos
            $wait = $WINDOW_SECONDS - ($now - $windowStart);
            echo json_encode([
                'success' => false,
                'message' => "Demasiados intentos fallidos. Espera {$wait} segundos antes de volver a intentarlo."
            ]);
            $stmt->close();
            return;
        }

        if (password_verify($password, $row['contrasena'])) { // Verifica la contraseña
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $reset = $conn->prepare("UPDATE usuarios SET login_fallidos = 0, momento_login = NULL WHERE id = ?");
            $reset->bind_param("i", $userId);
            $reset->execute();
            $reset->close();

            echo json_encode(['success' => true, 'user' => ['id' => $row['id'], 'nombre' => $row['nombre']]]);
        } else { // Contraseña incorrecta
            $failedCount++;
            $windowStart = $now;
            $update = $conn->prepare("UPDATE usuarios SET login_fallidos = ?, momento_login = ? WHERE id = ?");
            $update->bind_param("iii", $failedCount, $windowStart, $userId);
            $update->execute();
            $update->close();

            $remaining = $MAX_ATTEMPTS - $failedCount;
            if ($remaining <= 0) { // Límite de intentos alcanzado
                self::logFailedLogin($email, "Ha llegado al límite de intentos fallidos.");
                echo json_encode(['success' => false, 'message' => "Demasiados intentos fallidos. Espera {$WINDOW_SECONDS} segundos antes de volver a intentarlo."]);
            } else {
                echo json_encode(['success' => false, 'message' => "Usuario o contraseña incorrectos. Te quedan {$remaining} intento" . ($remaining === 1 ? "" : "s") . " antes del bloqueo."]);
            }
        }
        $stmt->close();
    }

    /*-------------------------------User Details--------------------------------- */
    public static function showDetails()
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // Evitar caché
        header("Pragma: no-cache");
        header("Expires: 0"); // Fecha de expiración en el pasado

        session_start(); //Iniciar sesión

        if (!isset($_SESSION["user_id"])) { //Verificar si el usuario está autenticado
            header('Location: /login');
            exit;
        }
        include __DIR__ . '/../views/userDetails.html';
    }
    public function showUserData($user)
    {
        session_start(); //Iniciar sesión

        if (!isset($_SESSION['user_id'])) { //Verificar si el usuario está autenticado
            header('HTTP/1.1 401 Unauthorized'); //Código de estado 401 porque no está autenticado
            echo json_encode(['error' => 'No autenticado']);
            return;
        }

        $user_id_seguro = $_SESSION['user_id'];

        $conn = new mysqli("db", "admin", "test", "database");
        if ($conn->connect_error)
            die("Database connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id_seguro);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $row = $resultado->fetch_assoc()) {
            unset($row['contrasena']); //No enviar la contraseña
            unset($row['login_fallidos']); //No enviar el número de intentos fallidos
            unset($row['momento_login']); //No enviar el momento del último intento de login
            header('Content-Type: application/json');
            echo json_encode($row);
        } else {
            echo json_encode(null);
        }

        $stmt->close();
        $conn->close();
    }
    /*-------------------------------User Details--------------------------------- */
    public function showModifyForm()
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // Evitar caché
        header("Pragma: no-cache");
        header("Expires: 0"); // Fecha de expiración en el pasado

        self::startSecureSession();

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if (!isset($_SESSION["user_id"])) { //Verificar si el usuario está autenticado
            header('Location: /login');
            exit;
        }

        include __DIR__ . '/../views/modifyUser.html';
    }
    public function modifyUser($payload)
    {
        self::startSecureSession();

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if (!isset($_SESSION["user_id"])) { //Verificar si el usuario está autenticado
            header('HTTP/1.1 401 Unauthorized');
            echo "Error: No autorizado";
            return;
        }

        $user_id_seguro = $_SESSION["user_id"]; //Obtener el ID del usuario autenticado

        if (!$payload || !is_array($payload)) {
            echo "Datos inválidos o incompletos";
            return;
        }

        unset($payload['id']); //Evitar que el usuario modifique el ID
        unset($payload['contrasena']); //Evitar que el usuario modifique la contraseña aquí

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
        $params[] = $user_id_seguro;
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


    /*------------------------------- LOGOUT --------------------------------- */
    public static function processLogout() {
        self::startSecureSession(); // Asegura que la sesión esté iniciada
        session_unset();
        session_destroy();
        if (ini_get("session.use_cookies")) { // Elimina la cookie de sesión
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]); 
        }
        header("Location: /login");
        exit;
    }

    /*------------------------------- UTIL --------------------------------- */
    private static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) { // Configura parámetros seguros para la sesión
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    private static function logFailedLogin($username, $reason) { // Registra intentos de login fallidos
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] 
                ?? $_SERVER['REMOTE_ADDR'] 
                ?? 'desconocida';

        $fecha2 = date("Y-m-d H:i:s");
        $log = "[$fecha2] Usuario: $username | IP: $ip | Motivo: $reason\n";

        $file = dirname(__DIR__) . '/logs/failed_logins.txt';
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        file_put_contents($file, $log, FILE_APPEND);
    }
}