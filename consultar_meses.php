<?php
require_once 'config/db.php';

if (isset($_GET['placa'])) {
    $placa = $_GET['placa'];

    try {
        $stmt = $conn->prepare("SELECT fechas_pago FROM data_cobranzas WHERE placa = :placa");
        $stmt->bindParam(':placa', $placa);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $fechas_pago = explode(',', $result['fechas_pago']); // Asumimos que los meses están separados por comas
        echo json_encode(array_map('intval', $fechas_pago)); // Convertir a enteros
    } catch (PDOException $e) {
        echo json_encode([]);
    }
}
?>
