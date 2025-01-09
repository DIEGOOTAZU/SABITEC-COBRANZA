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

// Verificar si se enviaron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $placa = $_POST['placa'];
    $cliente = $_POST['cliente'];
    $mensualidad_real = $_POST['mensualidad_real'];
    $letra = $_POST['letra'];
    $inicial = $_POST['inicial'];
    $fecha_contrato = $_POST['fecha_contrato'];

    try {
        // Actualizar los datos del contrato
        $stmt = $conn->prepare("UPDATE data_cobranzas SET placa = ?, cliente = ?, mensualidad_real = ?, letra = ?, inicial = ?, fecha_contrato = ? WHERE id = ?");
        $stmt->execute([$placa, $cliente, $mensualidad_real, $letra, $inicial, $fecha_contrato, $id]);

        // Redirigir de nuevo a la página de administración
        header("Location: administrar_contratos.php?mensaje=editado");
        exit;
    } catch (PDOException $e) {
        die("Error al editar el contrato: " . $e->getMessage());
    }
} else {
    die("Datos insuficientes para editar el contrato.");
}
?>
