<?php

class VideogameController
{
  public static function showVideoGames()
  {
    include __DIR__ . '/../views/listaVideoJuegos.html';
  }
  public static function showVideoGamesDetail()
  {
    include __DIR__ . '/../views/detalleJuego.html';
  }
  public static function showNewVideoGame()
  {
    include __DIR__ . '/../views/addGame.html';
  }

  /*--------------------SHOW VIDEO GAMES -------------------*/

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

    $sql = "SELECT * FROM videojuegos AS V";
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

  /*------------------------ADD NEW GAME --------------------------*/
  public static function processFormAdd()
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

  /*------------------------DELETE GAME --------------------------*/

  public static function processFormDelete()
  {
    $hostname = "db";
    $username = "admin";
    $password = "test";
    $db = "database";

    $conn = new mysqli($hostname, $username, $password, $db);
    if ($conn->connect_error) {
      die("Database connection failed: " . $conn->connect_error);
    }

    $id = $_GET['item'] ?? null;
    if (!$id) {
      echo "ID no proporcionado.";
      return;
    }

    self::deleteGame($conn, $id);
    $conn->close();
  }

  private static function deleteGame($conn, $id)
  {
    $sql = "DELETE FROM videojuegos WHERE id = '$id'";
    $resultado = $conn->query($sql);

    if (!$resultado) {
      echo "Error en la consulta SQL: " . $conn->error;
      return;
    }

    if ($conn->affected_rows > 0) {
      echo "Videojuego eliminado correctamente.";
    } else {
      echo "No se encontró ningún videojuego con ese ID.";
    }
  }

  /*------------------------SHOW GAME DETAIL ---------------------*/
  public static function mostrarDetalle($id) // Nuevo método para mostrar detalles de un juego
  {
    //$id = $_GET['id'] ?? null; // Obtener el ID del juego desde la URL
    if (!$id) {
      echo json_encode(null);
      return;
    }

    $conn = new mysqli("db", "admin", "test", "database");
    if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

    $id = intval($id); // seguridad
    $sql = "SELECT * FROM videojuegos WHERE id = $id";
    $resultado = $conn->query($sql);

    if ($resultado && $row = $resultado->fetch_assoc()) {
      header('Content-Type: application/json');
      echo json_encode($row);
    } else {
      echo json_encode(null);
    }

    $conn->close();
  }
}
