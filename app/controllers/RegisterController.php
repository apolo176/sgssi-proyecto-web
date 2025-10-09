<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class RegisterController
{
  public static function showForm()
  {
    include __DIR__ . '/../views/register.html';
  }
  
  public static function processForm()
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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  RegisterController::processForm();
}
?>
