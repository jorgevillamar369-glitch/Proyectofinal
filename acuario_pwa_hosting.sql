-- =====================================================================
--  ACUARIO AQUA VIVA - Version para hospedaje
--  No crea ni selecciona la base de datos: primero crea la base en el
--  panel del hosting, seleccionala en phpMyAdmin e importa este archivo.
-- =====================================================================

-- =====================================================================
--  ACUARIO AQUA VIVA - Base de datos del sistema de administración
--  Proyecto final de Programación Web Avanzada (Ejercicio 45: acuario)
--  Motor: MySQL 8 / MariaDB 10.4 o superior
--  Importar desde phpMyAdmin: pestaña "Importar" -> elegir este archivo.
-- =====================================================================

SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW IF EXISTS vista_ocupacion;
DROP TABLE IF EXISTS entradas, finanzas, peces, visitantes, tanques, especies, usuarios, roles;
SET FOREIGN_KEY_CHECKS = 1;


-- ---------------------------------------------------------------------
-- 1. Roles del sistema
-- ---------------------------------------------------------------------
CREATE TABLE roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(30) NOT NULL UNIQUE,
    descripcion VARCHAR(160) NOT NULL
);

INSERT INTO roles (id, nombre, descripcion) VALUES
(1, 'Administrador',          'Control total del acuario: personal, colecciones, taquilla y finanzas.'),
(2, 'Encargado de peces',     'Gestiona los peces, las especies y las condiciones de los tanques.'),
(3, 'Encargado de visitantes', 'Registra visitantes, vende entradas y lleva el control de finanzas.');

-- ---------------------------------------------------------------------
-- 2. Usuarios del personal (contraseñas cifradas con password_hash de PHP)
--    Clave de demostración para los tres usuarios: acuario2026
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(70)  NOT NULL,
    correo      VARCHAR(100) NOT NULL UNIQUE,
    contrasena  VARCHAR(255) NOT NULL,
    id_rol      INT          NOT NULL,
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (id_rol) REFERENCES roles(id)
);

INSERT INTO usuarios (nombre, correo, contrasena, id_rol, activo) VALUES
('Andrea Salazar',  'admin@aquaviva.ec',      '$2y$10$9AhZGK4L.hgXwxNAvMJwu.Jy.TwekuRejW888UxyBenN731sR.ZkC', 1, 1),
('Marco Cedeño',    'peces@aquaviva.ec',      '$2y$10$9AhZGK4L.hgXwxNAvMJwu.Jy.TwekuRejW888UxyBenN731sR.ZkC', 2, 1),
('Lucía Andrade',   'visitantes@aquaviva.ec', '$2y$10$9AhZGK4L.hgXwxNAvMJwu.Jy.TwekuRejW888UxyBenN731sR.ZkC', 3, 1);

-- ---------------------------------------------------------------------
-- 3. Especies: ficha técnica que sirve de referencia para validar el agua
-- ---------------------------------------------------------------------
CREATE TABLE especies (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comun      VARCHAR(80)  NOT NULL,
    nombre_cientifico VARCHAR(100) NOT NULL,
    habitat           ENUM('marino','dulce','salobre') NOT NULL,
    temp_min          DECIMAL(4,1) NOT NULL,
    temp_max          DECIMAL(4,1) NOT NULL,
    ph_min            DECIMAL(3,1) NOT NULL,
    ph_max            DECIMAL(3,1) NOT NULL,
    tamanio_max_cm    INT          NOT NULL COMMENT 'Tamaño máximo del adulto en centímetros',
    dieta             VARCHAR(120) NOT NULL,
    descripcion       VARCHAR(400) NOT NULL
);

