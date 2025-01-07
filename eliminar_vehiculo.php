<?php
// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Verificar si se recibe el ID del vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        // Eliminar el vehículo
        $stmt = $conn->prepare("DELETE FROM vehiculos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo "success";
    } catch (PDOException $e) {
        echo "Error al eliminar el vehículo: " . $e->getMessage();
    }
} else {
    echo "Error: ID del vehículo no recibido.";
}
?>
