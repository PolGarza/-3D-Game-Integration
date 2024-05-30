<?php
// Incluir el archivo de conexión
include 'LOGIN/conexion.php';

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["correo"])) {
    exit;
}

// Obtener el ID del usuario
$id_usuario = $_SESSION["correo"]["id_user"];

// Realizar la consulta a la base de datos para obtener los datos de puntuación, ordenando por fecha descendente
$conexion = Conexion::conectar();
$sql = "SELECT fecha_juego, jugador_1, jugador_2 FROM puntuacion WHERE id_user = :id_usuario ORDER BY fecha_juego DESC";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$puntuaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="STYLES/Home.css">

  <link rel="shortcut icon" href="IMAGES/LOGO.png" type="image/x-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Itim&family=Signika+Negative:wght@300..700&display=swap" 
  rel="stylesheet">
  <title>Michiventuras</title>


</head>

<body>

    <audio autoplay loop>
        <source src="STYLES/AUDIO/MEOW.mp3" type="audio/mpeg">
        
    </audio>


    <style>
    .container {
        width: 60%;
        margin: 20px auto;
        border: 2px solid black;
        max-height: 400px; /* Altura máxima para la tabla */
        overflow-y: auto; /* Habilita el desplazamiento vertical */
        position: relative; /* Establece la posición relativa para el deslizador */
        background-color: #FA8072; /* Color de fondo naranja */
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }
    input[type="range"] {
        position: absolute;
        top: 0;
        right: -20px; /* Cambia la posición a la derecha del contenedor */
        height: 100%; /* Establece la altura para que coincida con el contenedor */
        transform: rotate(270deg); /* Rota el deslizador verticalmente */
        transform-origin: right top; /* Establece el origen de la transformación en la esquina superior derecha */
    }
</style>



<div class="container" id="table-container">
    <table id="puntuacion-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Jugador 1</th>
                <th>Jugador 2</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($puntuaciones as $puntuacion): ?>
            <tr>
                <td><?php echo $puntuacion['fecha_juego']; ?></td>
                <td><?php echo $puntuacion['jugador_1']; ?></td>
                <td><?php echo $puntuacion['jugador_2']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <input type="range" orient="vertical" id="scroll-bar">
</div>

    <section class="Recomendaciones">

    
        <div class="contenedor">
            <div class="galeria_pub">

                <!-- Aquí puedes mostrar el nombre de usuario si está autenticado -->
                <?php
               
                if(isset($_SESSION['correo'])) {
                    echo "<div class='usuarioLogueado'>¡Bienvenido, " . $_SESSION['correo']['nombre_usuario'] . "!</div>";
                }
                ?>

                <div class="imagen-port">
                    <img src="STYLES/IMAGES/SINLGEPLAYER.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="ESCENARIOS.php">JUGADOR</a>
                    </div>
                </div>

                <div class="imagen-port">
                    <img src="STYLES/IMAGES/MULTIPLAYER.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="ESCENARIOS_M.php">MULTIJUGADOR</a>
                    </div>
                </div>

                <div class="imagen-port">
                    <img src="STYLES/IMAGES/CLOSE.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="Login_home.html">SALIR</a>
                    </div>
                </div>
                <div class="imagen-port">
                    <img src="STYLES/IMAGES/CONFIGURATION.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="CONFIGURACION.html">CONFIGURACION</a>
                    </div>
                </div>

            </div>
        </div>


        




    </section>
  

    <script>
  document.addEventListener('DOMContentLoaded', function() {
    var audio = document.querySelector('audio');
    var savedVolume = localStorage.getItem('volume');
    if (savedVolume !== null) {
      audio.volume = savedVolume;
    }
    audio.play();
  });
</script>


</body>
</html>
