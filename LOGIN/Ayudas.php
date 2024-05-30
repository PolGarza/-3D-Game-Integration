<?php


 /* 
 *Funcion que sirve para validar y limpiar un campo 
 *
 */

function validar_campo($campo) {
    $campo = trim($campo);
    $campo = stripslashes($campo);
    $campo = htmlspecialchars($campo);
    return $campo;
}
?>