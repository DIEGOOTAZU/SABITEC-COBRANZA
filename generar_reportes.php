<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reporte</title>
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
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .invoice-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header h3 {
            margin: 0;
            color: #007bff;
        }
        .invoice-header p {
            margin: 0;
            color: #6c757d;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details th,
        .invoice-details td {
            padding: 8px;
            text-align: left;
        }
        .invoice-details th {
            background-color: #007bff;
            color: white;
        }
        .invoice-footer {
            text-align: right;
            margin-top: 20px;
        }
        .btn-print {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-print:hover {
            background-color: #0056b3;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .invoice-container, .invoice-container * {
                visibility: visible;
            }
            .invoice-container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
            .add-row, .options-column {
                display: none !important;
            }
            .sidebar, .main-content .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">Sabitec GPS</h3>
    <a href="index.php">Inicio</a>
    <div class="has-submenu">
        <a href="#">Servicios</a>
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
    <div class="invoice-container">
        <div class="invoice-header">
            <h3>Factura Electrónica</h3>
            <p>Sabitec GPS - RUC: 12345678901</p>
        </div>
        <div class="invoice-details">
            <table class="table table-bordered">
                <tr>
                    <th>Fecha</th>
                    <td><input type="text" class="form-control" value="<?= date("d/m/Y") ?>"></td>
                </tr>
                <tr>
                    <th>Número de Factura</th>
                    <td><input type="text" class="form-control" value="F001-00012345"></td>
                </tr>
                <tr>
                    <th>Cliente</th>
                    <td><input type="text" class="form-control" value="Nombre del Cliente"></td>
                </tr>
                <tr>
                    <th>Placa</th>
                    <td><input type="text" class="form-control" value="ABC-123"></td>
                </tr>
            </table>
        </div>
        <div class="invoice-details">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th class="options-column">Opciones</th>
                    </tr>
                </thead>
                <tbody id="invoice-body">
                    <tr>
                        <td><input type="text" class="form-control" value=""></td>
                        <td><input type="number" class="form-control quantity" value="1" onchange="updateTotal(this)"></td>
                        <td><input type="number" class="form-control unit-price" value="100" onchange="updateTotal(this)"></td>
                        <td><input type="number" class="form-control total" value="100" readonly></td>
                        <td class="options-column"><button class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" value=""></td>
                        <td><input type="number" class="form-control quantity" value="1" onchange="updateTotal(this)"></td>
                        <td><input type="number" class="form-control unit-price" value="50" onchange="updateTotal(this)"></td>
                        <td><input type="number" class="form-control total" value="50" readonly></td>
                        <td class="options-column"><button class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-primary add-row" onclick="addRow()">Agregar Fila</button>
        </div>
        <div class="invoice-footer">
            <h4 id="grand-total">Total: 00.00</h4>
            
        </div>
    </div>
</div>
<div class="invoice-footer">
    <div class="d-flex align-items-center justify-content-between">
        <h4 id="grand-total">Total: 00.00</h4>
        <div class="d-flex align-items-center">
            <label for="currency" class="mr-2">Moneda:</label>
            <select id="currency" class="form-control" style="width: 100px;" onchange="updateCurrencySymbol()">
    <option value="USD" selected>Dólares</option>
    <option value="PEN">Soles</option>
</select>

        </div>
    </div>
    <button class="btn-print mt-3" onclick="window.print()">Imprimir</button>
</div>

<script>
   function updateCurrencySymbol() {
    const currency = document.getElementById('currency').value; // Obtiene la moneda seleccionada
    const grandTotalElement = document.getElementById('grand-total'); // Elemento del total
    const grandTotalText = grandTotalElement.textContent; // Texto actual del total
    const grandTotalValue = parseFloat(grandTotalText.match(/\d+(\.\d+)?/)[0]) || 0; // Extraer el valor numérico del total

    if (currency === 'USD') {
        grandTotalElement.textContent = `Total: $${grandTotalValue.toFixed(2)}`;
    } else if (currency === 'PEN') {
        grandTotalElement.textContent = `Total: S/${grandTotalValue.toFixed(2)}`;
    }
}


    function updateTotal(input) {
        const row = input.closest('tr');
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const total = quantity * unitPrice;
        row.querySelector('.total').value = total.toFixed(2);

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.total').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grand-total').textContent = `Total: $${grandTotal.toFixed(2)}`;
    }

    function addRow() {
        const tbody = document.getElementById('invoice-body');
        const newRow = `
            <tr>
                <td><input type="text" class="form-control" placeholder="Descripción"></td>
                <td><input type="number" class="form-control quantity" value="1" onchange="updateTotal(this)"></td>
                <td><input type="number" class="form-control unit-price" value="0" onchange="updateTotal(this)"></td>
                <td><input type="number" class="form-control total" value="0" readonly></td>
                <td class="options-column"><button class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
    }

    function removeRow(button) {
        button.closest('tr').remove();
        updateGrandTotal();
    }
</script>

</body>
</html>
