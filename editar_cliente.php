<?php
// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Verificar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos enviados desde el formulario
    $id = $_POST['id'];
    $tipo_persona = $_POST['tipo_persona'];
    $nombres = $_POST['nombres'];
    $tipo_documento = $_POST['tipo_documento'];
    $documento = $_POST['documento'];
    $sexo = $_POST['sexo'];
    $telefono = $_POST['telefono'];

    try {
        // Preparar la consulta de actualización
        $stmt = $conn->prepare("
            UPDATE clientes
            SET tipo_persona = ?, nombres = ?, tipo_documento = ?, documento = ?, sexo = ?, telefono = ?
            WHERE id = ?
        ");
        $stmt->execute([$tipo_persona, $nombres, $tipo_documento, $documento, $sexo, $telefono, $id]);

        // Redirigir de vuelta a clientes.php con un mensaje de éxito
        header("Location: clientes.php?mensaje=editado");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar los datos: " . $e->getMessage());
    }
} else {
    header("Location: clientes.php");
    exit;
}
?>
