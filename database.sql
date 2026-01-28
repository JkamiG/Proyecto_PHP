-- Esborrar la base de dades si existeix / Borrar la base de datos si existe
DROP DATABASE IF EXISTS tienda_online;

-- Crear la base de dades / Crear la base de datos
CREATE DATABASE tienda_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda_online;

-- --------------------------------------------------------

--
-- Estructura de la taula `usuarios` / Estructura de la tabla `usuarios`
--
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

--
-- Estructura de la taula `categorias` / Estructura de la tabla `categorias`
--
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
);

--
-- Estructura de la taula `productos` / Estructura de la tabla `productos`
--
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

--
-- Estructura de la taula `pedidos` / Estructura de la tabla `pedidos`
--
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- --------------------------------------------------------

--
-- Bolcat de dades / Volcado de datos
--

-- Usuario (Password: '1')
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin User', '2023_jeremy.galora@iticbcn.cat', '$2y$10$gT8/sW.O.sW.O.sW.O.sW.O.sW.O.sW.O.sW.O.sW.O.sW.O.sW.O', 'admin'),

-- Categorias
INSERT INTO categorias (nombre, descripcion) VALUES
('Electronica', 'Dispositivos y gadgets'),
('Roba', 'Ropa para hombre y mujer'),
('Llar', 'Artículos para el hogar');

-- Productos
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES
('Smartphone X', 'Teléfono inteligente de última generación', 599.99, 50, 1),
('Portátil Pro', 'Ordenador portátil potente para trabajo', 999.50, 20, 1),
('Auriculares BT', 'Auriculares inalámbricos con cancelación de ruido', 89.90, 100, 1),
('Camiseta Básica', 'Camiseta de algodón 100%', 15.00, 200, 2),
('Pantalons Jeans', 'Pantalones vaqueros clásicos', 35.50, 80, 2),
('Jaqueta Hivern', 'Chaqueta impermeable para el frío', 85.00, 30, 2),
('Làmpada LED', 'Lámpara de escritorio bajo consumo', 25.00, 60, 3),
('Coixí Ergonòmic', 'Cojín para mejor descanso', 40.00, 25, 3),
('Joc de Llits', 'Sábanas y fundas de almohada', 55.00, 15, 3),
('Cafetera Express', 'Cafetera automática para casa', 120.00, 10, 3);

-- Pedidos
INSERT INTO pedidos (usuario_id, total, estado) VALUES
(2, 599.99, 'completado'),
(2, 104.90, 'pendiente'),
(3, 35.50, 'enviado');