INSERT INTO especies (nombre_comun, nombre_cientifico, habitat, temp_min, temp_max, ph_min, ph_max, tamanio_max_cm, dieta, descripcion) VALUES
('Tiburón toro',            'Carcharhinus leucas',            'salobre', 23.0, 28.0, 7.8, 8.4, 340, 'Peces y cefalópodos',  'Tolera agua dulce y salada; es la especie insignia de la laguna central.'),
('Manta gigante',           'Mobula birostris',              'marino',  22.0, 26.0, 8.0, 8.5, 500, 'Plancton',             'Requiere amplias columnas de agua y corrientes suaves.'),
('Pez payaso',              'Amphiprion ocellaris',          'marino',  24.0, 27.0, 8.0, 8.4,  11, 'Plancton y algas',     'Vive en simbiosis con anemomas; muy resistente en cautiverio.'),
('Cirujano azul',           'Paracanthurus hepatus',         'marino',  24.0, 27.0, 8.1, 8.4,  31, 'Algas y plancton',     'Nada en cardúmenes sobre el arrecife; sensible al amoníaco.'),
('Caballito de mar',        'Hippocampus kuda',              'marino',  22.0, 26.0, 8.0, 8.4,  30, 'Microcrustáceos',      'Necesita corriente baja y soportes para agarrarse con la cola.'),
('Medusa luna',             'Aurelia aurita',                'marino',  15.0, 19.0, 7.9, 8.4,  40, 'Plancton',             'Se mantiene en tanques de flujo circular y luz tenue.'),
('Piraña de vientre rojo',  'Pygocentrus nattereri',         'dulce',   24.0, 28.0, 6.0, 7.5,  33, 'Peces y carroña',      'Cardumen activo del tanque amazónico; alimentación controlada.'),
('Arawana plateada',        'Osteoglossum bicirrhosum',      'dulce',   24.0, 28.0, 6.0, 7.0,  90, 'Peces e insectos',     'Pez de superficie, salta con facilidad: el tanque lleva tapa.'),
('Anguila eléctrica',       'Electrophorus electricus',      'dulce',   23.0, 28.0, 6.5, 7.5, 250, 'Peces',                'Descarga de hasta 860 voltios; manejo solo con personal técnico.'),
('Pez globo enano',         'Carinotetraodon travancoricus', 'dulce',   24.0, 28.0, 6.5, 7.5,   3, 'Caracoles',            'El pez globo más pequeño del mundo: 3 cm en la edad adulta.'),
('Raya motoro',             'Potamotrygon motoro',           'dulce',   24.0, 28.0, 6.5, 7.5,  50, 'Peces y crustáceos',   'Vive en el fondo arenoso; requiere sustrato fino.'),
('Pez arquero',             'Toxotes jaculatrix',            'salobre', 25.0, 28.0, 7.0, 8.4,  30, 'Insectos',             'Derriba insectos escupiendo chorros de agua desde la superficie.');

-- ---------------------------------------------------------------------
-- 4. Tanques y peces
-- ---------------------------------------------------------------------
CREATE TABLE tanques (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    codigo        VARCHAR(12)  NOT NULL UNIQUE,
    nombre        VARCHAR(70)  NOT NULL,
    habitat       ENUM('marino','dulce','salobre') NOT NULL DEFAULT 'marino',
    capacidad     INT          NOT NULL COMMENT 'Volumen en litros',
    temperatura   DECIMAL(4,1) NOT NULL,
    ph            DECIMAL(3,1) NOT NULL,
    iluminacion   ENUM('baja','media','alta') NOT NULL DEFAULT 'media',
    estado        ENUM('operativo','mantenimiento','cuarentena') NOT NULL DEFAULT 'operativo',
    notas         VARCHAR(255) DEFAULT NULL
);

INSERT INTO tanques (codigo, nombre, habitat, capacidad, temperatura, ph, iluminacion, estado, notas) VALUES
('T-01', 'Gran Arrecife',        'marino',  12000, 25.5, 8.2, 'alta',  'operativo',     'Tanque principal de dos pisos, visible desde el vestíbulo.'),
('T-02', 'Laguna de Tiburones',  'salobre',  7000, 25.0, 7.9, 'media', 'operativo',     'Circulación reforzada; revisión de salinidad los martes.'),
('T-03', 'Sala de Medusas',      'marino',   1500, 17.0, 8.1, 'baja',  'operativo',     'Flujo circular con luz azul tenue.'),
('T-04', 'Ríos del Amazonas',    'dulce',    4000, 26.5, 6.8, 'media', 'operativo',     'Filtro de turba para mantener el pH ácido.'),
('T-05', 'Cuarentena Norte',     'marino',    800, 24.0, 8.0, 'baja',  'cuarentena',    'Aislamiento de ejemplares nuevos durante 21 días.'),
('T-06', 'Manglar',              'salobre',  3000, 25.0, 7.6, 'media', 'mantenimiento', 'Cambio de sustrato previsto para la próxima semana.'),
('T-07', 'Tanque Táctil',        'marino',   1200, 23.5, 8.2, 'alta',  'operativo',     'Zona interactiva supervisada por un guía.');

