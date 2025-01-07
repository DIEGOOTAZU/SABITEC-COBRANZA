<?php
// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $tipo_persona = $_POST['tipo_persona'];
    $nombres = $_POST['nombres'];
    $tipo_documento = $_POST['tipo_documento'];
    $documento = $_POST['documento'];
    $sexo = $_POST['sexo'];
    $telefono = $_POST['telefono'];

    try {
        // Preparar la consulta SQL para insertar los datos
        $stmt = $conn->prepare("INSERT INTO clientes (tipo_persona, nombres, tipo_documento, documento, sexo, telefono) 
                                VALUES (:tipo_persona, :nombres, :tipo_documento, :documento, :sexo, :telefono)");

        // Asignar los valores
        $stmt->bindParam(':tipo_persona', $tipo_persona);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':sexo', $sexo);
        $stmt->bindParam(':telefono', $telefono);

        // Ejecutar la consulta
        $stmt->execute();

        // Redirigir de vuelta a la página clientes.php
        header("Location: clientes.php?mensaje=Cliente agregado correctamente");
        exit;
    } catch (PDOException $e) {
        // Manejo de errores
        die("Error al guardar los datos: " . $e->getMessage());
    }
} else {
    // Si se intenta acceder al archivo directamente, redirigir a clientes.php
    header("Location: clientes.php");
    exit;
}
?>
