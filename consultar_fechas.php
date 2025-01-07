<?php
// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Obtener los datos pasados por GET
$cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : null;
$placa = isset($_GET['placa']) ? trim($_GET['placa']) : null;

// Verificar que los parámetros estén presentes
if (!$cliente || !$placa) {
    die("Error: Cliente o placa no especificados.");
}

try {
    // Obtener datos generales de la placa
    $stmt = $conn->prepare("SELECT * FROM data_cobranzas WHERE cliente = :cliente AND placa = :placa");
    $stmt->execute([':cliente' => $cliente, ':placa' => $placa]);
    $cobranza = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró la cobranza
    if (!$cobranza) {
        die("Error: No se encontró información para el cliente y la placa especificados.");
    }

    // Obtener detalles de pagos
    $detailStmt = $conn->prepare("SELECT * FROM detalle_pagos WHERE data_cobranza_id = :id");
    $detailStmt->execute([':id' => $cobranza['id']]);
    $pagosExistentes = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pagos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        .sidebar .submenu a {
            font-size: 14px;
        }
        .sidebar .has-submenu.active .submenu {
            display: block;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .month-table {
            margin-top: 20px;
        }
        .table-header {
            background-color: #007bff;
            color: white;
        }
        .add-row-button {
            cursor: pointer;
            color: #007bff;
            font-size: 20px;
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
    <h2>Detalle de Pagos</h2>
        <h5>Cliente: <?= htmlspecialchars($cliente) ?></h5>
        <h5>Placa: <?= htmlspecialchars($placa) ?></h5>
        <h5>Mensualidad: $<?= htmlspecialchars($cobranza['mensualidad_real']) ?></h5>
        <h5>Fecha de Contrato: <?= htmlspecialchars($cobranza['fecha_contrato']) ?></h5>
        <h5>Fecha de Pago: <?= htmlspecialchars($cobranza['fecha_pago'] ?? 'Sin definir') ?></h5>
        <h5>Letra: <?= htmlspecialchars($cobranza['letra'] ?? 'N/A') ?> meses</h5>
        <h5>Inicial: $<?= htmlspecialchars($cobranza['inicial'] ?? '0.00') ?></h5>
        <h5>Monto Total: $<?= htmlspecialchars(($cobranza['letra'] ?? 0) * ($cobranza['mensualidad_real'] ?? 0)) ?></h5>

        <!-- Formulario para la tabla editable -->
        <form id="pagosForm" action="guardar_pagos.php" method="POST">
            <input type="hidden" name="cliente" value="<?= htmlspecialchars($cliente) ?>">
            <input type="hidden" name="placa" value="<?= htmlspecialchars($placa) ?>">

            <table class="table table-bordered month-table" id="pagosTable">
                <thead class="table-header">
                    <tr>
                        <th>Efectivo o Banco</th>
                        <th>Día de Pago</th>
                        <th>Letra</th>
                        <th>Importe</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pagosExistentes)): ?>
                        <?php foreach ($pagosExistentes as $pago): ?>
                            <tr data-id="<?= $pago['id'] ?>">
                                <td>
                                    <select name="efectivo_banco[]" class="form-control">
                                        <option value="EFECTIVO" <?= $pago['efectivo_o_banco'] === 'EFECTIVO' ? 'selected' : '' ?>>EFECTIVO</option>
                                        <option value="BANCO" <?= $pago['efectivo_o_banco'] === 'BANCO' ? 'selected' : '' ?>>BANCO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="fecha_pago[]" class="form-control" value="<?= htmlspecialchars($pago['fecha_pago']) ?>">
                                </td>
                                <td>
                                    <input type="text" name="letra[]" class="form-control" placeholder="Ej: 8/35" value="<?= htmlspecialchars($pago['letra']) ?>">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" name="importe[]" class="form-control" placeholder="Ej: 500.00" value="<?= htmlspecialchars($pago['importe']) ?>">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this, <?= $pago['id'] ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- Agregar una fila vacía al final -->
                    <tr>
                        <td>
                            <select name="efectivo_banco[]" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="BANCO">BANCO</option>
                            </select>
                        </td>
                        <td>
                            <input type="date" name="fecha_pago[]" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="letra[]" class="form-control" placeholder="Ej: 8/35">
                        </td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" name="importe[]" class="form-control" placeholder="Ej: 500.00">
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this, null)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center">
                <span class="add-row-button" onclick="agregarFila()">➕ Agregar Fila</span>
            </div>
            <button type="submit" class="btn btn-success save-button mt-3">Guardar Cambios</button>
        </form>
    </div>

    <script>
        // Funciones de interacción de la tabla
        function agregarFila() {
            const tableBody = document.querySelector('#pagosTable tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="efectivo_banco[]" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="EFECTIVO">EFECTIVO</option>
                        <option value="BANCO">BANCO</option>
                    </select>
                </td>
                <td>
                    <input type="date" name="fecha_pago[]" class="form-control">
                </td>
                <td>
                    <input type="text" name="letra[]" class="form-control" placeholder="Ej: 8/35">
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" name="importe[]" class="form-control" placeholder="Ej: 500.00">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this, null)">Eliminar</button>
                </td>
            `;
            tableBody.appendChild(newRow);
        }

        function eliminarFila(button, id) {
            if (confirm('¿Está seguro de eliminar esta fila?')) {
                const row = button.closest('tr');

                if (id) {
                    $.ajax({
                        url: 'eliminar_pago.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response === 'success') {
                                row.remove();
                            } else {
                                alert('Error al eliminar el registro.');
                            }
                        },
                        error: function() {
                            alert('Error al comunicarse con el servidor.');
                        }
                    });
                } else {
                    row.remove();
                }
            }
        }

        function toggleSubmenu(event) {
            event.preventDefault();
            const parent = event.target.closest('.has-submenu');
            parent.classList.toggle('active');
        }
    </script>
</body>
</html>