CREATE TABLE peces (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(60)  NOT NULL COMMENT 'Nombre con el que se identifica al ejemplar',
    id_especie    INT          NOT NULL,
    id_tanque     INT          DEFAULT NULL COMMENT 'NULL cuando el pez está fuera de exhibición',
    tamanio_cm    INT          NOT NULL,
    edad_meses    INT          NOT NULL,
    sexo          ENUM('macho','hembra','indefinido') NOT NULL DEFAULT 'indefinido',
    fecha_ingreso DATE         NOT NULL,
    estado        ENUM('saludable','observacion','tratamiento','trasladado') NOT NULL DEFAULT 'saludable',
    CONSTRAINT fk_peces_especie FOREIGN KEY (id_especie) REFERENCES especies(id),
    CONSTRAINT fk_peces_tanque  FOREIGN KEY (id_tanque)  REFERENCES tanques(id) ON DELETE SET NULL
);

INSERT INTO peces (nombre, id_especie, id_tanque, tamanio_cm, edad_meses, sexo, fecha_ingreso, estado) VALUES
('Capitán',    1, 2, 210, 132, 'macho',      '2021-06-14', 'saludable'),
('Bruna',      1, 2, 185, 108, 'hembra',     '2022-02-08', 'saludable'),
('Nube',       2, 1, 320,  96, 'indefinido', '2022-09-30', 'saludable'),
('Chispa',     3, 1,  8,   18, 'macho',      '2025-03-11', 'saludable'),
('Chispa II',  3, 1,  7,   14, 'hembra',     '2025-08-19', 'saludable'),
('Azul',       4, 1,  22,  26, 'macho',      '2024-11-05', 'saludable'),
('Rizo',       5, 7,  16,  12, 'macho',      '2025-12-02', 'observacion'),
('Luna',       6, 3,  28,  10, 'indefinido', '2026-01-20', 'saludable'),
('Filo',       6, 3,  24,   8, 'indefinido', '2026-03-04', 'saludable'),
('Diente',     7, 4,  26,  48, 'macho',      '2023-05-22', 'saludable'),
('Diente II',  7, 4,  23,  44, 'hembra',     '2023-05-22', 'saludable'),
('Plata',      8, 4,  72,  60, 'hembra',     '2023-10-10', 'saludable'),
('Voltio',     9, 4, 165,  84, 'macho',      '2022-07-18', 'tratamiento'),
('Bomba',     10, 4,   3,   9, 'indefinido', '2026-04-15', 'saludable'),
('Mancha',    11, 4,  38,  30, 'hembra',     '2024-08-27', 'saludable'),
('Tirador',   12, 7,  21,  20, 'macho',      '2025-05-09', 'saludable'),
('Naufragio',  5, 5,  14,   6, 'macho',      '2026-10-28', 'observacion');

-- ---------------------------------------------------------------------
-- 5. Visitantes, entradas y finanzas
-- ---------------------------------------------------------------------
CREATE TABLE visitantes (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(80)  NOT NULL,
    correo         VARCHAR(100) NOT NULL UNIQUE,
    telefono       VARCHAR(15)  DEFAULT NULL,
    procedencia    VARCHAR(80)  NOT NULL DEFAULT 'Quito',
    tipo           ENUM('general','estudiante','nino','adulto_mayor') NOT NULL DEFAULT 'general',
    fecha_registro DATE         NOT NULL
);

INSERT INTO visitantes (nombre, correo, telefono, procedencia, tipo, fecha_registro) VALUES
('Camila Herrera',    'camila.herrera@correo.ec',   '0991234567', 'Quito',        'general',      '2026-09-02'),
('Mateo Villacís',    'mateo.villacis@correo.ec',   '0987654321', 'Guayaquil',    'estudiante',   '2026-09-14'),
('Sofía Guamán',      'sofia.guaman@correo.ec',     '0976543210', 'Cuenca',       'general',      '2026-09-21'),
('Benjamín Torres',   'benjamin.torres@correo.ec',  '0965432109', 'Ambato',       'nino',         '2026-10-05'),
('Valentina Ponce',   'valentina.ponce@correo.ec',  '0954321098', 'Loja',         'estudiante',   '2026-10-12'),
('Don Aurelio Mena',  'aurelio.mena@correo.ec',     '0943210987', 'Riobamba',     'adulto_mayor', '2026-10-19'),
('Isabella Vargas',   'isabella.vargas@correo.ec',  '0932109876', 'Manta',        'general',      '2026-11-03'),
('Thiago Cabrera',    'thiago.cabrera@correo.ec',   '0921098765', 'Ibarra',       'nino',         '2026-11-09'),
('Renata Espinoza',   'renata.espinoza@correo.ec',  '0910987654', 'Santo Domingo','general',      '2026-11-14');

