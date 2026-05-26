-- Base de datos Veterinaria_Pro
-- Ejecutar en MySQL/MariaDB (phpMyAdmin o consola)

CREATE DATABASE IF NOT EXISTS Veterinaria_Pro
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE Veterinaria_Pro;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  contraseña VARCHAR(32) NOT NULL,
  rol ENUM('admin', 'veterinario', 'recepcion') DEFAULT 'admin',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  telefono VARCHAR(20),
  direccion VARCHAR(255),
  correo VARCHAR(150),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mascotas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  especie VARCHAR(50) NOT NULL,
  raza VARCHAR(80),
  edad INT DEFAULT 0,
  id_cliente INT NOT NULL,
  FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS citas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_mascota INT NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  motivo VARCHAR(255),
  estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') DEFAULT 'pendiente',
  FOREIGN KEY (id_mascota) REFERENCES mascotas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS historial (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_mascota INT NOT NULL,
  descripcion TEXT NOT NULL,
  diagnostico VARCHAR(255),
  tratamiento TEXT,
  fecha DATE NOT NULL,
  FOREIGN KEY (id_mascota) REFERENCES mascotas(id) ON DELETE CASCADE
);

-- Usuario demo: admin@veterinaria.com / admin123
INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES
('Administrador', 'admin@veterinaria.com', MD5('admin123'), 'admin')
ON DUPLICATE KEY UPDATE nombre = nombre;

INSERT INTO clientes (nombre, telefono, direccion, correo) VALUES
('María González', '555-0101', 'Calle Principal 12', 'maria@email.com'),
('Carlos Ruiz', '555-0202', 'Av. Central 45', 'carlos@email.com')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO mascotas (nombre, especie, raza, edad, id_cliente) VALUES
('Luna', 'Perro', 'Labrador', 3, 1),
('Michi', 'Gato', 'Siamés', 2, 1),
('Rocky', 'Perro', 'Bulldog', 5, 2)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO citas (id_mascota, fecha, hora, motivo, estado) VALUES
(1, CURDATE(), '10:00:00', 'Control anual y vacunas', 'confirmada'),
(2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:30:00', 'Revisión dental', 'pendiente'),
(3, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '09:00:00', 'Consulta por cojera', 'pendiente')
ON DUPLICATE KEY UPDATE motivo = VALUES(motivo);

INSERT INTO historial (id_mascota, descripcion, diagnostico, tratamiento, fecha) VALUES
(1, 'Control de rutina. Peso y temperatura normales.', 'Estado saludable', 'Refuerzo antiparasitario cada 3 meses', DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
(2, 'Paciente con encías inflamadas y mal aliento.', 'Gingivitis leve', 'Limpieza dental programada; cepillado diario', DATE_SUB(CURDATE(), INTERVAL 14 DAY)),
(3, 'Dueño reporta cojera en pata trasera derecha.', 'Esguince leve', 'Reposo 7 días, antiinflamatorio según peso', DATE_SUB(CURDATE(), INTERVAL 7 DAY))
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
