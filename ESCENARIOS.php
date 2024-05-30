<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="STYLES/Escenarios2.css">
  <link rel="shortcut icon" href="IMAGES/LOGO.png" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Itim&family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
  <title>Michiventuras</title>
</head>

<body>

    <audio id="backgroundAudio" autoplay loop>
        <source src="STYLES/AUDIO/MEOW.mp3" type="audio/mpeg">
    </audio>

    <!-- Sonido de clic oculto -->
    <audio id="clickSound">
        <source src="STYLES/AUDIO/ENTER.mp3" type="audio/mpeg">
    </audio>

    <section class="Recomendaciones">
  
        <div class="contenedor">
            <div class="galeria_pub">
            <a href="HOME.php"><button>Volver</button></a>
                <!-- Aquí puedes mostrar el nombre de usuario si está autenticado -->
                <?php
                session_start();
                if(isset($_SESSION['correo'])) {
                    echo "<div class='usuarioLogueado'>¡Bienvenido, " . $_SESSION['correo']['nombre_usuario'] . "!</div>";
                }
                ?>

                <div class="imagen-port" data-url="PRIMER_NIVEL_1.php">
                    <img src="STYLES/IMAGES/ZANAHORIA.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="PRIMER_NIVEL_1.php">NORMAL</a>
                    </div>
                </div>

                <div class="imagen-port" data-url="NIVEL_ESPECIAL_1.php">
                    <img src="STYLES/IMAGES/HALLOWEN.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="NIVEL_ESPECIAL_1.php">HALLOWEN</a>
                    </div>
                </div>

                <div class="imagen-port" data-url="NIVEL_ESPECIAL_2.php">
                    <img src="STYLES/IMAGES/GROGU.png" alt="" class="imagen-about-us">
                    <div class="hover-galeria">
                        <img src="STYLES/IMAGES/GATITO.png" alt="" class="imagen-about-us">
                        <a href="NIVEL_ESPECIAL_2.php">STAR WARS</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var backgroundAudio = document.getElementById('backgroundAudio');
            var savedVolume = localStorage.getItem('volume');
            if (savedVolume !== null) {
                backgroundAudio.volume = savedVolume;
            }
            backgroundAudio.play();

            var clickSound = document.getElementById('clickSound');
            var imagenPorts = document.querySelectorAll('.imagen-port');

            imagenPorts.forEach(function(port) {
                port.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevenir la redirección inmediata
                    var url = port.getAttribute('data-url');
                    clickSound.play();

                    clickSound.onended = function() {
                        window.location.href = url;
                    };
                });
            });
        });
    </script>

</body>
</html>

