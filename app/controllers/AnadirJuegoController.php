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

    $nombre = $_POST['nombre'];
    $genero = $_POST['genero'];
    $fechaLanzamiento = $_POST['fechaLanzamiento'];
    $precioSalida = $_POST['precioSalida'];
    $notaMetacritic = $_POST['notaMetacritic'];


    self::addgame($conn, $nombre, $genero, $fechaLanzamiento, $precioSalida, $notaMetacritic);
    $conn->close();
  }

  private static function addgame($conn, $nombre, $genero, $fechaLanzamiento, $precioSalida, $notaMetacritic)
  {
    $sql = "SELECT V.nombre FROM videojuegos AS V WHERE V.nombre = '$nombre'";
    $resultado = $conn->query($sql);
    if (!$resultado) {
      echo "Error en la consulta SQL: " . $conn->error;
      return;
    }

    if ($resultado->num_rows > 0) {
      echo "El videojuego ya está registrado";
      return;
    }

    $sql = "INSERT INTO videojuegos (nombre, genero, fechaLanzamiento, precioSalida, notaMetacritic)
            VALUES ('$nombre', '$genero', '$fechaLanzamiento', '$precioSalida', '$notaMetacritic')";
    if ($conn->query($sql)) {
      echo "Registro hecho correctamente";
    } else {
      echo "Error al registrar videojuego: " . $conn->error;
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  RegisterController::processForm();
}
?>
