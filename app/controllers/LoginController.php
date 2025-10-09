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
      die("Database connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    self::login($conn, $email, $password);
    $conn->close();
  }

  private static function login($conn, $email, $password)
  {
    $sql = "SELECT U.EMAIL FROM usuarios AS U WHERE U.EMAIL = '$email' AND U.CONTRASENA = '$password'";
    $resultado = $conn->query($sql);
    if (!$resultado) {
      echo "Error en la consulta SQL: " . $conn->error;
      return;
    }

    if ($resultado->num_rows > 0) {
      echo "Login exitoso";
      return;
    } else {
      echo "Error: Usuario o contraseña incorrectos";
      return;
    }
  }
}
