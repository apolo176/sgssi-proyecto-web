<?php
$hostname = "db";
  $username = "admin";
  $password = "test";
  $db = "database";

  $conn = mysqli_connect($hostname,$username,$password,$db);
  if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
  }

    listUsers($conn);





  function listUsers($conn){

    $query = mysqli_query($conn, "SELECT * FROM usuarios")
      or die (mysqli_error($conn));
    $users = [];
    while ($row = mysqli_fetch_array($query)) {
        $users[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre']
        ];

    }
    return $users;
  }


  echo json_encode(listUsers($conn))
  ?>