<?php
include '../login/Usuario_Controlador.php';
include '../login/Ayudas.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["nombre_usuario"]) && isset($_POST["correo"]) && isset($_POST["confirmar_pass"])) {
        $nombre_usuario = validar_campo($_POST["nombre_usuario"]);
        $correo = validar_campo($_POST["correo"]);
        $confirmar_pass = validar_campo($_POST["confirmar_pass"]);

        if ($correo) {
            if (UsuarioControlador::registrar($nombre_usuario, $correo, $confirmar_pass)) {
                $respuesta = array("success" => true, "message" => "Registro exitoso");
                $usuario = new UsuarioControlador();
                $correo = $usuario->getCorreo($correo, $confirmar_pass);

                $_SESSION["correo"] = array(
                    "id_user" => $correo->getId_user(),
                    "nombre_usuario" => $correo->getNombre_usuario(),
                    "correo" => $correo->getCorreo(),
                    "clave" => $confirmar_pass
                );

                header("location: ../Login_home.html");
            } else {
                header("location: registro.php?error=2");
            }
        } else {
            header("location: registro.php?error=3");
        }
    } else {
        header("location: registro.php?error=1");
    }
}
?>
