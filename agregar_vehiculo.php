<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = trim($_POST['placa']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $color = trim($_POST['color']);
    $anio = trim($_POST['anio']);

    if (!empty($placa) && !empty($marca) && !empty($modelo) && !empty($color) && is_numeric($anio)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO vehiculos (placa, marca, modelo, color, anio)
                VALUES (:placa, :marca, :modelo, :color, :anio)
            ");
            $stmt->bindParam(':placa', $placa);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':modelo', $modelo);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':anio', $anio);
            $stmt->execute();

            header("Location: vehiculos.php?success=1");
            exit;
        } catch (PDOException $e) {
            die("Error al agregar el vehículo: " . $e->getMessage());
        }
    } else {
        header("Location: vehiculos.php?error=1");
        exit;
    }
}
?>
