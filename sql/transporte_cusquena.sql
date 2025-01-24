-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS la_cusquena;
USE la_cusquena;

-- Tabla de conductores
CREATE TABLE IF NOT EXISTS conductores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    vehiculo VARCHAR(100) NOT NULL,
    turno BOOLEAN DEFAULT TRUE,
    deuda BOOLEAN DEFAULT FALSE,
    detalles TEXT
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    precio_compra DECIMAL(10, 2) NOT NULL,
    precio_venta DECIMAL(10, 2) NOT NULL,
    primer_ingreso INT NOT NULL,
    segundo_ingreso INT NOT NULL,
    restantes INT NOT NULL,
    vendidos INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL
);

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insertar datos de prueba en la tabla de conductores
INSERT INTO conductores (nombre, vehiculo, turno, deuda, detalles) VALUES
('Juan Pérez', 'Camión Volvo', TRUE, FALSE, 'Conductor experimentado'),
('Carlos Gómez', 'Camión Scania', FALSE, TRUE, 'Debe 2 cuotas de aceite'),
('Ana López', 'Camión Mercedes', TRUE, FALSE, 'Nuevo en la empresa');

-- Insertar datos de prueba en la tabla de productos
INSERT INTO productos (nombre, categoria, precio_compra, precio_venta, primer_ingreso, segundo_ingreso, restantes, vendidos, total) VALUES
('Aceite 10W40', 'Aceites', 15.00, 25.00, 100, 50, 120, 30, 750.00),
('Filtro de Aire', 'Filtros', 8.00, 15.00, 200, 100, 250, 50, 750.00),
('Filtro de Gasolina', 'Filtros', 10.00, 20.00, 150, 75, 200, 25, 500.00);

-- Insertar datos de prueba en la tabla de usuarios
INSERT INTO usuarios (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- Contraseña: 'password'