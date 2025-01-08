<?php
// Configuración de la base de datos PostgreSQL en Render
$host = 'dpg-ctvb9epu0jms73b0j5d0-a'; // Reemplaza con el "Nombre de host" de Render
$dbname = 'db_sabitec';               // Reemplaza con el "Base de datos" de Render
$username = 'db_sabitec_user';        // Reemplaza con el "Nombre de usuario" de Render
$password = 'Ruy8SL8VOIfCpLwS07etSc4BF7oBeJmP'; // Reemplaza con la "Contraseña" de Render
$port = 5432;                         // Reemplaza con el "Puerto" de Render

try {
    // Crear la conexión a la base de datos
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    // Configurar el modo de error de PDO para excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a PostgreSQL.";
} catch (PDOException $e) {
    // Manejo de errores en caso de falla de conexión
    die("Error de conexión: " . $e->getMessage());
}
?>
