<?php
include '../LOGIN/usuario_acceso.php';

class UsuarioControlador {
    public static function validarPassword($clave) {
        $usuarioAcceso = new Usuario_acceso();
        return $usuarioAcceso->validarClaveStoredProcedure($clave);
    }

    public static function login($correo, $clave) {
        $obj_correo = new Usuario_E();
        $obj_correo->setCorreo($correo);
        $obj_correo->setClave($clave);
        return Usuario_acceso::login($obj_correo);
    }

    public static function getCorreo($correo, $clave) {
        $obj_correo = new Usuario_E();
        $obj_correo->setCorreo($correo);
        $obj_correo->setClave($clave);
        return Usuario_acceso::getCorreo($obj_correo);
    }

    public static function registrar($nombre_usuario, $correo, $clave) {
        $obj_correo = new Usuario_E();
        $obj_correo->setNombre_usuario($nombre_usuario);
        $obj_correo->setCorreo($correo);
        $obj_correo->setClave($clave);
        return Usuario_acceso::registrar($obj_correo);
    }
}
?>
