-- ============================================
-- BASE DE DATOS: COMODIN MOTORS
-- Sistema de Gestión de Taller Automotriz
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS comodin_motors 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE comodin_motors;

-- ============================================
-- TABLA: clientes
-- ============================================
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    empresa VARCHAR(100) NULL COMMENT 'Empresa del cliente (opcional)',
    direccion VARCHAR(255) NULL,
    email VARCHAR(100) NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT DEFAULT 1,
    INDEX idx_telefono (telefono),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: vehiculos
-- ============================================
CREATE TABLE vehiculos (
    id_vehiculo INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    anio YEAR NOT NULL,
    color VARCHAR(30) NOT NULL,
    placas VARCHAR(20) NOT NULL UNIQUE,
    numero_serie VARCHAR(50) NULL,
    kilometraje_inicial INT NOT NULL DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT DEFAULT 1,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE,
    INDEX idx_placas (placas),
    INDEX idx_cliente (id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: ordenes_servicio
-- ============================================
CREATE TABLE ordenes_servicio (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    numero_orden VARCHAR(20) NOT NULL UNIQUE COMMENT 'Número de orden (ej: 000097)',
    id_vehiculo INT NOT NULL,
    id_cliente INT NOT NULL,
    fecha_orden DATE NOT NULL,
    hora_ingreso TIME NOT NULL,
    kilometraje_actual INT NOT NULL,
    ingreso_grua TINYINT DEFAULT 0 COMMENT '0=No, 1=Sí',
    trabajo_realizar TEXT NOT NULL,
    observaciones TEXT NULL,
    proximo_servicio_km INT NULL COMMENT 'Kilometraje para próximo servicio',
    nivel_combustible ENUM('E', '1/4', '1/2', '3/4', 'F') DEFAULT '1/2',
    estado_orden ENUM('pendiente', 'en_proceso', 'completado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    costo_total DECIMAL(10,2) DEFAULT 0.00,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    INDEX idx_numero_orden (numero_orden),
    INDEX idx_fecha (fecha_orden),
    INDEX idx_estado (estado_orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: inventario_vehiculo
-- ============================================
CREATE TABLE inventario_vehiculo (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    gato TINYINT DEFAULT 0,
    herramientas TINYINT DEFAULT 0,
    triangulos TINYINT DEFAULT 0,
    tapetes TINYINT DEFAULT 0,
    llanta_refaccion TINYINT DEFAULT 0,
    extintor TINYINT DEFAULT 0,
    antena TINYINT DEFAULT 0,
    emblemas TINYINT DEFAULT 0,
    tapones_rueda TINYINT DEFAULT 0,
    cables TINYINT DEFAULT 0,
    estereo TINYINT DEFAULT 0,
    encendedor TINYINT DEFAULT 0,
    otros TEXT NULL COMMENT 'Otros items del inventario',
    FOREIGN KEY (id_orden) REFERENCES ordenes_servicio(id_orden) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: danos_vehiculo
-- ============================================
CREATE TABLE danos_vehiculo (
    id_dano INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    ubicacion ENUM('frontal', 'trasero', 'lateral_izquierdo', 'lateral_derecho', 'techo', 'inferior') NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    tipo_dano ENUM('rayón', 'abolladura', 'cristal_roto', 'faltante', 'otro') DEFAULT 'otro',
    coordenada_x DECIMAL(5,2) NULL COMMENT 'Posición X en diagrama (porcentaje)',
    coordenada_y DECIMAL(5,2) NULL COMMENT 'Posición Y en diagrama (porcentaje)',
    foto_url VARCHAR(255) NULL COMMENT 'Ruta de la foto del daño',
    FOREIGN KEY (id_orden) REFERENCES ordenes_servicio(id_orden) ON DELETE CASCADE,
    INDEX idx_orden (id_orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: servicios_realizados
-- ============================================
CREATE TABLE servicios_realizados (
    id_servicio_realizado INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    costo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    cantidad INT DEFAULT 1,
    fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_orden) REFERENCES ordenes_servicio(id_orden) ON DELETE CASCADE,
    INDEX idx_orden (id_orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: tecnicos
-- ============================================
CREATE TABLE tecnicos (
    id_tecnico INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NULL,
    especialidad VARCHAR(100) NULL,
    firma_digital VARCHAR(255) NULL COMMENT 'Ruta de la imagen de firma',
    activo TINYINT DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre_completo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: asignacion_tecnicos
-- ============================================
CREATE TABLE asignacion_tecnicos (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    id_tecnico INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_orden) REFERENCES ordenes_servicio(id_orden) ON DELETE CASCADE,
    FOREIGN KEY (id_tecnico) REFERENCES tecnicos(id_tecnico),
    INDEX idx_orden (id_orden),
    INDEX idx_tecnico (id_tecnico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: firmas
-- ============================================
CREATE TABLE firmas (
    id_firma INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    firma_tecnico VARCHAR(255) NULL COMMENT 'Ruta de imagen firma técnico',
    firma_cliente VARCHAR(255) NULL COMMENT 'Ruta de imagen firma cliente',
    fecha_firma_tecnico TIMESTAMP NULL,
    fecha_firma_cliente TIMESTAMP NULL,
    FOREIGN KEY (id_orden) REFERENCES ordenes_servicio(id_orden) ON DELETE CASCADE,
    UNIQUE KEY unique_orden (id_orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuarios (para el sistema web/app)
-- ============================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'tecnico', 'recepcion') DEFAULT 'recepcion',
    activo TINYINT DEFAULT 1,
    ultimo_acceso TIMESTAMP NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: configuracion
-- ============================================
CREATE TABLE configuracion (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion VARCHAR(255) NULL,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Insertar configuración inicial
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_taller', 'Centro Automotriz Comodín', 'Nombre del taller'),
('direccion_taller', 'Calzada Romeo Lucas, Cobán, A.V.', 'Dirección del taller'),
('telefono_taller', '7867 8073', 'Teléfono de contacto'),
('contador_orden', '97', 'Último número de orden generado'),
('iva_porcentaje', '12', 'Porcentaje de IVA'),
('moneda', 'GTQ', 'Moneda del sistema');

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (debes cambiarla después)
INSERT INTO usuarios (username, password_hash, nombre_completo, email, rol) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin@comodinmotors.com', 'admin');

-- Insertar técnico de ejemplo
INSERT INTO tecnicos (nombre_completo, telefono, especialidad) VALUES
('Técnico Principal', '1234-5678', 'Mecánica General');

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: Órdenes completas con datos de cliente y vehículo
CREATE VIEW vista_ordenes_completas AS
SELECT 
    o.id_orden,
    o.numero_orden,
    o.fecha_orden,
    o.hora_ingreso,
    o.estado_orden,
    o.costo_total,
    c.nombre AS nombre_cliente,
    c.telefono AS telefono_cliente,
    c.empresa AS empresa_cliente,
    v.marca,
    v.modelo,
    v.anio,
    v.color,
    v.placas,
    o.kilometraje_actual,
    o.trabajo_realizar,
    o.observaciones
FROM ordenes_servicio o
INNER JOIN clientes c ON o.id_cliente = c.id_cliente
INNER JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
ORDER BY o.fecha_orden DESC, o.hora_ingreso DESC;

-- Vista: Historial de vehículos
CREATE VIEW vista_historial_vehiculos AS
SELECT 
    v.id_vehiculo,
    v.placas,
    v.marca,
    v.modelo,
    v.anio,
    c.nombre AS propietario,
    c.empresa,
    COUNT(o.id_orden) AS total_servicios,
    MAX(o.fecha_orden) AS ultima_visita,
    MAX(o.kilometraje_actual) AS ultimo_kilometraje
FROM vehiculos v
INNER JOIN clientes c ON v.id_cliente = c.id_cliente
LEFT JOIN ordenes_servicio o ON v.id_vehiculo = o.id_vehiculo
GROUP BY v.id_vehiculo, v.placas, v.marca, v.modelo, v.anio, c.nombre, c.empresa;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

DELIMITER //

-- Procedimiento: Generar siguiente número de orden
CREATE PROCEDURE sp_generar_numero_orden(OUT nuevo_numero VARCHAR(20))
BEGIN
    DECLARE contador INT;
    
    -- Obtener y actualizar contador
    SELECT CAST(valor AS UNSIGNED) INTO contador 
    FROM configuracion 
    WHERE clave = 'contador_orden';
    
    SET contador = contador + 1;
    
    -- Actualizar contador
    UPDATE configuracion 
    SET valor = contador 
    WHERE clave = 'contador_orden';
    
    -- Generar número con formato (6 dígitos)
    SET nuevo_numero = LPAD(contador, 6, '0');
END//

-- Procedimiento: Obtener órdenes pendientes
CREATE PROCEDURE sp_obtener_ordenes_pendientes()
BEGIN
    SELECT * FROM vista_ordenes_completas 
    WHERE estado_orden IN ('pendiente', 'en_proceso')
    ORDER BY fecha_orden DESC, hora_ingreso ASC;
END//

-- Procedimiento: Buscar cliente por teléfono
CREATE PROCEDURE sp_buscar_cliente_telefono(IN p_telefono VARCHAR(20))
BEGIN
    SELECT * FROM clientes 
    WHERE telefono LIKE CONCAT('%', p_telefono, '%')
    AND activo = 1;
END//

-- Procedimiento: Obtener vehículos de un cliente
CREATE PROCEDURE sp_obtener_vehiculos_cliente(IN p_id_cliente INT)
BEGIN
    SELECT * FROM vehiculos 
    WHERE id_cliente = p_id_cliente 
    AND activo = 1
    ORDER BY fecha_registro DESC;
END//

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger: Actualizar costo total de la orden al agregar servicio
CREATE TRIGGER trg_actualizar_costo_orden
AFTER INSERT ON servicios_realizados
FOR EACH ROW
BEGIN
    UPDATE ordenes_servicio 
    SET costo_total = (
        SELECT COALESCE(SUM(costo * cantidad), 0) 
        FROM servicios_realizados 
        WHERE id_orden = NEW.id_orden
    )
    WHERE id_orden = NEW.id_orden;
END//

-- Trigger: Actualizar costo total al modificar servicio
CREATE TRIGGER trg_actualizar_costo_orden_update
AFTER UPDATE ON servicios_realizados
FOR EACH ROW
BEGIN
    UPDATE ordenes_servicio 
    SET costo_total = (
        SELECT COALESCE(SUM(costo * cantidad), 0) 
        FROM servicios_realizados 
        WHERE id_orden = NEW.id_orden
    )
    WHERE id_orden = NEW.id_orden;
END//

-- Trigger: Actualizar costo total al eliminar servicio
CREATE TRIGGER trg_actualizar_costo_orden_delete
AFTER DELETE ON servicios_realizados
FOR EACH ROW
BEGIN
    UPDATE ordenes_servicio 
    SET costo_total = (
        SELECT COALESCE(SUM(costo * cantidad), 0) 
        FROM servicios_realizados 
        WHERE id_orden = OLD.id_orden
    )
    WHERE id_orden = OLD.id_orden;
END//

DELIMITER ;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

-- Índice para búsquedas por rango de fechas
CREATE INDEX idx_fecha_estado ON ordenes_servicio(fecha_orden, estado_orden);

-- Índice para búsquedas de vehículos por cliente
CREATE INDEX idx_vehiculo_cliente_activo ON vehiculos(id_cliente, activo);

-- ============================================
-- DATOS DE EJEMPLO (OPCIONAL - COMENTADO)
-- ============================================

/*
-- Cliente de ejemplo
INSERT INTO clientes (nombre, telefono, empresa, direccion) VALUES
('Saúl Rivera', '3565 3250', 'Transportes del Norte', 'Zona 1, Cobán');

-- Vehículo de ejemplo
INSERT INTO vehiculos (id_cliente, marca, modelo, anio, color, placas, numero_serie, kilometraje_inicial) VALUES
(1, 'Changan', '2020', 2020, 'Blanco', 'P590LBB', 'CH2020XYZ123', 29954);

-- Orden de ejemplo
INSERT INTO ordenes_servicio (numero_orden, id_vehiculo, id_cliente, fecha_orden, hora_ingreso, kilometraje_actual, ingreso_grua, trabajo_realizar, observaciones, proximo_servicio_km, nivel_combustible) VALUES
('000097', 1, 1, '2016-04-09', '10:20:00', 29954, 0, 'Servicio Neto y tiempos\n- Cambio de pastillas delanteras', 'Próximo servicio 39,954 km', 39954, '1/2');

-- Inventario del vehículo
INSERT INTO inventario_vehiculo (id_orden, gato, herramientas, llanta_refaccion, antena, emblemas) VALUES
(1, 1, 1, 1, 1, 1);
*/

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

-- Mostrar resumen de tablas creadas
SELECT 
    'Base de datos creada exitosamente' AS mensaje,
    COUNT(*) AS total_tablas
FROM information_schema.tables 
WHERE table_schema = 'comodin_motors';