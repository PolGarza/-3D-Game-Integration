<?php
// Incluir el archivo de conexión

include 'LOGIN/conexion.php';


session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["correo"])) {
    exit;
}

// Obtener el ID del usuario y el puntaje de la sesión
$id_usuario = $_SESSION["correo"]["id_user"];

// Verificar si se ha enviado el formulario para insertar puntuación
if (isset($_POST["insertar_puntuacion"])) {

    $puntaje =(int)$_POST["gato1"];
    // Insertar la puntuación en la base de datos
    $conexion = Conexion::conectar();
    $query = "INSERT INTO puntuacion (id_user, jugador_1, jugador_2, fecha_juego) VALUES (:id_usuario, :puntaje, 0, NOW())";
    $resultado = $conexion->prepare($query);
    $resultado->bindValue(":id_usuario", $id_usuario);
    $resultado->bindValue(":puntaje", $puntaje);
    $resultado->execute();

    // Redirigir al usuario o realizar alguna otra acción después de la inserción
    // Aquí puedes redirigir al usuario a otra página si lo deseas
    header("Location: HOME.php");
    exit;
}

// Incluir el archivo de conexión y el controlador de usuario

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>three.js webgl - OBJLoader + MTLLoader</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="STYLES/scene2.css"> <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="STYLES/modal.css">
</head>

<body>

<div id="info">
    <a href="https://threejs.org" target="_blank" rel="noopener">three.js</a> - OBJLoader + MTLLoader
</div>

<div id="coordContainer" style="display: none;">
    <p id="coordText"></p>
    <button id="closeButton">Cerrar</button>
</div>

<!-- Contenedor de la imagen de fondo -->
<div id="backgroundImage"></div>



<!-- Contenedor del contador de zanahorias -->
<div id="counterContainer"> <span id="contador_zanahoria">0</span></div>


<!-- Modal -->
<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"></p>
        
        <!-- Formulario para la inserción de datos -->
        <form action="" method="post">
        <input name="gato1" id="gato1" value="0">
        <a href="#" id="nivel2Button"><button class="rounded-button">NIVEL 2</button></a>
            <button type="submit" name="insertar_puntuacion" class="rounded-button">GUARDAR</button>
        </form>
    </div>
</div>



<audio id="miAudio" autoplay loop preload="auto">
    <source src="STYLES/AUDIO/NORMAL.mp3" type="audio/mpeg">
</audio>

<!-- Segundo Modal -->
<div id="myModal2" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage2"></p>
        
        <!-- Controles de volumen -->
        <div id="volumeControls">
            <input type="range" id="volumeSlider" min="0" max="1" step="0.1" value="1">
        </div>

        <!-- Botón para redirigir a otra pantalla -->
        <button id="redirectButton">SALIR</button>
    </div>
</div>


<button id="miBoton" onclick="mostrarModal2()">Pausa</button>
<style>
    /* Estilos adicionales para el botón */
  
</style>

<audio id="modalSound">
    <source src="STYLES/AUDIO/ENTER.mp3" type="audio/mpeg">
</audio>

<script src="VALIDATIONS/volumen.js"></script>





<script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@v0.155.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.155.0/examples/jsm/"
        }
    }
</script>