CREATE TABLE entradas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    codigo          VARCHAR(14)  NOT NULL UNIQUE,
    id_visitante    INT          NOT NULL,
    fecha           DATE         NOT NULL,
    hora            TIME         NOT NULL,
    cantidad        INT          NOT NULL DEFAULT 1,
    tarifa          ENUM('general','estudiante','nino','adulto_mayor') NOT NULL,
    precio_unitario DECIMAL(6,2) NOT NULL,
    total           DECIMAL(8,2) NOT NULL,
    metodo_pago     ENUM('efectivo','tarjeta','transferencia') NOT NULL DEFAULT 'efectivo',
    id_usuario      INT          DEFAULT NULL COMMENT 'Personal que registró la venta',
    CONSTRAINT fk_entradas_visitante FOREIGN KEY (id_visitante) REFERENCES visitantes(id) ON DELETE CASCADE,
    CONSTRAINT fk_entradas_usuario   FOREIGN KEY (id_usuario)   REFERENCES usuarios(id)   ON DELETE SET NULL
);

-- Fechas de ejemplo: siempre dentro del mes en curso y nunca posteriores a hoy.
SET @mes := DATE_FORMAT(CURDATE(), '%Y-%m-01');

INSERT INTO entradas (codigo, id_visitante, fecha, hora, cantidad, tarifa, precio_unitario, total, metodo_pago, id_usuario) VALUES
('AV-000101', 1, LEAST(DATE_ADD(@mes, INTERVAL 15 DAY), CURDATE()), '10:15:00', 2, 'general',      6.00, 12.00, 'tarjeta',       3),
('AV-000102', 2, LEAST(DATE_ADD(@mes, INTERVAL 14 DAY), CURDATE()), '11:40:00', 1, 'estudiante',   4.00,  4.00, 'efectivo',      3),
('AV-000103', 3, LEAST(DATE_ADD(@mes, INTERVAL 12 DAY), CURDATE()), '09:05:00', 4, 'general',      6.00, 24.00, 'tarjeta',       3),
('AV-000104', 4, LEAST(DATE_ADD(@mes, INTERVAL 11 DAY), CURDATE()), '14:30:00', 3, 'nino',         3.00,  9.00, 'efectivo',      3),
('AV-000105', 5, LEAST(DATE_ADD(@mes, INTERVAL 9 DAY),  CURDATE()), '16:10:00', 2, 'estudiante',   4.00,  8.00, 'transferencia', 3),
('AV-000106', 6, LEAST(DATE_ADD(@mes, INTERVAL 8 DAY),  CURDATE()), '09:45:00', 2, 'adulto_mayor', 2.50,  5.00, 'efectivo',      3),
('AV-000107', 7, LEAST(DATE_ADD(@mes, INTERVAL 6 DAY),  CURDATE()), '12:20:00', 5, 'general',      6.00, 30.00, 'tarjeta',       1),
('AV-000108', 8, LEAST(DATE_ADD(@mes, INTERVAL 5 DAY),  CURDATE()), '15:00:00', 2, 'nino',         3.00,  6.00, 'efectivo',      3),
('AV-000109', 9, LEAST(DATE_ADD(@mes, INTERVAL 4 DAY),  CURDATE()), '10:50:00', 3, 'general',      6.00, 18.00, 'tarjeta',       3),
('AV-000110', 2, LEAST(DATE_ADD(@mes, INTERVAL 3 DAY),  CURDATE()), '13:15:00', 1, 'estudiante',   4.00,  4.00, 'efectivo',      3),
('AV-000111', 1, LEAST(DATE_ADD(@mes, INTERVAL 2 DAY),  CURDATE()), '11:05:00', 4, 'general',      6.00, 24.00, 'tarjeta',       3),
('AV-000112', 5, LEAST(DATE_ADD(@mes, INTERVAL 1 DAY),  CURDATE()), '16:40:00', 2, 'estudiante',   4.00,  8.00, 'transferencia', 3),
('AV-000113', 7, CURDATE(),                                          '09:30:00', 3, 'general',      6.00, 18.00, 'efectivo',      3);

