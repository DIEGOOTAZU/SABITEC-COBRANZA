<?php
// Iniciar la sesión antes de cualquier salida
session_start();

// Incluir la conexión a la base de datos
require_once 'config/db.php';

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Validar credenciales (texto plano)
            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirigir al index
                header("Location: index.php");
                exit;
            } else {
                $error = "Credenciales incorrectas.";
            }
        } catch (PDOException $e) {
            $error = "Error de conexión: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 400px;
            width: 100%;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Ingrese su usuario" required>
            </div>
            <div class="form-group mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</body>
</html>
