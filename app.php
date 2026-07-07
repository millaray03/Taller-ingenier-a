<?php
// Configuración de la conexión local para GitHub Codespaces
$host = '127.0.0.1'; 
$db   = 'biblioteca_db'; 
$user = 'admin'; 
$pass = '1234';     
$charset = 'utf8mb4';

// 1. Conexión inicial al servidor MySQL (para asegurar que exista la BD)
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
    
    // INSTALACIÓN AUTOMÁTICA: Si la tabla no existe, importa 'init.sql'
    $tabla_existe = $pdo->query("SHOW TABLES LIKE 'resenas'")->rowCount();
    if ($tabla_existe == 0 && file_exists('init.sql')) {
        $sql_script = file_get_contents('init.sql');
        $pdo->exec($sql_script);
    }
} catch (\PDOException $e) {
    echo "Fallo de conexión a la Base de Datos: " . $e->getMessage();
    exit;
}

// --- LÓGICA CRUD ---
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $stmt = $pdo->prepare('INSERT INTO resenas (titulo_libro, autor, calificacion, comentario) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_POST['titulo_libro'], $_POST['autor'], $_POST['calificacion'], $_POST['comentario']]);
    header("Location: app.php");
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $stmt = $pdo->prepare('UPDATE resenas SET titulo_libro = ?, autor = ?, calificacion = ?, comentario = ? WHERE id = ?');
    $stmt->execute([$_POST['titulo_libro'], $_POST['autor'], $_POST['calificacion'], $_POST['comentario'], $_POST['id']]);
    header("Location: app.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM resenas WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    header("Location: app.php");
    exit;
}

$stmt = $pdo->query('SELECT * FROM resenas ORDER BY fecha_creacion DESC');
$resenas = $stmt->fetchAll();

$resena_a_editar = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM resenas WHERE id = ?');
    $stmt->execute([$_GET['edit']]);
    $resena_a_editar = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Reseñas — Calificación</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; color: #333; }
        
        /* --- BARRA DE NAVEGACIÓN REAL --- */
        .navbar { background-color: #28a745; display: flex; justify-content: space-between; align-items: center; padding: 10px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-size: 1.3rem; font-weight: bold; color: white; text-decoration: none; }
        .navbar-menu { display: flex; gap: 15px; }
        .navbar-item { color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; transition: background 0.3s; }
        .navbar-item:hover { background-color: #218838; }
        .navbar-item.active { background-color: #1e7e34; }

        .container { padding: 40px; }
        h1, h2 { color: #333; margin-top: 0; }
        hr { border: 0; height: 1px; background: #ccc; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: white; }
        
        .form-box { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 500px; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        
        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; color: white; display: inline-block; }
        .btn-submit { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        .btn-cancel { background-color: #6c757d; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">💻 Sistema de Calificaciones</a>
        <div class="navbar-menu">
            <a href="index.php" class="navbar-item">📄 Presentación</a>
            <a href="app.php" class="navbar-item active">💻 Calificación</a>
        </div>
    </nav>

    <div class="container">
        <h1>💻 Menú Principal: Sistema de Calificaciones</h1>
        <hr>

        <div class="form-box">
            <?php if ($resena_a_editar): ?>
                <h2>Modificar Reseña </h2>
                <form action="app.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $resena_a_editar['id']; ?>">
                    
                    <div class="form-group">
                        <label>Título del Libro:</label>
                        <input type="text" name="titulo_libro" value="<?php echo htmlspecialchars($resena_a_editar['titulo_libro']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Autor:</label>
                        <input type="text" name="autor" value="<?php echo htmlspecialchars($resena_a_editar['autor']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Calificación (1 a 5):</label>
                        <select name="calificacion">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $resena_a_editar['calificacion'] == $i ? 'selected' : ''; ?>><?php echo $i; ?> ⭐</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comentario / Reseña:</label>
                        <textarea name="comentario" rows="4" required><?php echo htmlspecialchars($resena_a_editar['comentario']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit">Guardar Cambios</button>
                    <a href="app.php" class="btn btn-cancel">Cancelar</a>
                </form>
            <?php else: ?>
                <h2>Añadir Nueva Reseña </h2>
                <form action="app.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label>Título del Libro:</label>
                        <input type="text" name="titulo_libro" placeholder="Ej: Don Quijote" required>
                    </div>
                    <div class="form-group">
                        <label>Autor:</label>
                        <input type="text" name="autor" placeholder="Ej: Miguel de Cervantes" required>
                    </div>
                    <div class="form-group">
                        <label>Calificación (1 a 5):</label>
                        <select name="calificacion">
                            <option value="5">5 ⭐⭐⭐⭐⭐</option>
                            <option value="4">4 ⭐⭐⭐⭐</option>
                            <option value="3">3 ⭐⭐⭐</option>
                            <option value="2">2 ⭐⭐</option>
                            <option value="1">1 ⭐</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comentario / Reseña:</label>
                        <textarea name="comentario" rows="4" placeholder="Escribe aquí qué te pareció el libro..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit">Publicar Reseña</button>
                </form>
            <?php endif; ?>
        </div>

        <h2>Reseñas Registradas </h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libro</th>
                    <th>Autor</th>
                    <th>Calificación</th>
                    <th>Comentario</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($resenas) == 0): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No hay reseñas aún. ¡Sé el primero en agregar una!</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resenas as $r): ?>
                        <tr>
                            <td><?php echo $r['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($r['titulo_libro']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['autor']); ?></td>
                            <td><?php echo str_repeat('⭐', $r['calificacion']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($r['comentario'])); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($r['fecha_creacion'])); ?></td>
                            <td>
                                <a href="app.php?edit=<?php echo $r['id']; ?>" class="btn btn-edit">Editar </a>
                                <a href="app.php?delete=<?php echo $r['id']; ?>" onclick="return confirm('¿Seguro que deseas borrar esta reseña?')" class="btn btn-delete">Borrar </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>