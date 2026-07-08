<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Reseñas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; 
            background: linear-gradient(135deg, #fdf6ec 0%, #f5e6d3 100%);
            min-height: 100vh;
            color: #4a3728;
        }
        
        /* --- BARRA DE NAVEGACIÓN CON CRUD --- */
        .navbar { 
            background: linear-gradient(135deg, #8b5e3c 0%, #6b4226 100%);
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 16px 40px; 
            box-shadow: 0 4px 20px rgba(107, 66, 38, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid #c9a88b;
        }
        .navbar-brand { 
            font-size: 1.4rem; 
            font-weight: 700; 
            color: #fdf0d5; 
            text-decoration: none; 
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .navbar-brand span { color: #f4c9a0; }
        .navbar-menu { 
            display: flex; 
            gap: 6px; 
            align-items: center;
            flex-wrap: wrap;
        }
        .navbar-item { 
            color: #fdf0d5; 
            text-decoration: none; 
            padding: 10px 20px; 
            border-radius: 25px; 
            font-weight: 600; 
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .navbar-item:hover { 
            background: rgba(255, 215, 175, 0.25); 
            transform: translateY(-2px);
        }
        .navbar-item.active { 
            background: #a67c52; 
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .navbar-item .badge {
            background: rgba(255,215,175,0.2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 700;
        }
        .navbar-item .badge.create { background: #2d7d46; color: #fff; }
        .navbar-item .badge.read { background: #2a6f8f; color: #fff; }
        .navbar-item .badge.update { background: #b8860b; color: #fff; }
        .navbar-item .badge.delete { background: #b94a4a; color: #fff; }

        /* --- CONTENEDOR --- */
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 40px 30px 80px;
        }
        
        h1 { 
            font-size: 2.4rem; 
            color: #4a3728; 
            font-weight: 800;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 1.1rem;
            color: #8b6b50;
            margin-bottom: 35px;
            border-left: 4px solid #c9a88b;
            padding-left: 20px;
        }
        
        /* --- TARJETAS CRUD --- */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .card {
            background: rgba(255, 248, 240, 0.9);
            backdrop-filter: blur(10px);
            padding: 25px 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.12);
            border: 1px solid rgba(255, 215, 175, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            color: #4a3728;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(107, 66, 38, 0.2);
        }
        .card .icon { font-size: 3rem; display: block; margin-bottom: 10px; }
        .card h3 { font-size: 1.2rem; margin-bottom: 6px; }
        .card p { font-size: 0.85rem; color: #8b6b50; line-height: 1.4; }
        .card.create { border-top: 4px solid #2d7d46; }
        .card.read { border-top: 4px solid #2a6f8f; }
        .card.update { border-top: 4px solid #b8860b; }
        .card.delete { border-top: 4px solid #b94a4a; }
        .card.presentacion { border-top: 4px solid #8b5e3c; }

        /* --- SECCIÓN DE INTEGRANTES --- */
        .integrantes {
            background: rgba(255, 248, 240, 0.85);
            backdrop-filter: blur(10px);
            padding: 25px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.12);
            border: 1px solid rgba(255, 215, 175, 0.3);
        }
        .integrantes h3 {
            color: #6b4226;
            font-size: 1.2rem;
            margin-bottom: 12px;
        }
        .integrantes ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .integrantes ul li {
            background: linear-gradient(135deg, #fdf0d5, #f5e0cc);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 500;
        }

        /* --- TABLA DE RESEÑAS --- */
        .table-wrapper {
            background: rgba(255, 248, 240, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.12);
            border: 1px solid rgba(255, 215, 175, 0.3);
            overflow-x: auto;
            margin-top: 30px;
        }
        .table-wrapper h2 {
            color: #6b4226;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            border-radius: 14px;
            overflow: hidden;
        }
        th { 
            background: linear-gradient(135deg, #8b5e3c, #6b4226);
            color: #fdf0d5; 
            padding: 14px 18px; 
            text-align: left; 
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        td { 
            padding: 14px 18px; 
            border-bottom: 1px solid #f5e6d3;
            vertical-align: middle;
            background: #fffcf7;
            color: #4a3728;
            font-size: 0.95rem;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fdf6ec; }

        .stars { color: #f5b342; font-size: 1.1rem; letter-spacing: 1px; }
        .empty-message { text-align: center; padding: 30px; color: #8b6b50; }
        .empty-message span { font-size: 2.5rem; display: block; margin-bottom: 8px; }

        .btn { 
            padding: 6px 14px; 
            border: none; 
            border-radius: 30px; 
            cursor: pointer; 
            text-decoration: none; 
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .btn-edit { 
            background: linear-gradient(135deg, #d4a84a, #b8912e);
            color: white;
        }
        .btn-edit:hover { transform: translateY(-2px); }
        .btn-delete { 
            background: linear-gradient(135deg, #c95a5a, #a84444);
            color: white;
        }
        .btn-delete:hover { transform: translateY(-2px); }
        .action-buttons { display: flex; gap: 6px; flex-wrap: wrap; }

        /* --- PRESENTACIÓN (CRUD TABLE) --- */
        .crud-table-wrapper {
            background: rgba(255, 248, 240, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 5px;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.12);
            border: 1px solid rgba(255, 215, 175, 0.3);
            overflow: hidden;
            margin-top: 20px;
        }
        .crud-table-wrapper table { margin: 0; }
        .crud-table-wrapper th {
            background: linear-gradient(135deg, #8b5e3c, #6b4226);
        }
        .letra { font-weight: 700; font-size: 1.05rem; }
        .create { color: #2d7d46; }
        .read   { color: #2a6f8f; }
        .update { color: #b8860b; }
        .delete { color: #b94a4a; }
        .badge-op {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-left: 6px;
        }
        .badge-create { background: #e6f5ed; color: #2d7d46; }
        .badge-read { background: #e3f0f7; color: #2a6f8f; }
        .badge-update { background: #fdf0d5; color: #b8860b; }
        .badge-delete { background: #fce8e8; color: #b94a4a; }
        .crud-icon { font-size: 1.3rem; margin-right: 4px; }

        /* --- IMAGEN MOCKUP --- */
        .imagen-app { 
            display: block; 
            max-width: 100%; 
            height: auto; 
            margin: 20px auto 5px; 
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.15);
            border: 3px solid rgba(255, 215, 175, 0.4);
            transition: transform 0.4s ease;
        }
        .imagen-app:hover {
            transform: scale(1.01);
        }

        /* --- FOOTER --- */
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #a68b78;
            border-top: 2px solid #f5e6d3;
            padding-top: 30px;
        }

        /* --- FORMULARIO --- */
        .form-card {
            background: rgba(255, 248, 240, 0.9);
            backdrop-filter: blur(10px);
            padding: 35px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.12);
            border: 1px solid rgba(255, 215, 175, 0.3);
            max-width: 700px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: 600; 
            color: #6b4226;
        }
        .form-group input, 
        .form-group textarea, 
        .form-group select { 
            width: 100%; 
            padding: 12px 16px; 
            border: 2px solid #f5e6d3; 
            border-radius: 12px; 
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: #fffcf7;
            color: #4a3728;
        }
        .form-group input:focus, 
        .form-group textarea:focus, 
        .form-group select:focus {
            outline: none;
            border-color: #c9a88b;
            box-shadow: 0 0 0 4px rgba(201, 168, 139, 0.15);
        }
        .form-group textarea { resize: vertical; min-height: 120px; }

        .btn-submit { 
            padding: 12px 32px; 
            border: none; 
            border-radius: 50px; 
            cursor: pointer; 
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #3a7d5e, #2a5f47);
            color: white; 
            box-shadow: 0 4px 12px rgba(42, 95, 71, 0.25);
        }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-warning { 
            background: linear-gradient(135deg, #b8860b, #8b6508);
            color: white; 
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.25);
        }
        .btn-warning:hover { transform: translateY(-2px); }
        .btn-danger { 
            background: linear-gradient(135deg, #c95a5a, #a84444);
            color: white; 
            box-shadow: 0 4px 12px rgba(168, 68, 68, 0.25);
        }
        .btn-danger:hover { transform: translateY(-2px); }
        .btn-secondary {
            background: #a0b8ae;
            color: white;
        }
        .btn-secondary:hover { background: #8aa69a; transform: translateY(-2px); }
        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .success-msg {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .success-msg.create { background: #e6f5ed; color: #2d7d46; border-color: #2d7d46; }
        .success-msg.update { background: #fdf0d5; color: #b8860b; border-color: #b8860b; }
        .success-msg.delete { background: #fce8e8; color: #b94a4a; border-color: #b94a4a; }

        .warning-box {
            background: #fce8e8;
            color: #b94a4a;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 15px 0;
            font-weight: 500;
            border-left: 4px solid #b94a4a;
        }
        .book-info {
            background: rgba(255, 215, 175, 0.15);
            padding: 15px 20px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: left;
        }
        .book-info p { margin: 5px 0; }

        /* --- MENSAJE DE REDIRECCIÓN --- */
        .redirect-message {
            background: rgba(255, 248, 240, 0.85);
            backdrop-filter: blur(10px);
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(107, 66, 38, 0.12);
            border: 1px solid rgba(255, 215, 175, 0.3);
            text-align: center;
            max-width: 600px;
            margin: 40px auto;
        }
        .redirect-message .icon {
            font-size: 4rem;
            display: block;
            margin-bottom: 15px;
        }
        .redirect-message h2 {
            color: #6b4226;
            margin-bottom: 10px;
        }
        .redirect-message p {
            color: #8b6b50;
            margin-bottom: 20px;
            font-size: 1.05rem;
            line-height: 1.6;
        }
        .redirect-message .btn-submit {
            display: inline-block;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 12px; padding: 15px 20px; }
            .navbar-menu { justify-content: center; }
            .container { padding: 25px 16px; }
            h1 { font-size: 1.8rem; }
            .grid-cards { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
            .card { padding: 20px 15px; }
            .card .icon { font-size: 2.5rem; }
            th, td { padding: 10px 12px; font-size: 0.85rem; }
            .form-card { padding: 25px 20px; }
            .redirect-message { padding: 30px 20px; margin: 20px 10px; }
        }
        @media (max-width: 480px) {
            .navbar-brand { font-size: 1.1rem; }
            .navbar-item { padding: 6px 12px; font-size: 0.8rem; }
            .navbar-item .badge { display: none; }
            h1 { font-size: 1.4rem; }
            .grid-cards { grid-template-columns: 1fr 1fr; }
            th, td { padding: 8px 8px; font-size: 0.75rem; }
            .stars { font-size: 0.9rem; }
            .crud-icon { font-size: 1rem; }
            .badge-op { font-size: 0.55rem; padding: 1px 8px; }
            .redirect-message { padding: 25px 15px; }
            .redirect-message .icon { font-size: 3rem; }
            .redirect-message h2 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">📚 <span>Biblioteca</span> de Reseñas</a>
        <div class="navbar-menu">
            <a href="index.php?section=presentacion" class="navbar-item <?php echo (!isset($_GET['section']) || $_GET['section'] == 'presentacion') ? 'active' : ''; ?>">📄 Presentación</a>
            <a href="index.php?section=create" class="navbar-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'create') ? 'active' : ''; ?>">➕ Crear <span class="badge create">C</span></a>
            <a href="index.php?section=read" class="navbar-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'read') ? 'active' : ''; ?>">📖 Ver <span class="badge read">R</span></a>
            <a href="index.php?section=update" class="navbar-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'update') ? 'active' : ''; ?>">✏️ Editar <span class="badge update">U</span></a>
            <a href="index.php?section=delete" class="navbar-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'delete') ? 'active' : ''; ?>">🗑️ Eliminar <span class="badge delete">D</span></a>
        </div>
    </nav>

    <div class="container">
        <?php
        // Incluir la lógica de la aplicación
        require_once 'app.php';
        
        // Determinar qué sección mostrar
        $section = isset($_GET['section']) ? $_GET['section'] : 'presentacion';
        
        switch($section) {
            case 'presentacion':
                // SECCIÓN DE PRESENTACIÓN
                ?>
                <h1>📚 Biblioteca de Reseñas de Libros</h1>
                <div class="subtitle">Comparte tu pasión por la lectura — reseñas, calificaciones y recomendaciones</div>

                <div class="integrantes">
                    <h3>👥 Integrantes del Grupo</h3>
                    <ul>
                        <li><strong>Bárbara Vargas</strong></li>
                        <li><strong>Milaray Montecino</strong></li>
                        <li><strong>Gabriela Cancino</strong></li>
                    </ul>
                </div>

                <div style="background:rgba(255,248,240,0.85);padding:25px 30px;border-radius:20px;margin-bottom:30px;box-shadow:0 8px 32px rgba(107,66,38,0.12);border:1px solid rgba(255,215,175,0.3);">
                    <h3 style="color:#6b4226;margin-bottom:15px;">📖 Descripción de la Aplicación</h3>
                    <p style="line-height:1.8;color:#5a4a3a;">
                        <strong>Biblioteca de Reseñas de Libros</strong> es una aplicación web que permite a los usuarios
                        registrar y gestionar reseñas de libros. Por cada libro se almacena el título, el nombre del autor,
                        una calificación de 1 a 5 estrellas y un comentario personal sobre la lectura.
                    </p>
                    <p style="line-height:1.8;color:#5a4a3a;margin-top:12px;">
                        La aplicación utiliza <strong>PHP</strong> con <strong>PDO</strong> para la conexión a una base de datos
                        <strong>MySQL</strong>, y está adaptada para su ejecución en el entorno en la nube <strong>GitHub Codespaces</strong>.
                        Los datos se persisten en la tabla <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">resenas</code> y se muestran ordenados por fecha de creación.
                    </p>
                </div>

                <h2 style="color:#6b4226;font-size:1.8rem;margin-top:20px;margin-bottom:20px;">⚙️ Operaciones CRUD</h2>
                <div class="crud-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:18%;">Operación</th>
                                <th style="width:42%;">Descripción</th>
                                <th style="width:40%;">Cómo se implementa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="crud-icon">➕</span>
                                    <span class="letra create">CREATE</span>
                                    <span class="badge-op badge-create">Crear</span>
                                </td>
                                <td>El usuario completa el formulario con título, autor, calificación y comentario, y publica una nueva reseña.</td>
                                <td>Formulario POST con <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">action=create</code> en <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">app.php</code>. Se ejecuta <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">INSERT INTO resenas</code> usando un prepared statement.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="crud-icon">📖</span>
                                    <span class="letra read">READ</span>
                                    <span class="badge-op badge-read">Leer</span>
                                </td>
                                <td>Se muestran todas las reseñas registradas en una tabla, ordenadas de la más reciente a la más antigua.</td>
                                <td><code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">SELECT * FROM resenas ORDER BY fecha_creacion DESC</code>. Los resultados se recorren con <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">foreach</code> en el HTML.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="crud-icon">✏️</span>
                                    <span class="letra update">UPDATE</span>
                                    <span class="badge-op badge-update">Modificar</span>
                                </td>
                                <td>El usuario hace clic en "Editar" y el formulario se precarga con los datos actuales del registro para modificarlos.</td>
                                <td>GET con <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">edit=id</code> carga el registro. Formulario POST con <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">action=update</code> ejecuta <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">UPDATE resenas ... WHERE id = ?</code>.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="crud-icon">🗑️</span>
                                    <span class="letra delete">DELETE</span>
                                    <span class="badge-op badge-delete">Borrar</span>
                                </td>
                                <td>El usuario hace clic en "Borrar", confirma la acción en una ventana emergente y el registro se elimina permanentemente.</td>
                                <td>Enlace GET con <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">delete=id</code>. Se ejecuta <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">DELETE FROM resenas WHERE id = ?</code> y se redirige a <code style="background:#f5e6d3;padding:3px 10px;border-radius:6px;color:#8b5e3c;font-weight:600;">app.php</code>.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="background:rgba(255,248,240,0.85);padding:25px 30px;border-radius:20px;margin-top:30px;box-shadow:0 8px 32px rgba(107,66,38,0.12);border:1px solid rgba(255,215,175,0.3);">
                    <h3 style="color:#6b4226;margin-bottom:15px;">🖥️ Interfaz de la Aplicación Web</h3>
                    <p style="color:#5a4a3a;margin-bottom:15px;">
                        A continuación se muestra la interfaz principal de la aplicación web
                        Biblioteca de Reseñas de Libros.
                    </p>
                    <img src="mockup.png" alt="Interfaz de la aplicación web" class="imagen-app">
                </div>

                <!-- Acceso rápido al CRUD -->
                <div class="grid-cards" style="margin-top:30px;">
                    <a href="index.php?section=create" class="card create">
                        <span class="icon">➕</span>
                        <h3>Ir a Crear</h3>
                        <p>Añade una nueva reseña</p>
                    </a>
                    <a href="index.php?section=read" class="card read">
                        <span class="icon">📖</span>
                        <h3>Ir a Ver</h3>
                        <p>Visualiza todas las reseñas</p>
                    </a>
                </div>
                <?php
                break;
                
            case 'create':
                // FORMULARIO DE CREACIÓN
                ?>
                <h1>➕ Crear Nueva Reseña</h1>
                <div class="subtitle">Completa el formulario para añadir un libro a tu biblioteca</div>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="success-msg create">✅ ¡Reseña creada exitosamente!</div>
                <?php endif; ?>

                <div class="form-card">
                    <form action="app.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-group">
                            <label>📖 Título del Libro</label>
                            <input type="text" name="titulo_libro" placeholder="Ej: Don Quijote de la Mancha" required>
                        </div>
                        <div class="form-group">
                            <label>✍️ Autor</label>
                            <input type="text" name="autor" placeholder="Ej: Miguel de Cervantes" required>
                        </div>
                        <div class="form-group">
                            <label>⭐ Calificación (1 a 5)</label>
                            <select name="calificacion">
                                <option value="5">5 ⭐⭐⭐⭐⭐</option>
                                <option value="4">4 ⭐⭐⭐⭐</option>
                                <option value="3">3 ⭐⭐⭐</option>
                                <option value="2">2 ⭐⭐</option>
                                <option value="1">1 ⭐</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>💬 Comentario / Reseña</label>
                            <textarea name="comentario" rows="4" placeholder="¿Qué te pareció el libro? Comparte tu opinión..." required></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn-submit btn-primary">📤 Publicar Reseña</button>
                            <a href="index.php?section=presentacion" class="btn-submit btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
                <?php
                break;
                
            case 'read':
                // LISTA DE RESEÑAS
                ?>
                <h1>📖 Lista de Reseñas</h1>
                <div class="subtitle">Todas las reseñas registradas en la biblioteca</div>
                
                <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                    <div class="success-msg delete">🗑️ Reseña eliminada exitosamente</div>
                <?php endif; ?>

                <div class="table-wrapper">
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
                            <?php 
                            $stmt = $pdo->query('SELECT * FROM resenas ORDER BY fecha_creacion DESC');
                            $resenas = $stmt->fetchAll();
                            if (count($resenas) == 0): 
                            ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-message">
                                            <span>📭</span>
                                            No hay reseñas aún. ¡Ve a <strong>Crear</strong> para agregar una!
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($resenas as $r): ?>
                                    <tr>
                                        <td><strong>#<?php echo $r['id']; ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($r['titulo_libro']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($r['autor']); ?></td>
                                        <td><span class="stars"><?php echo str_repeat('⭐', $r['calificacion']); ?></span></td>
                                        <td><?php echo nl2br(htmlspecialchars($r['comentario'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($r['fecha_creacion'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="index.php?section=update&id=<?php echo $r['id']; ?>" class="btn btn-edit">✏️ Editar</a>
                                                <a href="index.php?section=delete&id=<?php echo $r['id']; ?>" class="btn btn-delete">🗑️ Borrar</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                break;
                
            case 'update':
                // FORMULARIO DE EDICIÓN
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                
                // Si no hay ID, mostrar mensaje y redirigir
                if ($id == 0) {
                    ?>
                    <div class="redirect-message">
                        <span class="icon">ℹ️</span>
                        <h2>Selecciona una reseña para editar</h2>
                        <p>Ve a la lista de reseñas y haz clic en el botón <strong>"Editar"</strong> de la reseña que deseas modificar.</p>
                        <a href="index.php?section=read" class="btn-submit btn-primary">📖 Ir a la lista de reseñas</a>
                    </div>
                    <?php
                    break;
                }
                
                $stmt = $pdo->prepare('SELECT * FROM resenas WHERE id = ?');
                $stmt->execute([$id]);
                $resena = $stmt->fetch();
                
                if (!$resena) {
                    ?>
                    <div class="redirect-message">
                        <span class="icon">❌</span>
                        <h2>Reseña no encontrada</h2>
                        <p>La reseña que intentas editar no existe o ha sido eliminada.</p>
                        <a href="index.php?section=read" class="btn-submit btn-primary">📖 Ir a la lista de reseñas</a>
                    </div>
                    <?php
                    break;
                }
                ?>
                <h1>✏️ Editar Reseña</h1>
                <div class="subtitle">Modifica los datos de la reseña seleccionada</div>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="success-msg update">✅ ¡Reseña actualizada exitosamente!</div>
                <?php endif; ?>

                <div class="form-card">
                    <form action="app.php" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $resena['id']; ?>">
                        
                        <div class="form-group">
                            <label>📖 Título del Libro</label>
                            <input type="text" name="titulo_libro" value="<?php echo htmlspecialchars($resena['titulo_libro']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>✍️ Autor</label>
                            <input type="text" name="autor" value="<?php echo htmlspecialchars($resena['autor']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>⭐ Calificación (1 a 5)</label>
                            <select name="calificacion">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $resena['calificacion'] == $i ? 'selected' : ''; ?>>
                                        <?php echo $i; ?> <?php echo str_repeat('⭐', $i); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>💬 Comentario / Reseña</label>
                            <textarea name="comentario" rows="4" required><?php echo htmlspecialchars($resena['comentario']); ?></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn-submit btn-warning">💾 Guardar Cambios</button>
                            <a href="index.php?section=read" class="btn-submit btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
                <?php
                break;
                
            case 'delete':
                // CONFIRMACIÓN DE ELIMINACIÓN
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                
                // Si no hay ID, mostrar mensaje y redirigir
                if ($id == 0) {
                    ?>
                    <div class="redirect-message">
                        <span class="icon">ℹ️</span>
                        <h2>Selecciona una reseña para eliminar</h2>
                        <p>Ve a la lista de reseñas y haz clic en el botón <strong>"Borrar"</strong> de la reseña que deseas eliminar.</p>
                        <a href="index.php?section=read" class="btn-submit btn-primary">📖 Ir a la lista de reseñas</a>
                    </div>
                    <?php
                    break;
                }
                
                $stmt = $pdo->prepare('SELECT * FROM resenas WHERE id = ?');
                $stmt->execute([$id]);
                $resena = $stmt->fetch();
                
                if (!$resena) {
                    ?>
                    <div class="redirect-message">
                        <span class="icon">❌</span>
                        <h2>Reseña no encontrada</h2>
                        <p>La reseña que intentas eliminar no existe o ya ha sido eliminada.</p>
                        <a href="index.php?section=read" class="btn-submit btn-primary">📖 Ir a la lista de reseñas</a>
                    </div>
                    <?php
                    break;
                }
                ?>
                <div style="max-width:600px;margin:0 auto;">
                    <div style="background:rgba(255,248,240,0.9);backdrop-filter:blur(10px);padding:35px 40px;border-radius:20px;box-shadow:0 8px 32px rgba(107,66,38,0.12);border:1px solid rgba(255,215,175,0.3);text-align:center;">
                        <span style="font-size:4rem;display:block;margin-bottom:10px;">⚠️</span>
                        <h1 style="font-size:2rem;color:#b94a4a;margin-bottom:8px;">Eliminar Reseña</h1>
                        <div style="color:#8b6b50;margin-bottom:20px;">¿Estás seguro de que deseas eliminar esta reseña?</div>
                        
                        <div class="book-info">
                            <p><strong>📖 Título:</strong> <?php echo htmlspecialchars($resena['titulo_libro']); ?></p>
                            <p><strong>✍️ Autor:</strong> <?php echo htmlspecialchars($resena['autor']); ?></p>
                            <p><strong>⭐ Calificación:</strong> <?php echo str_repeat('⭐', $resena['calificacion']); ?></p>
                        </div>
                        
                        <div class="warning-box">
                            ⚠️ Esta acción no se puede deshacer. El registro será eliminado permanentemente.
                        </div>
                        
                        <form action="app.php" method="POST">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $resena['id']; ?>">
                            
                            <div class="btn-group" style="justify-content:center;">
                                <button type="submit" class="btn-submit btn-danger">🗑️ Sí, Eliminar</button>
                                <a href="index.php?section=read" class="btn-submit btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                break;
        }
        ?>
        
        <div class="footer">
            📚 Biblioteca de Reseñas &middot; 
        </div>
    </div>

</body>
</html>