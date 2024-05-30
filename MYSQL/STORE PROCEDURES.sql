
-- STORE PROCEDURE --

-- VALIDAR USUARIO Y CONTRASEÑA --  


DELIMITER //

CREATE PROCEDURE `SP_LOGIN_VALIDAR`(
    IN p_correo VARCHAR(255), 
    IN p_clave VARCHAR(255)
)
BEGIN
    SELECT id_user, nombre_usuario, fecha_registro, correo
    FROM usuarios
    WHERE correo = p_correo AND clave = p_clave;
END //

DELIMITER ;





DELIMITER //

CREATE PROCEDURE `SP_VALIDACION_CLAVE`(
   IN clave VARCHAR(255)
)
BEGIN
   DECLARE contiene_minuscula INT DEFAULT 0;
   DECLARE contiene_mayuscula INT DEFAULT 0;
   DECLARE i INT DEFAULT 1;
   DECLARE j INT DEFAULT 1;
 
   -- Verificar la longitud de la contraseña
   IF LENGTH(clave) < 8 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'La clave debe tener al menos 8 caracteres.';
   ELSE
      -- Verificar si la contraseña contiene al menos una letra en minúscula
      WHILE i <= LENGTH(clave) DO
         IF ASCII(SUBSTRING(clave, i, 1)) BETWEEN ASCII('a') AND ASCII('z') THEN
            SET contiene_minuscula = 1;
         END IF;
         SET i = i + 1;
      END WHILE;
 
      -- Verificar si la contraseña contiene al menos una letra en mayúscula
      WHILE j <= LENGTH(clave) DO
         IF ASCII(SUBSTRING(clave, j, 1)) BETWEEN ASCII('A') AND ASCII('Z') THEN
            SET contiene_mayuscula = 1;
         END IF;
         SET j = j + 1;
      END WHILE;
 
      IF contiene_minuscula = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'La clave debe contener al menos una letra en minúscula.';
      END IF;
 
      IF contiene_mayuscula = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'La clave debe contener al menos una letra en mayúscula.';
      END IF;
   END IF;

   -- Si la contraseña cumple con los requisitos, no se lanza una excepción
END //

DELIMITER ;
