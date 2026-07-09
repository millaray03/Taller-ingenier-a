<?php
// logout.php
session_start();
require_once 'app.php';

if(isset($_SESSION['usuario_name'])) {
    registrar_log($pdo, 'Cierre de sesión', "Usuario '" . $_SESSION['usuario_name'] . "' cerró sesión");
}

// Borramos todas las variables de la sesión
$_SESSION = array();

// Destruimos la sesión por completo
session_destroy();

// Redirigimos al usuario a la página de login
header("Location: login.php");
exit();
?>