<script type="module">

    import * as THREE from 'three';
    import { OBJLoader } from 'three/addons/loaders/OBJLoader.js';
    import { MTLLoader } from 'three/addons/loaders/MTLLoader.js';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

    let camera, scene, renderer;
    let catModel, carrotModel, maisModel, chocolateModel;
    let contador_zanahoria = 0; // Contador de zanahorias
    const movementSpeed = 0.1;
    let elapsedTime = 0; // Variable para controlar el tiempo

    init();

    function init() {
        camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 20);
        camera.position.z = 2.5;

        // scene
        scene = new THREE.Scene();

        // Ambient light
        const ambientLight = new THREE.AmbientLight(0xffccaa, 0.5); // Warm ambient light
        scene.add(ambientLight);

        // Point light
        const pointLight = new THREE.PointLight(0xffddbb, 1, 100); // Warm point light
        pointLight.position.set(0, 3, 0); // Position the light
        scene.add(pointLight);

        // Directional light (sunlight)
        const dirLight = new THREE.DirectionalLight(0xffddbb, 1); // Warm directional light
        dirLight.position.set(5, 10, 5); // Position the light
        scene.add(dirLight);

        // Load cat model
        new MTLLoader().setPath('model/GATOS/').load('Cat.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/GATOS/').load('Cat.obj', function (object) {
                object.position.y = 0;
                object.scale.setScalar(0.05);
                scene.add(object);

                // Save reference to the cat model for movement
                catModel = object;
            });
        });

        // Load second model (Normal House Interior)
        new MTLLoader().setPath('model/TERRENO1/').load('TERRENO01.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/TERRENO1/').load('TERRENO01.obj', function (object) {
        object.position.y = 0;
        object.scale.setScalar(1.0);

                // Desactivar la transparencia del material
                object.traverse((child) => {
                    if (child instanceof THREE.Mesh) {
                        child.material.transparent = false;
                    }
                });

                scene.add(object);
            }, onProgress);
        });

        //SUBIR MODELOS 

    // Load Zanahoria
    new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
	object.position.y = 0;
    object.position.x = -1  ;
    object.position.z =-0.79;
	object.scale.setScalar( 1.2 );
	scene.add( object );
		}, onProgress);
	});

    // Load Zanahoria2
    new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
	object.position.y = 0;
    object.position.x = 3.0;
    object.position.z = 1.90;
	object.scale.setScalar( 1.2 );
	scene.add( object );
		}, onProgress);
	});

    // Load Zanahoria3
    new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
	object.position.y = 0;
    object.position.x = 1.70;
    object.position.z = 5.99;
	object.scale.setScalar( 1.2 );
	scene.add( object );
		}, onProgress);
	});

    // Load Zanahoria4
    new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
	object.position.y = 0;
    object.position.x = 0.40;
    object.position.z = 3.10;
	object.scale.setScalar( 1.2 );
	scene.add( object );
		}, onProgress);
	});

    // Load Zanahoria5
    new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
	object.position.y = 0;
    object.position.x = 0.70;
    object.position.z = -4.40;
	object.scale.setScalar( 1.2 );
	scene.add( object );
		}, onProgress);
	});

    // Load maiz1
    new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function (object) {
                object.position.y = 0;
                object.position.x = -3.60;
                object.position.z = 3.60;
                object.scale.setScalar(1.0);
                scene.add(object);
            }, onProgress);
        });

         // Load maiz2
    new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function (object) {
                object.position.y = 0;
                object.position.x = -4.20;
                object.position.z = -0.20;
                object.scale.setScalar(1.0);
                scene.add(object);
            }, onProgress);
        });

         // Load maiz3
    new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function (object) {
                object.position.y = 0;
                object.position.x = 4.59;
                object.position.z = 1.9;
                object.scale.setScalar(1.0);
                scene.add(object);
            }, onProgress);
        });

         // Load maiz4
    new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function (object) {
                object.position.y = 0;
                object.position.x = 3.29;
                object.position.z = 0.29;
                object.scale.setScalar(1.0);
                scene.add(object);
            }, onProgress);
        });

         // Load maiz5
    new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function (object) {
                object.position.y = 0;
                object.position.x = 1.49;
                object.position.z = 1.59;
                object.scale.setScalar(1.0);
                scene.add(object);
            }, onProgress);
        });

    // Load Lampara1
    new MTLLoader().setPath( 'model/Lampara/' ).load( 'Lampara.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/Lampara/').load('Lampara.obj', function(object){
	object.position.y = -0.1;
    object.position.x = -5.99  ;
    object.position.z =0.5;
	object.scale.setScalar( 0.25 );
	scene.add( object );
		}, onProgress);
	});

    // Load Lampara2
    new MTLLoader().setPath( 'model/Lampara/' ).load( 'Lampara.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/Lampara/').load('Lampara.obj', function(object){
	object.position.y = -0.1;
    object.position.x = -5.39  ;
    object.position.z =5.09;
	object.scale.setScalar( 0.25 );
	scene.add( object );
		}, onProgress);
	});

    // Load Lampara3
    new MTLLoader().setPath( 'model/Lampara/' ).load( 'Lampara.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/Lampara/').load('Lampara.obj', function(object){
	object.position.y = -0.1;
    object.position.x = 4.8  ;
    object.position.z = 4.89;
	object.scale.setScalar( 0.25 );
	scene.add( object );
		}, onProgress);
	});

    // Load Lampara4
    new MTLLoader().setPath( 'model/Lampara/' ).load( 'Lampara.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/Lampara/').load('Lampara.obj', function(object){
	object.position.y = -0.1;
    object.position.x = 4.8;
    object.position.z = -1.30;
	object.scale.setScalar( 0.25 );
	scene.add( object );
		}, onProgress);
	});

    // Load Lampara5
    new MTLLoader().setPath( 'model/Lampara/' ).load( 'Lampara.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/Lampara/').load('Lampara.obj', function(object){
	object.position.y = -0.1;
    object.position.x = 0;
    object.position.z = 6.69;
	object.scale.setScalar( 0.25 );
	scene.add( object );
		}, onProgress);
	});

    // Load Lampara6
    new MTLLoader().setPath( 'model/Lampara/' ).load( 'Lampara.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/Lampara/').load('Lampara.obj', function(object){
	object.position.y = -0.1;
    object.position.x = 0;
    object.position.z = -4.8;
	object.scale.setScalar( 0.25 );
	scene.add( object );
		}, onProgress);
	});

    // Carro de comida
    new MTLLoader().setPath( 'model/FOOD_CART/' ).load( 'FOOD_CART.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/FOOD_CART/').load('FOOD_CART.obj', function(object){
	object.position.y = 0;
    object.position.x = -3.30;
    object.position.z = -2.40;
	object.scale.setScalar( 0.1 );
	scene.add( object );
		}, onProgress);
	});

    // Sillas
    new MTLLoader().setPath( 'model/SILLA/' ).load( 'SILLA1.mtl', function ( materials ) {
	materials.preload();
	new OBJLoader().setMaterials(materials).setPath('model/SILLA/').load('SILLA1.obj', function(object){
	object.position.y = -0.1;
    object.position.x = 0.4;
    object.position.z = 0.79;
	object.scale.setScalar( 0.8 );
	scene.add( object );
		}, onProgress);
	});

        // Load carrot model
        new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
                object.position.y = -0.1;
                object.position.x = -1.3;
                object.position.z = 0.8;
                object.scale.setScalar(1.0);
                scene.add(object);

                // Save reference to the carrot model for collision detection
                carrotModel = object;

                // Animate the carrot
                animateCarrot();
            });
        });

        // Load maize model
        new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function (object) {
                object.position.y = 0;
                object.position.x = 2;
                object.position.z = -2.1;
                object.scale.setScalar(1.0);
                scene.add(object);

                // Save reference to the maize model for collision detection
                maisModel = object;
                animateMais(object);
            }, onProgress);
        });

        // Load chocolate model
        new MTLLoader().setPath('model/Chocolate/').load('Chocolate.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/Chocolate/').load('Chocolate.obj', function (object) {
                object.position.y = 0;
                object.position.x = 2.7;
                object.position.z = -2.1;
                object.scale.setScalar(1.0);
                scene.add(object);

                chocolateModel = object;

                // Call the animateChocolate function after loading the chocolate model
                animateChocolate(object);
            }, onProgress);
        });


                // Load sky model
