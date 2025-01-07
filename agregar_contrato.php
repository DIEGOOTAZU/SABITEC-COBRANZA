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

// Consultar los datos de la tabla de clientes
try {
    $stmt = $conn->prepare("SELECT id, nombres, telefono FROM clientes");
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Contrato</title>
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
        .submenu {
            display: none;
            padding-left: 20px;
        }
        .has-submenu.active .submenu {
            display: block;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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
        <h2 class="text-center">Agregar Nuevo Contrato</h2>
        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="placa">Placa</label>
                    <input type="text" name="placa" id="placa" class="form-control" required>
                </div>
                <div class="form-group mt-3">
                    <label for="cliente">Cliente</label>
                    <input list="clientes" name="cliente_nombre" id="cliente" class="form-control" placeholder="Escriba o seleccione un cliente" required>
                    <datalist id="clientes">
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= htmlspecialchars($cliente['nombres']) ?>" data-telefono="<?= htmlspecialchars($cliente['telefono']) ?>">
                                <?= htmlspecialchars($cliente['nombres']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="form-group mt-3">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" readonly>
                </div>
                <div class="form-group mt-3">
                    <label for="mensualidad_real">Mensualidad Real</label>
                    <input type="number" step="0.01" name="mensualidad_real" id="mensualidad_real" class="form-control" required>
                </div>
                <div class="form-group mt-3">
                    <label for="letra">Letra (Meses)</label>
                    <input type="number" name="letra" id="letra" class="form-control" required>
                </div>
                <div class="form-group mt-3">
                    <label for="inicial">Inicial ($)</label>
                    <input type="number" step="0.01" name="inicial" id="inicial" class="form-control" required>
                </div>
                <div class="form-group mt-3">
                    <label for="monto_total">Monto Total</label>
                    <input type="text" id="monto_total" class="form-control" readonly>
                </div>
                <div class="form-group mt-3">
                    <label for="fecha_pago">Fechas de Pago</label>
                    <input type="number" name="fecha_pago" id="fecha_pago" class="form-control" required>
                </div>
                <div class="form-group mt-3">
                    <label for="fecha_contrato">Fecha de Contrato</label>
                    <input type="date" name="fecha_contrato" id="fecha_contrato" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary mt-4 w-100">Guardar Contrato</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clienteInput = document.getElementById('cliente');
            const telefonoInput = document.getElementById('telefono');
            const dataList = document.getElementById('clientes');
            const mensualidadRealInput = document.getElementById('mensualidad_real');
            const letraInput = document.getElementById('letra');
            const montoTotalInput = document.getElementById('monto_total');

            clienteInput.addEventListener('input', function () {
                const value = clienteInput.value;
                const option = Array.from(dataList.options).find(opt => opt.value === value);
                if (option) {
                    telefonoInput.value = option.getAttribute('data-telefono');
                } else {
                    telefonoInput.value = '';
                }
            });

            function calculateMontoTotal() {
                const mensualidadReal = parseFloat(mensualidadRealInput.value) || 0;
                const letra = parseInt(letraInput.value) || 0;
                montoTotalInput.value = `$${(mensualidadReal * letra).toFixed(2)}`;
            }

            mensualidadRealInput.addEventListener('input', calculateMontoTotal);
            letraInput.addEventListener('input', calculateMontoTotal);

            // Toggle submenu functionality
            document.querySelectorAll('.submenu-toggle').forEach(toggle => {
                toggle.addEventListener('click', function () {
                    this.parentElement.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>
