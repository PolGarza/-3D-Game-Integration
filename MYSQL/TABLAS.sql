create database gw_michiventuras;

CREATE TABLE `usuarios` (
    `id_user` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre_usuario` VARCHAR(255) NOT NULL,
    `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `correo` VARCHAR(100) NOT NULL,
    `clave` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_user`)
);

CREATE TABLE `puntuacion` (
    `id_puntuacion` INT(11) NOT NULL AUTO_INCREMENT,
    `id_user` INT(11) NOT NULL,
    `jugador_1` INT(11) NOT NULL,
    `jugador_2` INT(11) NOT NULL,
    `fecha_juego` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_puntuacion`),
    FOREIGN KEY (`id_user`) REFERENCES `usuarios`(`id_user`)
);