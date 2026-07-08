<?php
// logout.php
session_start();

// Borramos todas las variables de la sesión
$_SESSION = array();

// Destruimos la sesión por completo
session_destroy();

// Redirigimos al usuario a la página de login
header("Location: login.php");
exit();
?>