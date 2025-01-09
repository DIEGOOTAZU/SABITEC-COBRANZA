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

// Variables iniciales
$datosContrato = null;
$datosVehiculo = null;
$pagos = [];
$mensajeError = "";

// Si se envió una placa desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = trim($_POST['placa']);

    try {
        // Buscar los datos del contrato por placa
        $stmt = $conn->prepare("SELECT * FROM data_cobranzas WHERE placa = :placa");
        $stmt->execute([':placa' => $placa]);
        $datosContrato = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datosContrato) {
            // Obtener los pagos asociados al contrato
            $stmtPagos = $conn->prepare("SELECT * FROM detalle_pagos WHERE data_cobranza_id = :id");
            $stmtPagos->execute([':id' => $datosContrato['id']]);
            $pagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);

            // Obtener los datos del vehículo asociado
            $stmtVehiculo = $conn->prepare("SELECT * FROM vehiculos WHERE placa = :placa");
            $stmtVehiculo->execute([':placa' => $placa]);
            $datosVehiculo = $stmtVehiculo->fetch(PDO::FETCH_ASSOC);
        } else {
            $mensajeError = "No se encontró un contrato con la placa especificada.";
        }
    } catch (PDOException $e) {
        $mensajeError = "Error al obtener los datos: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 250px;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .submenu {
            display: none;
            padding-left: 20px;
        }
        .sidebar .has-submenu.active .submenu {
            display: block;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .details-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .details-section {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .table-header {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">Sabitec GPS</h3>
    <a href="index.php">Inicio</a>
    <div class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)">Contratos</a>
        <div class="submenu">
            <a href="agregar_contrato.php">Agregar Nuevo Contrato</a>
            <a href="administrar_contratos.php">Administrar Contratos</a>
        </div>
    </div>
    <div class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)">Servicios</a>
        <div class="submenu">
            <a href="consulta_pagos.php">Consulta de Pagos</a>
            <a href="generar_reportes.php">Generar Reportes</a>
        </div>
    </div>
    <a href="#">Cobranzas</a>
    <a href="administracion.php">Administración</a>
    <a href="clientes.php">Clientes</a>
    <a href="vehiculos.php">Vehículos</a>
    <a href="#">Tipos de Servicios</a>
    <a href="logout.php">Cerrar Sesión</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Administración</h2>

    <!-- Formulario de búsqueda -->
    <form method="POST" action="administracion.php" class="mb-4">
        <div class="form-group">
            <label for="placa">Ingrese la Placa:</label>
            <input type="text" name="placa" id="placa" class="form-control" placeholder="Ej: ABC-123" required>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <?php if ($mensajeError): ?>
        <div class="alert alert-danger"><?= $mensajeError ?></div>
    <?php endif; ?>

    <?php if ($datosContrato): ?>
        <!-- Contenedor de detalles -->
        <div class="details-container">
            <!-- Detalles del contrato -->
            <div class="details-section">
                <h4>Detalles del Contrato</h4>
                <p><strong>Cliente:</strong> <?= htmlspecialchars($datosContrato['cliente']) ?></p>
                <p><strong>Fecha de Contrato:</strong> <?= htmlspecialchars($datosContrato['fecha_contrato']) ?></p>
                <p><strong>Fecha de Pago:</strong> <?= htmlspecialchars($datosContrato['fecha_pago']) ?></p>
                <p><strong>Letra:</strong> <?= htmlspecialchars($datosContrato['letra']) ?> meses</p>
                <p><strong>Inicial:</strong> $<?= htmlspecialchars($datosContrato['inicial']) ?></p>
                <p><strong>Mensualidad Real:</strong> $<?= htmlspecialchars($datosContrato['mensualidad_real']) ?></p>
                <p><strong>Monto Total:</strong> $<?= htmlspecialchars($datosContrato['monto_total']) ?></p>
            </div>
            <!-- Información del vehículo -->
<div class="details-section">
    <h4>Información del Vehículo</h4>
    <?php if ($datosVehiculo): ?>
        <p><strong>Placa:</strong> <?= htmlspecialchars($datosVehiculo['placa']) ?></p>
        <p><strong>Marca:</strong> <?= htmlspecialchars($datosVehiculo['marca']) ?></p>
        <p><strong>Modelo:</strong> <?= htmlspecialchars($datosVehiculo['modelo']) ?></p>
        <p><strong>Color:</strong> <?= htmlspecialchars($datosVehiculo['color']) ?></p>
        <p><strong>Año:</strong> <?= htmlspecialchars($datosVehiculo['anio']) ?></p>
    <?php else: ?>
        <p>No se encontraron datos del vehículo para la placa especificada.</p>
    <?php endif; ?>
</div>


    
        </div>

        <!-- Mostrar tabla de pagos -->
        <h4 class="mt-4">Pagos Registrados</h4>
        <table class="table table-bordered">
            <thead class="table-header">
                <tr>
                    <th>#</th>
                    <th>Forma de Pago</th>
                    <th>Fecha de Pago</th>
                    <th>Importe</th>
                    <th>Letra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $index => $pago): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($pago['efectivo_o_banco']) ?></td>
                        <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                        <td>$<?= htmlspecialchars($pago['importe']) ?></td>
                        <td><?= htmlspecialchars($pago['letra']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    function toggleSubmenu(event) {
        event.preventDefault();
        const parent = event.target.closest('.has-submenu');
        parent.classList.toggle('active');
    }
</script>

</body>
</html>