CREATE TABLE finanzas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    fecha       DATE         NOT NULL,
    tipo        ENUM('ingreso','egreso') NOT NULL,
    categoria   VARCHAR(40)  NOT NULL,
    descripcion VARCHAR(160) NOT NULL,
    monto       DECIMAL(9,2) NOT NULL,
    id_usuario  INT          DEFAULT NULL,
    CONSTRAINT fk_finanzas_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL
);

INSERT INTO finanzas (fecha, tipo, categoria, descripcion, monto, id_usuario) VALUES
(LEAST(DATE_ADD(@mes, INTERVAL 15 DAY), CURDATE()), 'ingreso', 'Tienda',          'Venta de peluches y guías del acuario',          486.40, 3),
(LEAST(DATE_ADD(@mes, INTERVAL 14 DAY), CURDATE()), 'egreso',  'Alimento',        'Plancton congelado y alimento granulado',        312.00, 2),
(LEAST(DATE_ADD(@mes, INTERVAL 12 DAY), CURDATE()), 'egreso',  'Mantenimiento',   'Cambio de bombas del tanque T-02',               420.00, 1),
(LEAST(DATE_ADD(@mes, INTERVAL 11 DAY), CURDATE()), 'ingreso', 'Cafetería',       'Consumo de la cafetería del vestíbulo',           742.10, 3),
(LEAST(DATE_ADD(@mes, INTERVAL 10 DAY), CURDATE()), 'egreso',  'Veterinaria',     'Revisión del ejemplar Voltio (anguila eléctrica)', 145.00, 1),
(LEAST(DATE_ADD(@mes, INTERVAL 9 DAY),  CURDATE()), 'ingreso', 'Donaciones',      'Aporte de la fundación Océano Abierto',         2200.00, 3),
(LEAST(DATE_ADD(@mes, INTERVAL 8 DAY),  CURDATE()), 'egreso',  'Planilla',        'Quincena del personal de sala',                  1840.00, 1),
(LEAST(DATE_ADD(@mes, INTERVAL 7 DAY),  CURDATE()), 'egreso',  'Servicios',       'Consumo eléctrico de los sistemas de bombeo',     690.30, 1),
(LEAST(DATE_ADD(@mes, INTERVAL 6 DAY),  CURDATE()), 'ingreso', 'Tienda',          'Venta de postales y libretas marinas',            268.90, 3),
(LEAST(DATE_ADD(@mes, INTERVAL 5 DAY),  CURDATE()), 'egreso',  'Alimento',        'Krill y camarón para la laguna de tiburones',     275.60, 2),
(LEAST(DATE_ADD(@mes, INTERVAL 4 DAY),  CURDATE()), 'ingreso', 'Cafetería',       'Fin de semana de mayor afluencia',                342.10, 3),
(LEAST(DATE_ADD(@mes, INTERVAL 3 DAY),  CURDATE()), 'egreso',  'Mantenimiento',   'Repuesto del filtro de turba (T-04)',             198.00, 2),
(LEAST(DATE_ADD(@mes, INTERVAL 2 DAY),  CURDATE()), 'ingreso', 'Visitas guiadas', 'Ingreso adicional por visitas guiadas',           320.00, 3),
(LEAST(DATE_ADD(@mes, INTERVAL 1 DAY),  CURDATE()), 'egreso',  'Servicios',       'Internet y telefonía del edificio',                86.50, 1);

-- ---------------------------------------------------------------------
-- 6. Vista de apoyo: ocupación de cada tanque según la regla
--    "10 litros de agua por cada centímetro de pez adulto".
-- ---------------------------------------------------------------------
CREATE OR REPLACE VIEW vista_ocupacion AS
SELECT
    t.id,
    t.codigo,
    t.nombre,
    t.capacidad,
    COALESCE(SUM(p.tamanio_cm), 0)                    AS carga_cm,
    ROUND(t.capacidad / 10, 0)                        AS carga_maxima_cm,
    ROUND(COALESCE(SUM(p.tamanio_cm), 0) * 100 / (t.capacidad / 10), 1) AS ocupacion_pct,
    COUNT(p.id)                                        AS total_peces
FROM tanques t
LEFT JOIN peces p ON p.id_tanque = t.id AND p.estado <> 'trasladado'
GROUP BY t.id, t.codigo, t.nombre, t.capacidad;
