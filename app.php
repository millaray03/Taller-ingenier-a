<?php
// Configuración de la conexión
$host = 'db'; 
$db   = 'biblioteca_db'; 
$user = 'admin'; 
$pass = '1234';     
$charset = 'utf8mb4';

// 1. Conexión inicial al servidor MySQL
try {
    $pdo_init = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db`");
} catch (\PDOException $e) {
    echo "Error al inicializar el servidor MySQL: " . $e->getMessage();
    exit;
}

// 2. Conexión oficial a la base de datos
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Instalación automática de la tabla
    $tabla_existe = $pdo->query("SHOW TABLES LIKE 'resenas'")->rowCount();
    if ($tabla_existe == 0 && file_exists('init.sql')) {
        $sql_script = file_get_contents('init.sql');
        $pdo->exec($sql_script);
    }
} catch (\PDOException $e) {
    echo "Fallo de conexión a la Base de Datos: " . $e->getMessage();
    exit;
}

// --- PROCESAR ACCIONES CRUD ---

// CREATE
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $stmt = $pdo->prepare('INSERT INTO resenas (titulo_libro, autor, calificacion, comentario) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_POST['titulo_libro'], $_POST['autor'], $_POST['calificacion'], $_POST['comentario']]);
    header("Location: index.php?section=create&success=1");
    exit;
}

// UPDATE
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $stmt = $pdo->prepare('UPDATE resenas SET titulo_libro = ?, autor = ?, calificacion = ?, comentario = ? WHERE id = ?');
    $stmt->execute([$_POST['titulo_libro'], $_POST['autor'], $_POST['calificacion'], $_POST['comentario'], $_POST['id']]);
    header("Location: index.php?section=update&id=" . $_POST['id'] . "&success=1");
    exit;
}

// DELETE
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $stmt = $pdo->prepare('DELETE FROM resenas WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    header("Location: index.php?section=read&deleted=1");
    exit;
}
?>