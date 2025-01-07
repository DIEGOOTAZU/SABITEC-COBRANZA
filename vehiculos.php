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

// Consultar los vehículos de la tabla `vehiculos`
try {
    $stmt = $conn->prepare("SELECT * FROM vehiculos");
    $stmt->execute();
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los vehículos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Vehículos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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
        .table-header {
            background-color: #007bff;
            color: white;
        }
        .modal {
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body>
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

<div class="main-content">
    <h2>Administrar Vehículos</h2>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#nuevoVehiculoModal">Nuevo Vehículo</button>
    <table class="table table-bordered">
        <thead class="table-header">
            <tr>
                <th>#</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Año</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vehiculos as $index => $vehiculo): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($vehiculo['placa']) ?></td>
                    <td><?= htmlspecialchars($vehiculo['marca']) ?></td>
                    <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
                    <td><?= htmlspecialchars($vehiculo['color']) ?></td>
                    <td><?= htmlspecialchars($vehiculo['anio']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" 
                                onclick="editarVehiculo(<?= $vehiculo['id'] ?>, '<?= htmlspecialchars($vehiculo['placa']) ?>', '<?= htmlspecialchars($vehiculo['marca']) ?>', '<?= htmlspecialchars($vehiculo['modelo']) ?>', '<?= htmlspecialchars($vehiculo['color']) ?>', <?= $vehiculo['anio'] ?>)">
                            Editar
                        </button>
                        <button class="btn btn-danger btn-sm" 
                                onclick="eliminarVehiculo(<?= $vehiculo['id'] ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('.submenu-toggle').on('click', function (e) {
            e.preventDefault();
            const parent = $(this).closest('.has-submenu');
            parent.toggleClass('active');
        });
    });

    function eliminarVehiculo(id) {
        if (confirm('¿Está seguro de eliminar este vehículo?')) {
            $.ajax({
                url: 'eliminar_vehiculo.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.trim() === 'success') {
                        alert('Vehículo eliminado correctamente.');
                        location.reload();
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert('Error al comunicarse con el servidor.');
                }
            });
        }
    }

    function editarVehiculo(id, placa, marca, modelo, color, anio) {
        $('#vehiculoId').val(id);
        $('#editarPlaca').val(placa);
        $('#editarMarca').val(marca);
        $('#editarModelo').val(modelo);
        $('#editarColor').val(color);
        $('#editarAnio').val(anio);
        $('#editarVehiculoModal').modal('show');
    }
</script>
</body>
</html>
