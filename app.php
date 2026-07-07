<?php
$host = '127.0.0.1'; 
$db   = 'biblioteca_db'; 
$user = 'admin'; 
$pass = '1234';     
$charset = 'utf8mb4';

try {
    $pdo_init = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db`");
} catch (\PDOException $e) {
    echo "Error al inicializar el servidor MySQL: " . $e->getMessage();
    exit;
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $tabla_existe = $pdo->query("SHOW TABLES LIKE 'resenas'")->rowCount();
    if ($tabla_existe == 0 && file_exists('init.sql')) {
        $sql_script = file_get_contents('init.sql');
        $pdo->exec($sql_script);
    }
} catch (\PDOException $e) {
    echo "Fallo de conexión a la Base de Datos: " . $e->getMessage();
    exit;
}

// Control de vistas por URL (Si no hay ninguna elegida por defecto va a "leer")
$vista_actual = isset($_GET['vista']) ? $_GET['vista'] : 'leer';

// Si viene la acción de editar por GET, forzamos la vista de modificar
if (isset($_GET['edit'])) {
    $vista_actual = 'modificar';
}

// LÓGICA DE PROCESAMIENTO CRUD
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $stmt = $pdo->prepare('INSERT INTO resenas (titulo_libro, autor, calificacion, comentario) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_POST['titulo_libro'], $_POST['autor'], $_POST['calificacion'], $_POST['comentario']]);
    header("Location: app.php?vista=leer");
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $stmt = $pdo->prepare('UPDATE resenas SET titulo_libro = ?, autor = ?, calificacion = ?, comentario = ? WHERE id = ?');
    $stmt->execute([$_POST['titulo_libro'], $_POST['autor'], $_POST['calificacion'], $_POST['comentario'], $_POST['id']]);
    header("Location: app.php?vista=leer");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM resenas WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    header("Location: app.php?vista=eliminar");
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
    <title>Biblioteca de Reseñas — Gestión</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f8f9fa; 
            color: #333; 
        }
        
        /* --- BARRA SUPERIOR NEGRA/AZUL --- */
        .navbar { 
            background-color: #1a1d20; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 14px 40px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-bottom: 3px solid #0056b3;
        }
        
        .navbar-brand { font-size: 1.3rem; font-weight: bold; color: white; text-decoration: none; }
        .navbar-menu { display: flex; gap: 10px; list-style: none; margin: 0; padding: 0; }
        
        .navbar-item { 
            color: #e2e8f0; 
            text-decoration: none; 
            padding: 8px 16px; 
            border-radius: 6px; 
            font-weight: 600; 
            font-size: 0.9rem;
            transition: all 0.2s ease; 
        }
        .navbar-item:hover { background-color: rgba(255, 255, 255, 0.1); color: white; }
        .navbar-item.active { background-color: #0056b3; color: white; }

        .nav-divider { width: 1px; height: 20px; background-color: rgba(255, 255, 255, 0.2); margin: 0 4px; }

        .container { padding: 40px; max-width: 1000px; margin: 0 auto; }
        h1 { color: #1e293b; font-weight: 700; margin-bottom: 25px; }
        
        /* --- TABLAS Y FORMULARIOS --- */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
        th, td { padding: 14px; text-align: left; }
        th { background-color: #1a1d20; color: white; font-weight: 600; }
        td { border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) { background-color: #f8fafc; }
        
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); max-width: 600px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #475569; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem; }
        
        .btn { padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; color: white; font-weight: 600; display: inline-block; font-size: 0.9rem; }
        .btn-submit { background-color: #2b8a3e; }
        .btn-edit { background-color: #e67e22; }
        .btn-delete { background-color: #c92a2a; }
        .btn-cancel { background-color: #6c757d; }
        
        .aviso { background-color: #e3f2fd; color: #0d47a1; padding: 15px; border-radius: 6px; font-weight: 500; margin-bottom: 15px; border-left: 4px solid #0d47a1; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">📚 Sistema de Reseñas</a>
        <div class="navbar-menu">
            <a href="index.php" class="navbar-item">📄 Presentación</a>
            <div class="nav-divider"></div>
            <a href="app.php?vista=crear" class="navbar-item <?php echo $vista_actual == 'crear' ? 'active' : ''; ?>">➕ Crear</a>
            <a href="app.php?vista=leer" class="navbar-item <?php echo $vista_actual == 'leer' ? 'active' : ''; ?>">📋 Leer</a>
            <a href="app.php?vista=modificar" class="navbar-item <?php echo $vista_actual == 'modificar' ? 'active' : ''; ?>">✏️ Modificar</a>
            <a href="app.php?vista=eliminar" class="navbar-item <?php echo $vista_actual == 'eliminar' ? 'active' : ''; ?>">❌ Eliminar</a>
        </div>
    </nav>

    <div class="container">

        <?php if ($vista_actual == 'crear'): ?>
            <h1>➕ Añadir Nueva Reseña</h1>
            <div class="form-box">
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
            </div>

        <?php elseif ($vista_actual == 'leer'): ?>
            <h1>📋 Reseñas Registradas (Modo Lectura)</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Libro</th>
                        <th>Autor</th>
                        <th>Calificación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resenas) == 0): ?>
                        <tr><td colspan="6" style="text-align: center;">No hay reseñas guardadas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resenas as $r): ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($r['titulo_libro']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['autor']); ?></td>
                                <td><?php echo str_repeat('⭐', $r['calificacion']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($r['comentario'])); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($r['fecha_creacion'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($vista_actual == 'modificar'): ?>
            <h1>✏️ Modificar Registros en MySQL</h1>
            
            <?php if ($resena_a_editar): ?>
                <div class="form-box">
                    <h2>Editando Registro ID #<?php echo $resena_a_editar['id']; ?></h2>
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
                        <button type="submit" class="btn btn-edit">Guardar Cambios</button>
                        <a href="app.php?vista=modificar" class="btn btn-cancel">Cancelar</a>
                    </form>
                </div>
            <?php else: ?>
                <div class="aviso">Selecciona de la lista qué reseña deseas modificar haciendo clic en el botón "Editar".</div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libro</th>
                            <th>Autor</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resenas as $r): ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($r['titulo_libro']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['autor']); ?></td>
                                <td>
                                    <a href="app.php?edit=<?php echo $r['id']; ?>" class="btn btn-edit">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($vista_actual == 'eliminar'): ?>
            <h1>❌ Eliminar Registros Permanentemente</h1>
            <div class="aviso" style="background-color: #fdf2f2; color: #9b1c1c; border-left-color: #9b1c1c;">Cuidado: Al presionar "Borrar" el dato se perderá de la base de datos MySQL.</div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Libro</th>
                        <th>Autor</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resenas) == 0): ?>
                        <tr><td colspan="4" style="text-align: center;">No hay registros para eliminar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resenas as $r): ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($r['titulo_libro']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['autor']); ?></td>
                                <td>
                                    <a href="app.php?delete=<?php echo $r['id']; ?>&vista=eliminar" onclick="return confirm('¿Seguro que deseas borrar esta reseña?')" class="btn btn-delete">Borrar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</body>
</html>