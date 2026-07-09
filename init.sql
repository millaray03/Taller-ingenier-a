CREATE TABLE IF NOT EXISTS resenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo_libro VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    calificacion INT NOT NULL,
    comentario TEXT NOT NULL,
    creado_por VARCHAR(50) NOT NULL DEFAULT 'Anonimo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para el Login de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Log / Bitácora de eventos del sistema
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    detalle TEXT NOT NULL,
    ip_host_cliente VARCHAR(100) NOT NULL
);
