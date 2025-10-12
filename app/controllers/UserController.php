<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    public static function showRegister()
    {
        include __DIR__ . '/../views/register.html';
    }
    public static function processRegisterForm()
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

    private static function register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password)
    {
        $sql = "SELECT U.DNI FROM usuarios AS U WHERE U.DNI = '$dni'";
        $resultado = $conn->query($sql);
        if (!$resultado) {
            echo "Error en la consulta SQL: " . $conn->error;
            return;
        }

        if ($resultado->num_rows > 0) {
            echo "El DNI ya está registrado";
            return;
        }

        $sql = "SELECT U.EMAIL FROM usuarios AS U WHERE U.EMAIL = '$email'";
        $resultado2 = $conn->query($sql);
        if (!$resultado2) {
            echo "Error en la consulta SQL: " . $conn->error;
            return;
        }

        if ($resultado2->num_rows > 0) {
            echo "El correo ya está registrado";
            return;
        }

        $sql = "INSERT INTO usuarios (nombre, dni, telefono, fechaNacimiento, email, contrasena)
            VALUES ('$nombreapellido', '$dni', '$telefono', '$fecha', '$email', '$password')";
        if ($conn->query($sql)) {
            echo "Registro hecho correctamente";
        } else {
            echo "Error al registrar usuario: " . $conn->error;
        }
    }




    /*-------------------------------LOGIN--------------------------------- */
    public static function showLogin()
    {
        include __DIR__ . '/../views/login.html';
    }
    public static function processLoginForm()
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
        $stmt = $conn->prepare("SELECT CONTRASENA FROM usuarios WHERE EMAIL = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {

            if (trim($password) === trim($row['CONTRASENA'])) {
                echo "Login exitoso";
            } else {
                echo "Hola";
            }
        } else {
            echo "Usuario no encontrado";
        }

        $stmt->close();
    }
}

?>