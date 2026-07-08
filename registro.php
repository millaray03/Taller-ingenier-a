<?php
// registro.php
require_once 'app.php';

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // 🔐 Encriptación segura con Bcrypt
        $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO usuarios (username, password) VALUES (:username, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $password_encriptada
            ]);
            $mensaje = "¡Usuario registrado con éxito! Ya puedes iniciar sesión.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "Error: El nombre de usuario ya está registrado.";
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "Por favor, llena todos los campos.";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Biblioteca de Reseñas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7ebd9; /* Fondo crema idéntico a tu web */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .header-title {
            background-color: #795548; /* Café principal */
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 1.4em;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #fafafa;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #8d6e63; /* Café claro para botones */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        button:hover {
            background-color: #5d4037; /* Café oscuro al pasar el cursor */
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .link-footer {
            margin-top: 20px;
            font-size: 0.9em;
        }
        .link-footer a {
            color: #795548;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header-title">📚 Crear Cuenta</div>
        
        <?php if(!empty($mensaje)): ?>
            <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST" action="registro.php">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña nueva" required>
            <button type="submit">Registrar Integrante</button>
        </form>
        
        <div class="link-footer">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>