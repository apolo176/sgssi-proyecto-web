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
  public static function showModifyForm()
  {
    include __DIR__ . '/../views/modifyGame.html';
  }
  public static function showDeleteForm()
  {
    include __DIR__ . '/../views/deleteGame.html';
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
  { // Metodo para añadir el juego

    // Consulta para verificar si el videojuego ya existe con prepared statement
    $stmt = $conn->prepare("SELECT V.nombre FROM videojuegos AS V WHERE V.nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if (!$resultado) {
      echo "Error en la consulta SQL: " . $conn->error;
      return;
    }

    if ($resultado->num_rows > 0) {
      echo "El videojuego ya está registrado";
      return;
    }

    // Inserción del nuevo videojuego si no existe con prepared statement
    $stmt = $conn->prepare("INSERT INTO videojuegos (nombre, genero, fechaLanzamiento, precioSalida, notaMetacritic)
            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdd", $nombre, $genero, $fechaLanzamiento, $precioSalida, $notaMetacritic);
    if ($stmt->execute()) {
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

    // Eliminación del videojuego en base a su ID
    $stmt = $conn->prepare("DELETE FROM videojuegos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();

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
  public static function getItem($id) // Nuevo método para mostrar detalles de un juego
  {
    //$id = $_GET['id'] ?? null; // Obtener el ID del juego desde la URL
    if (!$id) {
      echo json_encode(null);
      return;
    }
    $conn = new mysqli("db", "admin", "test", "database");
    if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

    $id = intval($id); // seguridad

    $stmt = $conn->prepare("SELECT * FROM videojuegos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $row = $resultado->fetch_assoc()) {
      header('Content-Type: application/json');
      echo json_encode($row);
    } else {
      echo json_encode(null);
    }

    $conn->close();
  }

  /*------------------------MODIFY GAME ---------------------*/
  public static function modifyItem($payload)
  {
    if (!$payload || !is_array($payload)) {
      echo "Datos inválidos o incompletos";
      return;
    }

    // Si no hay campos modificados, salimos
    if (empty($payload) || !isset($payload['id'])) {
      echo "No hay campos para actualizar o ID no proporcionado";
      return;
    }

    $conn = new mysqli("db", "admin", "test", "database");
    if ($conn->connect_error) {
      echo "Error de conexión: " . $conn->connect_error;
      return;
    }

    $id = intval($payload['id']); // seguridad
    unset($payload['id']); // eliminamos el id del array para no incluirlo en la actualización

    $fields = [];
    foreach ($payload as $key => $value) {
      $safe_value = $conn->real_escape_string($value);
      $fields[] = "$key = '$safe_value'";
    }

    if (empty($fields)) {
      echo "No hay campos para actualizar";
      return;
    }

    //Actualización de datos del videojuego
    $stmt = $conn->prepare("UPDATE videojuegos SET " . implode(", ", $fields) . " WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() === TRUE) {
      if ($stmt->affected_rows > 0) {
        echo "Videojuego modificado correctamente.";
      } else {
        echo "No se realizaron cambios.";
      }
    } else {
      echo "Error al modificar videojuego: " . $stmt->error;
    }

    $conn->close();
  }

}
