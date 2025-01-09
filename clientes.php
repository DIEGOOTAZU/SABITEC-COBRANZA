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
    $stmt = $conn->prepare("SELECT id, tipo_persona, nombres, tipo_documento, documento, sexo, telefono FROM clientes");
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
    <title>Administrar Clientes</title>
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

<script>
    // Función para mostrar/ocultar el submenú
    function toggleSubmenu(event) {
        event.preventDefault();
        const parent = event.target.closest('.has-submenu');
        parent.classList.toggle('active');
    }
</script>



    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Administrar Clientes</h2>
        <!-- Botón para nuevo cliente -->
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#nuevoClienteModal">Nuevo Cliente</button>
        <table class="table table-bordered">
            <thead class="table-header">
                <tr>
                    <th>#</th>
                    <th>Tipo Persona</th>
                    <th>Nombres</th>
                    <th>Tipo Documento</th>
                    <th>Documento</th>
                    <th>Sexo</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $index => $cliente): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($cliente['tipo_persona']) ?></td>
                        <td><?= htmlspecialchars($cliente['nombres']) ?></td>
                        <td><?= htmlspecialchars($cliente['tipo_documento']) ?></td>
                        <td><?= htmlspecialchars($cliente['documento']) ?></td>
                        <td><?= htmlspecialchars($cliente['sexo']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                        <td>
                            <!-- Botón Editar -->
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editarClienteModal<?= $cliente['id'] ?>">Editar</button>
                            <!-- Botón Eliminar con Confirmación -->
                            <form action="eliminar_cliente.php" method="POST" style="display:inline;" onsubmit="return confirmarEliminacion();">
                                <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal para Editar Cliente -->
                    <div class="modal fade" id="editarClienteModal<?= $cliente['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editarClienteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarClienteModalLabel">Editar Cliente</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="editar_cliente.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                                        <div class="form-group">
                                            <label>Tipo de Persona</label>
                                            <select name="tipo_persona" class="form-control" required>
                                                <option value="Natural" <?= $cliente['tipo_persona'] === 'Natural' ? 'selected' : '' ?>>Natural</option>
                                                <option value="Jurídica" <?= $cliente['tipo_persona'] === 'Jurídica' ? 'selected' : '' ?>>Jurídica</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nombres o Razón Social</label>
                                            <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($cliente['nombres']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Tipo de Documento</label>
                                            <select name="tipo_documento" class="form-control" required>
                                                <option value="DNI" <?= $cliente['tipo_documento'] === 'DNI' ? 'selected' : '' ?>>DNI</option>
                                                <option value="RUC" <?= $cliente['tipo_documento'] === 'RUC' ? 'selected' : '' ?>>RUC</option>
                                                <option value="Pasaporte" <?= $cliente['tipo_documento'] === 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <input type="text" name="documento" class="form-control" value="<?= htmlspecialchars($cliente['documento']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Sexo</label><br>
                                            <input type="radio" name="sexo" value="M" <?= $cliente['sexo'] === 'M' ? 'checked' : '' ?>> Masculino
                                            <input type="radio" name="sexo" value="F" <?= $cliente['sexo'] === 'F' ? 'checked' : '' ?>> Femenino
                                        </div>
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($cliente['telefono']) ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para Nuevo Cliente -->
    <div class="modal fade" id="nuevoClienteModal" tabindex="-1" role="dialog" aria-labelledby="nuevoClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevoClienteModalLabel">Agregar Nuevo Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="agregar_cliente.php" method="POST">
                        <div class="form-group">
                            <label>Tipo de Persona</label>
                            <select name="tipo_persona" class="form-control" required>
                                <option value="Natural">Natural</option>
                                <option value="Jurídica">Jurídica</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nombres o Razón Social</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Documento</label>
                            <select name="tipo_documento" class="form-control" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Documento</label>
                            <input type="text" name="documento" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Sexo</label><br>
                            <input type="radio" name="sexo" value="M" required> Masculino
                            <input type="radio" name="sexo" value="F" required> Femenino
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este cliente?");
        }
    </script>
</body>
</html>
