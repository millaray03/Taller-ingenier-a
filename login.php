<?php
// login.php
require_once 'app.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Buscamos el usuario
        $sql = "SELECT * FROM usuarios WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔑 Comparamos la contraseña usando verificación de hash descifrado
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Guardamos datos en la sesión para que el compañero del LOG sepa quién es
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_name'] = $usuario['username'];

            registrar_log($pdo, 'Inicio de sesión', "Usuario '" . $usuario['username'] . "' inició sesión");
            
            // Redireccionamos a tu página principal
            header("Location: index.php"); 
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Por favor, llena todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Biblioteca de Reseñas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7ebd9; /* Fondo crema uniforme */
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
            background-color: #795548; /* Café institucional de tu barra superior */
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
            background-color: #4caf50; /* Verde igual al botón "+ Crear" de tu captura */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        button:hover {
            background-color: #388e3c; /* Verde oscuro al pasar el cursor */
        }
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            font-size: 0.9em;
        }
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
        <div class="header-title">📚 Biblioteca de Reseñas</div>
        
        <?php if(!empty($error)): ?>
            <div class="error-box"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <div class="link-footer">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>