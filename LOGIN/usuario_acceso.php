<?php
include '../LOGIN/conexion.php';
include '../LOGIN/entidad_usuario.php';

class Usuario_acceso extends Conexion {
    protected static $cnx;

    private static function getConexion() {
        self::$cnx = Conexion::conectar();
    }

    private static function desconectar() {
        self::$cnx = null;
    }

    public static function validarClaveStoredProcedure($clave) {
        $conexion = Conexion::conectar();

        try {
            $sentencia = $conexion->prepare("CALL SP_VALIDACION_CLAVE(?)");
            $sentencia->bindParam(1, $clave);
            $sentencia->execute();

            $is_valid = true;
            $error_message = null;
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $is_valid = false;
            http_response_code(400);
            echo json_encode(array("is_valid" => $is_valid, "error_message" => $error_message));
            exit;
        }

        return array("is_valid" => $is_valid, "error_message" => $error_message);
    }

    public static function login($correo) {
        $query = "CALL SP_LOGIN_VALIDAR(:correo, :clave)";

        self::getConexion();
        $resultado = self::$cnx->prepare($query);

        $resultado->bindValue(":correo", $correo->getCorreo());
        $resultado->bindValue(":clave", $correo->getClave());

        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            $filas = $resultado->fetch();
            if ($filas["correo"] == $correo->getCorreo()) {
                return true;
            }
        }

        return false;
    }

    public static function getCorreo($correo) {
        $query = "SELECT id_user, nombre_usuario, correo FROM usuarios WHERE correo = :correo AND clave = :clave";

        self::getConexion();
        $resultado = self::$cnx->prepare($query);

        $resultado->bindValue(":correo", $correo->getCorreo());
        $resultado->bindValue(":clave", $correo->getClave());

        $resultado->execute();

        $filas = $resultado->fetch();

        $correo = new Usuario_E();
        $correo->setId_user($filas["id_user"]);
        $correo->setNombre_usuario($filas["nombre_usuario"]);
        $correo->setCorreo($filas["correo"]);

        return $correo;
    }

    public static function registrar($correo) {
        $query = "INSERT INTO usuarios(nombre_usuario, correo, clave) VALUES (:nombre_usuario, :correo, :clave)";

        self::getConexion();
        $resultado = self::$cnx->prepare($query);

        $resultado->bindValue(":nombre_usuario", $correo->getNombre_usuario());
        $resultado->bindValue(":correo", $correo->getCorreo());
        $resultado->bindValue(":clave", $correo->getClave());

        if ($resultado->execute()) {
            return true;
        }

        return false;
    }

    public static function obtenerListaUsuarios() {
        self::getConexion();

        $query = "SELECT id_user, nombre_usuario FROM usuarios";
        $resultado = self::$cnx->query($query);

        $listaUsuarios = array();

        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $listaUsuarios[] = $fila;
        }

        self::desconectar();

        return $listaUsuarios;
    }
}
?>
