<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class LoginController
{
  public static function showForm()
  {
    include __DIR__ . '/../views/login.html';
  }

  public static function processForm()
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
      if (password_verify($password, $row['CONTRASENA'])) {
        echo "Login exitoso";
      } else {
        echo "Contraseña incorrecta";
      }
    } else {
      echo "Usuario no encontrado";
    }

    $stmt->close();
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  LoginController::processForm();
}
?>
