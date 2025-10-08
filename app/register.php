<?php
  $hostname = "db";
  $username = "admin";
  $password = "test";
  $db = "database";

  $conn = mysqli_connect($hostname,$username,$password,$db); //Conexión a la base de datos
  if ($conn->connect_error) { //Comprobación de la conexión
    die("Database connection failed: " . $conn->connect_error); //Si hay un error, se muestra el mensaje y se detiene el programa
  }

  $nombreapellido = $_POST['nombreapellido'];
  $dni = $_POST['DNI'];
  $telefono = $_POST['telefono'];
  $fecha = $_POST['fechanac'];
  $email = $_POST['email'];
  $password = $_POST['password'];


  register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password);
  mysqli_close($conn); //Cierre de la conexión

  function register($conn, $nombreapellido, $dni, $telefono, $fecha, $email, $password) {
  $sql = "SELECT U.DNI FROM usuarios AS U WHERE U.DNI = '$dni'"; //Consulta para comprobar si el DNI ya existe
  $resultado = mysqli_query($conn, $sql); //Ejecución de la consulta
  if ($resultado === false) {
    echo "Error en la consulta SQL: " . mysqli_error($conn);
    return;
  }
  if (mysqli_num_rows($resultado) > 0) { //Si la consulta devuelve alguna fila es que el DNI ya existe
    echo "El DNI ya está registrado";
  }
  else {
    $sql = "INSERT INTO usuarios (nombre, dni, telefono, fechaNacimiento, email, contrasena) VALUES ('$nombreapellido', '$dni', '$telefono', '$fecha', '$email', '$password')";
    //Consulta para insertar el nuevo usuario
    if (mysqli_query($conn, $sql)) { //Ejecución de la consulta
      echo "Registro hecho correctamente";
    } else {
      echo "Error al registrar usuario: " . mysqli_error($conn);
    }
  }
  }

?>