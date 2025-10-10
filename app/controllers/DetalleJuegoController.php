<?php
class DetalleJuegoController {
    public static function mostrarDetalle() // Nuevo método para mostrar detalles de un juego
    {
        $id = $_GET['id'] ?? null; // Obtener el ID del juego desde la URL
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

DetalleJuegoController::mostrarDetalle();
