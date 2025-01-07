<?php
require_once 'config/db.php';

if (isset($_GET['cliente'])) {
    $cliente = $_GET['cliente'];

    try {
        // Consulta para obtener las placas asociadas al cliente
        $stmt = $conn->prepare("SELECT placa FROM data_cobranzas WHERE cliente = :cliente");
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->execute();
        $placas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver las placas en formato JSON
        echo json_encode($placas);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Cliente no especificado.']);
}
?>
