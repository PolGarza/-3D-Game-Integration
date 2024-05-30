<?php

session_start();

if (!isset($_SESSION["correo"])) {
    // El usuario no ha iniciado sesión, puedes redirigirlo a la página de inicio de sesión o mostrar un mensaje de error.
    $response = array("error" => "No has iniciado sesión");
    echo json_encode($response);
    exit;
}

// El usuario ha iniciado sesión, obtén los datos del usuario desde la sesión
$usuarioData = array(
    "nombre_completo" => $_SESSION["correo"]["nombre_completo"],
    "nombre_usuario" => $_SESSION["correo"]["nombre_usuario"],
    "correo" => $_SESSION["correo"]["correo"],
    "imagen" => $_SESSION["correo"]["imagen"],
    "id_user" => $_SESSION["correo"]["id_user"],
    // Agrega otros campos de datos aquí
);

