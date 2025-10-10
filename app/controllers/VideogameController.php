<?php

class VideogameController {
  public static function listarVideojuegos()
  {
    $hostname = "db";
    $username = "admin";
    $password = "test";
    $db = "database";

    $conn = new mysqli($hostname, $username, $password, $db);
    if ($conn->connect_error) {
      die("Database connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT V.id, V.nombre FROM videojuegos AS V";
    $resultado = $conn->query($sql);
    if (!$resultado) {
      echo "Error en la consulta SQL: " . $conn->error;
      return;
    }

    $videogames = [];
    while ($row = $resultado->fetch_assoc()) {
      $videogames[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($videogames);

    $conn->close();
  }
}

VideogameController::listarVideojuegos();
?>