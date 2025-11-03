<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Madrid');

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
        self::startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o malformado']);
            exit;
        }
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o ausente']);
            exit;
        }

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

    private static function register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password) {
        $stmt = $conn->prepare("SELECT U.DNI FROM usuarios AS U WHERE U.DNI = ?");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado']);
            return;
        }

        $stmt = $conn->prepare("SELECT U.EMAIL FROM usuarios AS U WHERE U.EMAIL = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado2 = $stmt->get_result();
        if ($resultado2->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
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
            if ($user = $result->fetch_assoc()) {
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
        self::startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/i', $token)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o malformado']);
            exit;
        }
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'CSRF token inválido o ausente']);
            exit;
        }

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

    private static function login($conn, $email, $password) {
        $MAX_ATTEMPTS = 5;
        $WINDOW_SECONDS = 60;
        $now = time();

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE EMAIL = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$row = $result->fetch_assoc()) {
            echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos.']);
            $stmt->close();
            return;
        }

        $userId = $row['id'];
        $failedCount = (int)($row['login_fallidos'] ?? 0);
        $windowStart = $row['momento_login'] ? (int)$row['momento_login'] : null;

        if ($windowStart === null || ($now - $windowStart) > $WINDOW_SECONDS) {
            $failedCount = 0;
            $windowStart = $now;
            $update = $conn->prepare("UPDATE usuarios SET login_fallidos = 0, momento_login = ? WHERE id = ?");
            $update->bind_param("ii", $windowStart, $userId);
            $update->execute();
            $update->close();
        }

        if ($failedCount >= $MAX_ATTEMPTS && $windowStart !== null && ($now - $windowStart) < $WINDOW_SECONDS) {
            $wait = $WINDOW_SECONDS - ($now - $windowStart);
            echo json_encode([
                'success' => false,
                'message' => "Demasiados intentos fallidos. Espera {$wait} segundos antes de volver a intentarlo."
            ]);
            $stmt->close();
            return;
        }

        if (password_verify($password, $row['contrasena'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $reset = $conn->prepare("UPDATE usuarios SET login_fallidos = 0, momento_login = NULL WHERE id = ?");
            $reset->bind_param("i", $userId);
            $reset->execute();
            $reset->close();

            echo json_encode(['success' => true, 'user' => ['id' => $row['id'], 'nombre' => $row['nombre']]]);
        } else {
            $failedCount++;
            $windowStart = $now;
            $update = $conn->prepare("UPDATE usuarios SET login_fallidos = ?, momento_login = ? WHERE id = ?");
            $update->bind_param("iii", $failedCount, $windowStart, $userId);
            $update->execute();
            $update->close();

            $remaining = $MAX_ATTEMPTS - $failedCount;
            if ($remaining <= 0) {
                self::logFailedLogin($email, "Ha llegado al límite de intentos fallidos.");
                echo json_encode(['success' => false, 'message' => "Demasiados intentos fallidos. Espera {$WINDOW_SECONDS} segundos antes de volver a intentarlo."]);
            } else {
                echo json_encode(['success' => false, 'message' => "Usuario o contraseña incorrectos. Te quedan {$remaining} intento" . ($remaining === 1 ? "" : "s") . " antes del bloqueo."]);
            }
        }
        $stmt->close();
    }

    /*------------------------------- LOGOUT --------------------------------- */
    public static function processLogout() {
        self::startSecureSession();
        session_unset();
        session_destroy();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        header("Location: /login");
        exit;
    }

    /*------------------------------- UTIL --------------------------------- */
    private static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
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

    private static function logFailedLogin($username, $reason) {
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