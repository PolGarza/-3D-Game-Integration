<?php
include '../login/Usuario_Controlador.php';

session_start();

if (isset($_POST["correo"]) && isset($_POST["pass"])) {
    $Correo = $_POST["correo"];
    $pass = $_POST["pass"];

    $resultado = array("success" => false); // Inicializamos como falso

    // Llama a la función para validar la contraseña
    $validacion = UsuarioControlador::validarPassword($pass);

    if ($validacion['is_valid']) {
        // La contraseña es válida, procede a la autenticación
        if (UsuarioControlador::login($Correo, $pass)) {
            // Crea una instancia de UsuarioControlador
            $controlador = new UsuarioControlador();
            // Llama al método de instancia getCorreo para obtener los datos del usuario
            $correo = $controlador->getCorreo($Correo, $pass);

            $_SESSION["correo"] = array(
                "id_user" => $correo->getId_user(),
               
                "nombre_usuario" => $correo->getNombre_usuario(),
                "correo" => $correo->getCorreo(),
            
         
            );

            $resultado["success"] = true;
        }
    } else {
        // La contraseña no es válida, puedes manejarlo aquí (por ejemplo, mostrar un mensaje de error).
        $resultado["error_message"] = "La contraseña no cumple con los requisitos de complejidad.";
    }

    echo json_encode($resultado); // Devolvemos la respuesta como JSON
} else {
    $resultado = array("success" => false);
    echo json_encode($resultado); // Devolvemos la respuesta como JSON
}
?>
