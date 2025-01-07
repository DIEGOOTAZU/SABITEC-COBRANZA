<?php
// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Verificar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar el ID del cliente a eliminar
    $id = $_POST['id'];

    try {
        // Preparar la consulta para eliminar el registro
        $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->execute([$id]);

        // Redirigir de vuelta a clientes.php con un mensaje de éxito
        header("Location: clientes.php?mensaje=eliminado");
        exit;
    } catch (PDOException $e) {
        die("Error al eliminar el cliente: " . $e->getMessage());
    }
} else {
    header("Location: clientes.php");
    exit;
}
?>
