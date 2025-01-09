<?php
// Configuración de la base de datos PostgreSQL en Render
$host = 'dpg-ctvvjb23esus73ad4qpg-a'; // Reemplaza con el "Nombre de host" de Render
$dbname = 'gestion_cobros';               // Reemplaza con el "Base de datos" de Render
$username = 'gestion_cobros_user';        // Reemplaza con el "Nombre de usuario" de Render
$password = 'HFXdvU5CkBNTJkgbZjesELr7C7Wk3O6A'; // Reemplaza con la "Contraseña" de Render
$port = 5432;                         // Reemplaza con el "Puerto" de Render

try {
    // Crear la conexión a la base de datos
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    // Configurar el modo de error de PDO para excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "Conexión exitosa a PostgreSQL.";
} catch (PDOException $e) {
    // Manejo de errores en caso de falla de conexión
    die("Error de conexión: " . $e->getMessage());
}
?>
