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

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $placa = trim($_POST['placa']);
    $cliente = trim($_POST['cliente']);
    $telefono = trim($_POST['telefono']);
    $mensualidad_real = trim($_POST['mensualidad_real']);
    $letra = trim($_POST['letra']);
    $inicial = trim($_POST['inicial']);
    $monto_total = trim($_POST['monto_total']);
    $fecha_pago = trim($_POST['fecha_pago']);
    $fecha_contrato = trim($_POST['fecha_contrato']);

    // Validar que los campos no estén vacíos
    if (empty($placa) || empty($cliente) || empty($telefono) || empty($mensualidad_real) || empty($letra) || empty($inicial) || empty($monto_total) || empty($fecha_pago) || empty($fecha_contrato)) {
        die("Todos los campos son obligatorios.");
    }

    // Insertar los datos en la base de datos
    try {
        $stmt = $conn->prepare("INSERT INTO data_cobranzas (placa, cliente, telefono, mensualidad_real, letra, inicial, monto_total, fecha_pago, fecha_contrato) VALUES (:placa, :cliente, :telefono, :mensualidad_real, :letra, :inicial, :monto_total, :fecha_pago, :fecha_contrato)");
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':cliente', $cliente);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':mensualidad_real', $mensualidad_real);
        $stmt->bindParam(':letra', $letra);
        $stmt->bindParam(':inicial', $inicial);
        $stmt->bindParam(':monto_total', $monto_total);
        $stmt->bindParam(':fecha_pago', $fecha_pago);
        $stmt->bindParam(':fecha_contrato', $fecha_contrato);

        $stmt->execute();

        // Redirigir al listado de contratos con un mensaje de éxito
        header("Location: administrar_contratos.php?success=Contrato agregado correctamente");
        exit;
    } catch (PDOException $e) {
        die("Error al guardar el contrato: " . $e->getMessage());
    }
} else {
    // Si se accede al archivo directamente, redirigir al listado de contratos
    header("Location: administrar_contratos.php");
    exit;
}