new MTLLoader().setPath('model/SKYDOM/').load('CIELO1.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/SKYDOM/').load('CIELO1.obj', function (object) {
        object.position.y = -1;
        object.scale.setScalar(15.0);
        
        // Ajustar el material para que emita luz
        const emissiveColor = new THREE.Color(0xffccaa); // Color de emisión (en este caso, un color amarillo tenue)
        const emissiveIntensity = 0.3; // Intensidad de la emisión (ajusta según sea necesario)

        object.traverse((child) => {
            if (child instanceof THREE.Mesh) {
                child.material.emissive = emissiveColor; // Establecer el color de emisión
                child.material.emissiveIntensity = emissiveIntensity; // Establecer la intensidad de emisión
            }
        });
        
        // Asignar un nombre al objeto del cielo
        object.name = 'cielo';
        
        scene.add(object);

        // Llamar a la función animate después de cargar el cielo
        animate();
    }, onProgress);
});

        // Renderer
        renderer = new THREE.WebGLRenderer
        ({ antialias: true });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.minDistance = 2;
        controls.maxDistance = 5;

        window.addEventListener('resize', onWindowResize);

        // Call the animate function after loading the sky
        animate();
    }

    // Keyboard event listener for movement
    document.addEventListener('keydown', function(event) {
        const keyCode = event.code;

        // Get movement direction of the model
        let movementDirection = new THREE.Vector3();

        switch (keyCode) {
            case 'KeyS': // Move forward
                movementDirection.set(0, 0, -1);
                break;
            case 'KeyA': // Move left
                movementDirection.set(-1, 0, 0);
                break;
            case 'KeyW': // Move backward
                movementDirection.set(0, 0, 1);
                break;
            case 'KeyD': // Move right
                movementDirection.set(1, 0, 0);
                break;
            default:
                return; // Do nothing if it's not a movement key
        }

        movementDirection.normalize().multiplyScalar(movementSpeed);

        moveCatModel(movementDirection);
    });

    // Function to move the carrot model within the specified bounds
    function moveCarrotWithinBounds() {
        const minX = -4.4;
        const maxX = -2.7;
        const minZ = 2.1;
        const maxZ = 5.69;

        // Generate random coordinates within the bounds
        let newX = THREE.MathUtils.randFloat(minX, maxX);
        let newZ = THREE.MathUtils.randFloat(minZ, maxZ);

        // Keep Y position at 0
        const newY = 0;

        // Set new position for the carrot
        carrotModel.position.set(newX, newY, newZ);
    }

    // Function to move the cat model
    function moveCatModel(direction) {
        if (!catModel) return;

        // Obtener la nueva posición del modelo del gato
       const newPosition = catModel.position.clone().add(direction);

        // Verificar si la nueva posición está dentro de los límites de la pared
        if (newPosition.x < 7.89 && newPosition.x > -7.89 && newPosition.z < 8.89) {
        // Si la nueva posición está dentro de los límites de la pared, mover el modelo
        catModel.position.add(direction);
        } else {
        // Si la nueva posición estaría fuera de los límites de la pared, no hacer nada
        // Esto evitará que el gato atraviese la pared
        console.log("¡El gato ha chocado contra la pared!");
        }

        // Check collision with carrot
        if (carrotModel && checkCollision(catModel, carrotModel)) {
            // Handle collision with carrot
            handleCollision(carrotModel);
        }

        // Check collision with maize
        if (maisModel && checkCollision(catModel, maisModel)) {
            // Handle collision with maize
            handleCollision(maisModel);
        }

        // Check collision with chocolate
        if (chocolateModel && checkCollision(catModel, chocolateModel)) {
            // Handle collision with chocolate
            handleCollision(chocolateModel);
        }
    }

    // Function to handle collision with carrot, maize, or chocolate
    function handleCollision(model) {
        // Move model within bounds
        moveModelWithinBounds(model);

        // Increment or decrement counter depending on the model
        if (model === carrotModel || model === maisModel) {
            contador_zanahoria++;
        } else if (model === chocolateModel) {
            contador_zanahoria--;
        }

        // Update counter
        updateCounter();
    }

    // Function to update the counter display
