

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

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $placa = isset($_POST['placa']) ? trim($_POST['placa']) : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $anio = isset($_POST['anio']) ? intval($_POST['anio']) : 0;

    if ($id > 0 && !empty($placa) && !empty($marca) && !empty($modelo) && !empty($color) && $anio > 0) {
        try {
            // Actualizar el vehículo
            $stmt = $conn->prepare("
                UPDATE vehiculos 
                SET placa = :placa, marca = :marca, modelo = :modelo, color = :color, anio = :anio 
                WHERE id = :id
            ");
            $stmt->bindParam(':placa', $placa);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':modelo', $modelo);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':anio', $anio);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                // Redirigir a vehiculos.php después de la actualización
                header("Location: vehiculos.php?success=edit");
                exit;
            } else {
                echo "Error al actualizar el vehículo.";
            }
        } catch (PDOException $e) {
            die("Error al actualizar el vehículo: " . $e->getMessage());
        }
    } else {
        echo "Por favor, complete todos los campos correctamente.";
    }
} else {
    echo "Método no permitido.";
}
?>
