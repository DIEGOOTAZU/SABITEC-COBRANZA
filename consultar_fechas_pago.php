<?php
require_once 'config/db.php';

if (isset($_GET['placa'])) {
    $placa = $_GET['placa'];

    try {
        // Consulta para obtener los meses de pago asociados a la placa
        $stmt = $conn->prepare("SELECT MONTH(fecha_pago) AS mes_pago FROM data_cobranzas WHERE placa = :placa");
        $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
        $stmt->execute();
        $mesesPagados = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Devolver los meses en formato JSON
        echo json_encode($mesesPagados);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Placa no especificada.']);
}
?>
