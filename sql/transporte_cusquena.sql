CREATE DATABASE IF NOT EXISTS la_cusquena;
USE la_cusquena;

-- Tabla: Categoria
CREATE TABLE Categoria (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100),
    estado ENUM('activo', 'inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Usuarios
CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL, 
    correo VARCHAR(100) NOT NULL UNIQUE,
    rol ENUM('admin', 'user') NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL
);

-- Tabla para recuperación de contraseñas
CREATE TABLE pwdreset (
    pwdResetId INT(11) PRIMARY KEY AUTO_INCREMENT,
    pwdResetEmail TEXT,
    pwdResetSelector TEXT,
    pwdResetToken LONGTEXT,
    pwdResetExpires DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla para historico de cambios
CREATE TABLE historico_cambios (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(255),
    accion VARCHAR(255),
    fecha DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Producto
CREATE TABLE Producto (
    idProducto INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100),
    stock INT,
    precio DECIMAL(10,2), -- Ampliado a 10 dígitos
    estado ENUM('activo', 'inactivo') NOT NULL,
    detalle TEXT,
    idCategoria INT,
    FOREIGN KEY (idCategoria) REFERENCES Categoria(idCategoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: IngresoProducto
CREATE TABLE IngresoProducto (
    idIngresoProducto INT AUTO_INCREMENT PRIMARY KEY,
    fechaIngreso DATE,
    precioCompra DECIMAL(10,2), -- Ampliado a 10 dígitos
    cantidad INT,
    detalle TEXT,
    idProducto INT,
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: VentaProducto
CREATE TABLE VentaProducto (
    idVentaProducto INT AUTO_INCREMENT PRIMARY KEY,
    fechaVenta DATE,
    total DECIMAL(10,2) -- Ampliado a 10 dígitos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: DetalleVentaProducto
CREATE TABLE DetalleVentaProducto (
    idDetalleVentaProducto INT AUTO_INCREMENT PRIMARY KEY,
    precioUnitario DECIMAL(10,2), -- Ampliado a 10 dígitos
    cantidad INT,
    idVentaProducto INT,
    idProducto INT,
    FOREIGN KEY (idVentaProducto) REFERENCES VentaProducto(idVentaProducto),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Servicio
CREATE TABLE Servicio (
    idServicio INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(45),
    precioUnitario DECIMAL(10,2), -- Ampliado a 10 dígitos
    estado ENUM('activo', 'inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: VentaServicio
CREATE TABLE VentaServicio (
    idVentaServicio INT AUTO_INCREMENT PRIMARY KEY,
    fechaVenta DATE,
    total DECIMAL(10,2) -- Ampliado a 10 dígitos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: DetalleVentaServicio
CREATE TABLE DetalleVentaServicio (
    idDetalleVentaServicio INT AUTO_INCREMENT PRIMARY KEY,
    fechaVenta DATE,
    precioUnitario DECIMAL(10,2), -- Ampliado a 10 dígitos
    idServicio INT,
    idVentaServicio INT,
    FOREIGN KEY (idServicio) REFERENCES Servicio(idServicio),
    FOREIGN KEY (idVentaServicio) REFERENCES VentaServicio(idVentaServicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Conductor
CREATE TABLE Conductor (
    idConductor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(25),
    apellido VARCHAR(25),
    tipoLicencia VARCHAR(10),
    telefono VARCHAR(10),
    direccion VARCHAR(50),
    dni VARCHAR(8),
    placa VARCHAR(15),
    correo VARCHAR(50),
    usuario VARCHAR(25),
    clave VARCHAR(255), -- Ampliado para almacenar hashes seguros
    estado ENUM('activo', 'inactivo') NOT NULL,
    detalle TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: SeguroPlanilla
CREATE TABLE SeguroPlanilla (
    idSeguroPlanilla INT AUTO_INCREMENT PRIMARY KEY,
    fechaEmision DATE,
    fechaVencimiento DATE,
    sueldo DECIMAL(10,2), -- Ampliado a 10 dígitos
    observacion TEXT,
    estado ENUM('activo', 'inactivo') NOT NULL,
    idConductor INT,
    FOREIGN KEY (idConductor) REFERENCES Conductor(idConductor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Soat
CREATE TABLE Soat (
    idSoat INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30),
    fechaMantenimiento DATE,
    fechaProxMantenimiento DATE,
    estado ENUM('activo', 'inactivo') NOT NULL,
    idConductor INT,
    FOREIGN KEY (idConductor) REFERENCES Conductor(idConductor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Alquiler
CREATE TABLE Alquiler (
    idAlquiler INT AUTO_INCREMENT PRIMARY KEY,
    identificador VARCHAR(30), 
    nombre VARCHAR(30),
    telefono VARCHAR(10),
    tipo ENUM('local','cochera'),
    fechaInicio DATE,
    periodicidad ENUM('mensual','semanal','diario'),
    pago DECIMAL(10,2),
    ubicacion VARCHAR(30),
    estado ENUM('activo', 'inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: Cotizacion
CREATE TABLE Cotizacion (
    idCotizacion INT AUTO_INCREMENT PRIMARY KEY,
    fechaCotizacion DATE,
    subtotalProducto DECIMAL(10,2), -- Ampliado a 10 dígitos
    subtotalServicio DECIMAL(10,2), -- Ampliado a 10 dígitos
    total DECIMAL(10,2), -- Ampliado a 10 dígitos
    detalle TEXT,
    nombre VARCHAR(30),
    idSoat INT,
    idConductor INT,
    FOREIGN KEY (idSoat) REFERENCES Soat(idSoat),
    FOREIGN KEY (idConductor) REFERENCES Conductor(idConductor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios`(`id`, `usuario`, `contrasena`, `correo`, `rol`, `estado`) VALUES ('1','admin','12345','admin@gmail.com','admin','activo')