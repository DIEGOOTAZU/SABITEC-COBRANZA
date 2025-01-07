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

// Consultar los datos de la tabla de contratos
try {
    $stmt = $conn->prepare("SELECT * FROM data_cobranzas");
    $stmt->execute();
    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Contratos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        table {
            width: 100%;
            margin-top: 20px;
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
            <a href="#" class="submenu-toggle">Contratos</a>
            <div class="submenu">
                <a href="agregar_contrato.php">Agregar Nuevo Contrato</a>
                <a href="administrar_contratos.php">Administrar Contratos</a>
            </div>
        </div>
        <div class="has-submenu">
            <a href="#" class="submenu-toggle">Servicios</a>
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
        <h2 class="mb-4">Administrar Contratos de Servicios</h2>
        <button class="btn btn-primary mb-3" onclick="window.location.href='agregar_contrato.php'">Agregar Contrato</button>
        <table class="table table-bordered">
            <thead class="table-header">
                <tr>
                    <th>#</th>
                    <th>Placa</th>
                    <th>Cliente</th>
                    <th>Mensualidad</th>
                    <th>Letra (Meses)</th>
                    <th>Inicial</th> <!-- Nueva columna inicial -->
                    <th>Monto Total</th> <!-- Nueva columna monto total -->
                    <th>Fecha de Pago</th>
                    <th>Fecha de Contrato</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contratos as $index => $contrato): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($contrato['placa']) ?></td>
                        <td><?= htmlspecialchars($contrato['cliente']) ?></td>
                        <td><?= htmlspecialchars($contrato['mensualidad_real']) ?></td>
                        <td><?= htmlspecialchars($contrato['letra'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($contrato['inicial'] ?? 'N/A') ?></td> <!-- Mostrar inicial -->
                        <td><?= htmlspecialchars($contrato['monto_total'] ?? 'N/A') ?></td> <!-- Mostrar monto total -->
                        <td><?= htmlspecialchars($contrato['fecha_pago']) ?></td>
                        <td><?= htmlspecialchars($contrato['fecha_contrato']) ?></td>
                        <td><?= htmlspecialchars($contrato['telefono']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const submenuToggles = document.querySelectorAll('.submenu-toggle');
            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function (event) {
                    event.preventDefault();
                    const parent = toggle.closest('.has-submenu');
                    parent.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>