// Función para actualizar la visualización del contador
// Función para actualizar la visualización del contador
function updateCounter() {
  // Actualiza el contador en la interfaz
  document.getElementById('contador_zanahoria').innerText = contador_zanahoria;

  // Actualiza el valor del input con el valor del contador
  const gato1 = document.getElementById('gato1');
  gato1.value = contador_zanahoria;

  // Comprueba el valor del contador y muestra la ventana modal
  if (contador_zanahoria === 3) {
    showModal("¡Ganaste!");

    // Muestra el botón NIVEL 2 en el modal
    const nivel2Button = document.getElementById('nivel2Button');
    nivel2Button.style.display = 'block';

    // Agrega un detector de eventos clic al botón NIVEL 2
    nivel2Button.addEventListener('click', function() {
      // Redirecciona a una nueva ventana usando window.open()
      window.open('SEGUNDO_NIVEL_1.php');
    });
  } else if (contador_zanahoria === -1) {
    showModal("¡Perdiste!");

    // Oculta el botón NIVEL 2 en el modal (si existe)
    const nivel2Button = document.getElementById('nivel2Button');
    if (nivel2Button) {
      nivel2Button.style.display = 'none';
    }
  } else {
    // Oculta el botón NIVEL 2 si ya está visible (opcional)
    const nivel2Button = document.getElementById('nivel2Button');
    if (nivel2Button) {
      nivel2Button.style.display = 'none';
    }
  }
}
    // Function to move the carrot, maize, or chocolate model within the specified bounds
    function moveModelWithinBounds(model) {
        // Define bounds for carrot, maize, and chocolate
        let minX, maxX, minZ, maxZ;
        if (model === carrotModel) {
            minX = -4.4;
            maxX = -2.7;
            minZ = 2.1;
            maxZ = 5.69;
        } else if (model === maisModel) {
            minX = -4.4; // Update with appropriate bounds
            maxX = -2.7; // Update with appropriate bounds
            minZ = 2.1;  // Update with appropriate bounds
            maxZ = 5.69; // Update with appropriate bounds
        } else if (model === chocolateModel) {
            minX = 2.0; // Update with appropriate bounds
            maxX = 3.5; // Update with appropriate bounds
            minZ = -3.5; // Update with appropriate bounds
            maxZ = -2.0; // Update with appropriate bounds
        }

        // Generate random coordinates within the bounds
        let newX = THREE.MathUtils.randFloat(minX, maxX);
        let newZ = THREE.MathUtils.randFloat(minZ, maxZ);

        // Keep Y position at 0
        const newY = 0;

        // Set new position for the model
        model.position.set(newX, newY, newZ);
    }

    // Function to check collision between two objects
    function checkCollision(object1, object2) {
        const distance = object1.position.distanceTo(object2.position);
        const collisionThreshold = 0.5; // Adjust as necessary
        return distance < collisionThreshold;
    }

    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }

    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }

    // Function to show loading progress in console
    function onProgress(xhr) {
        if (xhr.lengthComputable) {
            const percentComplete = xhr.loaded / xhr.total * 100;
            console.log(percentComplete.toFixed(2) + '% downloaded');
        }
    }

    // Function to animate the carrot model
    function animateCarrot() {
        // Rotate the carrot about its own axis on the Y axis
        carrotModel.rotation.y += 0.05; // Adjust rotation speed as desired

        // Request the next frame animation
        requestAnimationFrame(animateCarrot);
    }

    // Call the carrot animation function to start the animation
    animateCarrot();

    // Event listener for click on the "P" key
    document.addEventListener('keypress', function(event) {
        if (event.code === 'KeyP') {
            // Get current coordinates of the cat model
            const catPosition = catModel.position.clone();

            // Show the container and coordinates
            document.getElementById('coordText').innerText = `Cat Coordinates: X=${catPosition.x}, Y=${catPosition.y}, Z=${catPosition.z}`;
            document.getElementById('coordContainer').style.display = 'block';
        }
    });

    // Function to animate the chocolate model
    function animateChocolate(model) {
        requestAnimationFrame(function animate() {
            // Rotate the model
            model.rotation.y += 0.01;

            // Oscillate the model up and down along the Y axis
            const delta = 0.005; // Oscillation speed
            const maxY = 0.2; // Maximum Y coordinate
            const minY = 0.0; // Minimum Y coordinate

            // Update elapsed time
            elapsedTime += delta;

            // Calculate new Y coordinate using sine function for oscillation
            const y = Math.sin(elapsedTime) * (maxY
- minY) * 0.5 + (maxY + minY) * 0.5;
            model.position.y = y;

            // Call the animation function again in the next animation frame
            requestAnimationFrame(animate);
        });
    }

    // Function to animate the "Mais" model
    function animateMais(model) {
        requestAnimationFrame(function animate() {
            // Rotate the model
            model.rotation.y += 0.01;

            // Oscillate the model up and down along the Y axis
            const delta = 0.005; // Oscillation speed
            const maxY = 0.2; // Maximum Y coordinate
            const minY = 0.0; // Minimum Y coordinate

            // Update elapsed time
            elapsedTime += delta;

            // Calculate new Y coordinate using sine function for oscillation
            const y = Math.sin(elapsedTime) * (maxY - minY) * 0.5 + (maxY + minY) * 0.5;
            model.position.y = y;

            // Call the animation function again in the next animation frame
            requestAnimationFrame(animate);
        });
    }


    // Función para mostrar la ventana modal con un mensaje específico
function showModal(message) {
    // Mostrar la ventana modal
    const modal = document.getElementById('myModal');
    const modalMessage = document.getElementById('modalMessage');
    modalMessage.innerText = message;
    modal.style.display = 'block';

    // Asignar evento para cerrar la ventana modal al hacer clic en la "X"
    const closeButton = document.getElementsByClassName('close')[0];
    closeButton.onclick = function() {
        modal.style.display = 'none';
    };

    // Cerrar la ventana modal si se hace clic fuera de ella
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }; 
  }


</script>


</script>
</body>
</html>
