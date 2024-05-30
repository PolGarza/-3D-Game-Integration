<?php
// Incluir el archivo de conexión

include 'LOGIN/conexion.php';


session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["correo"])) {
    // El usuario no está autenticado, redirigir a la página de inicio de sesión
 
    exit;
}

// Obtener el ID del usuario y el puntaje de la sesión
$id_usuario = $_SESSION["correo"]["id_user"];


// Verificar si se ha enviado el formulario para insertar puntuación
if (isset($_POST["insertar_puntuacion"])) {

    $cat1Counter=(int)$_POST["gato1"]; 
    $cat2Counter=(int)$_POST["gato2"]; 

    // Insertar la puntuación en la base de datos
    $conexion = Conexion::conectar();
    $query = "INSERT INTO puntuacion (id_user, jugador_1, jugador_2, fecha_juego) VALUES (:id_usuario, :cat1Counter, :cat2Counter, NOW())";
    $resultado = $conexion->prepare($query);
    $resultado->bindValue(":id_usuario", $id_usuario);
    $resultado->bindValue(":cat1Counter", $cat1Counter, PDO::PARAM_INT);
    $resultado->bindValue(":cat2Counter", $cat2Counter, PDO::PARAM_INT);
    $resultado->execute();
    

    // Redirigir al usuario o realizar alguna otra acción después de la inserción
    // Aquí puedes redirigir al usuario a otra página si lo deseas
    header("Location: HOME.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>three.js webgl - OBJLoader + MTLLoader</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="STYLES/modal.css">
    <link rel="stylesheet" href="STYLES/scene2.css"> <!-- Enlace al archivo CSS -->
</head>

<body>
<div id="info">
    <a href="https://threejs.org" target="_blank" rel="noopener">three.js</a> - OBJLoader + MTLLoader
</div>

<div id="coordContainer" style="display: none;">
    <p id="coordText"></p>
    <button id="closeButton">Cerrar</button>
</div>




<!-- Contenedor para el renderizador 1 y sus contadores -->
<div id="container1" style="position: relative;">
    <!-- Renderizador 1 -->
    <canvas id="renderCanvas1"></canvas>

    <!-- Contadores para el renderizador 1 -->
    <div id="info1" style="position: absolute; top: 10px; left: 10px; z-index: 1;">
        <p id="cat1Counter1">Gato 1: 0</p>
        <p id="cat2Counter1">Gato 2: 0</p>
    </div>
</div>

<!-- Contenedor para el renderizador 2 y sus contadores -->
<div id="container2" style="position: relative;">
    <!-- Renderizador 2 -->
    <canvas id="renderCanvas2"></canvas>

    <!-- Contadores para el renderizador 2 -->
    <div id="info2" style="position: absolute; top: 10px; right: 10px; z-index: 1;">
        <p id="cat1Counter2">Gato 1: 0</p>
        <p id="cat2Counter2">Gato 2: 0</p>
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
<script src="VALIDATIONS/volumen.js"></script>



<?php
// Verificar si el usuario está autenticado
if(isset($_SESSION['correo'])) {
    // Si está autenticado, mostrar el nombre de usuario
    echo "<div id='usuarioLogueado'>¡Bienvenido, " . $_SESSION['correo']['nombre_usuario'] . "!</div>";
}
?>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"></p>
        <form action="" method="post">
            <input name="gato1" id="gato1" value="0">
            <input name="gato2" id="gato2" value="0">
            <a href="#" id="nivel2Button"><button class="rounded-button">NIVEL 2</button></a>
            <button type="submit" name="insertar_puntuacion" class="rounded-button">GUARDAR</button>
        </form>
    </div>
</div>

<!-- CSS para el estilo de la ventana modal -->
<style>
  /* Estilo para la ventana modal */

</style>




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
    let secondCamera;
    let renderer1, renderer2;
    let catModel, secondCatModel, carrotModel,  maisModel, chocolateModel;
    let cat1Counter = 0;
    let cat2Counter = 0;
    const movementSpeed = 0.05; // Velocidad reducida
    let elapsedTime = 0; // Variable para controlar el tiempo
    const camararot2 = 0;

    let controls1, controls2; // Definir las variables globalmente

     // Inicializar contadores
    window.cat1Counter = 0;
    window.cat2Counter = 0;

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

        // model
        const onProgress = function (xhr) {
            if (xhr.lengthComputable) {
                const percentComplete = xhr.loaded / xhr.total * 100;
                console.log(percentComplete.toFixed(2) + '% downloaded');
            }
        };

        // Load first cat model
        new MTLLoader().setPath('model/GATOS/').load('Cat.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/GATOS/').load('Cat.obj', function (object) {
                object.position.y = 0;
                object.scale.setScalar(0.05);
                scene.add(object);

                // Save reference to the first cat model for movement
                catModel = object;
            }, onProgress);
        });

        // Load second cat model
        new MTLLoader().setPath('model/GATO2/').load('Cat.mtl', function (materials) {
            materials.preload();
            new OBJLoader().setMaterials(materials).setPath('model/GATO2/').load('Cat.obj', function (object) {
                object.position.y = 0;
                object.position.x = 0.79;
                object.position.z = 0.1;
                object.scale.setScalar(0.05);
                scene.add(object);

                // Save reference to the second cat model for movement
                secondCatModel = object;
            }, onProgress);
        });

        new MTLLoader().setPath('model/Carrot/').load('Carrot.mtl', function (materials) {
         materials.preload();
         new OBJLoader().setMaterials(materials).setPath('model/Carrot/').load('Carrot.obj', function (object) {
        object.position.y = 0;
        object.position.x = -1.3;
        object.position.z = 0.8;
        object.scale.setScalar(1.2);
        scene.add(object);


        // Save reference to the carrot model for collision detection
        carrotModel = object;

        // Animar la zanahoria
        animateCarrot();
       });
      });


// Load maize model

// Load maize model
new MTLLoader().setPath('model/Mais/').load('Mais.mtl', function(materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Mais/').load('Mais.obj', function(object) {
        object.position.y = 0;
        object.position.x = 2;
        object.position.z = -2.1;
        object.scale.setScalar(1.2);
        scene.add(object);

        // Save reference to the maize model for collision detection
        maisModel = object; // Asignar el objeto del modelo a maisModel
        animateMais(object); // Llamar a la función de animación después de agregar el modelo a la escena
    }, onProgress);
});



// Load chocolate model
new MTLLoader().setPath('model/Chocolate/').load('Chocolate.mtl', function (materials) {
    materials.preload();
    new OBJLoader().setMaterials(materials).setPath('model/Chocolate/').load('Chocolate.obj', function (object) {
        object.position.y = 0;
        object.position.x = 2.7;
        object.position.z = -2.1;
        object.scale.setScalar(1.2);
        scene.add(object);

        chocolateModel = object; // Asignar el objeto del modelo a maisModel

        // Call the animateChocolate function after loading the chocolate model
        animateChocolate(object);
    }, onProgress);
});



        // Load second model
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

                // Crear el primer renderizador
                renderer1 = new THREE.WebGLRenderer({ antialias: true });
                renderer1.setPixelRatio(window.devicePixelRatio);
                renderer1.setSize(window.innerWidth / 2, window.innerHeight);
                document.body.appendChild(renderer1.domElement);

                // Crear el segundo renderizador
                renderer2 = new THREE.WebGLRenderer({ antialias: true });
                renderer2.setPixelRatio(window.devicePixelRatio);
                renderer2.setSize(window.innerWidth / 2, window.innerHeight);
                document.body.appendChild(renderer2.domElement);

                // Crear la primera instancia de OrbitControls para la primera cámara
                controls1 = new OrbitControls(camera, renderer1.domElement);
                controls1.minDistance = 2;
                controls1.maxDistance = 5;

                // Crear la segunda cámara
                secondCamera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 20);
                secondCamera.position.set(0, 2.5, 2.5); // Posición de la cámara
                secondCamera.lookAt(secondCatModel.position); // Apuntar hacia el segundo modelo de gato

                // Crear la segunda instancia de OrbitControls para la segunda cámara
                controls2 = new OrbitControls(secondCamera, renderer2.domElement);
                controls2.minDistance = 2;
                controls2.maxDistance = 5;

                // Después de agregar los renderizadores al DOM
                renderer1.domElement.style.position = 'absolute';
                renderer1.domElement.style.top = '0';
                renderer1.domElement.style.left = '0';

                renderer2.domElement.style.position = 'absolute';
                renderer2.domElement.style.top = '0';
                renderer2.domElement.style.left = (window.innerWidth / 2) + 'px'; // Ajusta la posición para el segundo renderizador

                window.addEventListener('resize', () => {
                    // Ajusta el tamaño y la posición de los renderizadores en función del tamaño de la ventana
                    renderer1.setSize(window.innerWidth / 2, window.innerHeight);
                    renderer2.setSize(window.innerWidth / 2, window.innerHeight);
                    renderer2.domElement.style.left = (window.innerWidth / 2) + 'px';
                });

                // Llamar a la función animate después de cargar el cielo
                animate();
            }, onProgress);
        });
    }

    let rotateTimer = null;
    let rotationSpeed = 0.0;

 // Keyboard event listener for movement
 document.addEventListener('keydown', function (event) {
        const keyCode = event.code;

        // Get movement direction of the model
        let movementDirection = new THREE.Vector3();
        let rotationDirection = new THREE.Vector3();

        switch (keyCode) {
            case 'KeyW': // Move first model forward
                movementDirection.set(0, 0, -1);
                moveModel(catModel, movementDirection);
                break;
            case 'KeyA': // Move first model left
                movementDirection.set(-1, 0, 0);
                moveModel(catModel, movementDirection);
                break;
            case 'KeyS': // Move first model backward
                movementDirection.set(0, 0, 1);
                moveModel(catModel, movementDirection);
                break;
            case 'KeyD': // Move first model right
                movementDirection.set(1, 0, 0);
                moveModel(catModel, movementDirection);
                break;
            case 'KeyJ': // Move second model left
                movementDirection.set(-1, 0, 0);
                moveModel(secondCatModel, movementDirection);
                break;
            case 'KeyI': // Move second model forward
                movementDirection.set(0, 0, 1);
                moveModel(secondCatModel, movementDirection);
                break;
            case 'KeyK': // Move second model backward
                movementDirection.set(0, 0, -1);
                moveModel(secondCatModel, movementDirection);
                break;
            case 'KeyL': // Move second model right
                movementDirection.set(1, 0, 0);
                moveModel(secondCatModel, movementDirection);
                break;
            case 'Numpad1': // Numpad 1 (rotate left)
                startRotation(-0.01);
                break;
            case 'Numpad3': // Numpad 3 (rotate right)
                startRotation(0.01);
                break;
            default:
                return; // Do nothing if it's not a movement key
        }

        movementDirection.normalize().multiplyScalar(movementSpeed);
        rotateModel(rotationDirection);
    });

    document.addEventListener('keyup', function (event) {
        const keyCode = event.code;

        switch (keyCode) {
            case 'Numpad1': // Numpad 1 (rotate left)
            case 'Numpad3': // Numpad 3 (rotate right)
                stopRotation();
                break;
            default:
                return; // Do nothing if it's not a rotation key
        }
    });

    // Function to move the model
    function moveModel(model, direction) {
        if (!model) return;

        model.position.add(direction);

        // Check collision with carrot
        if (carrotModel && checkCollision(model, carrotModel)) {
            // If collision detected, move carrot to a random position on the x-axis
            const randomX = Math.random() * 5 - 2.5; // Random value between -2.5 and 2.5
            carrotModel.position.x = randomX;
        }
    }

 

// Function to check collision between models
function checkCollision() {
    // Get bounding spheres of models
    const catBoundingSphere = new THREE.Sphere(catModel.position, 0.05);
    const secondCatBoundingSphere = new THREE.Sphere(secondCatModel.position, 0.05);
    const carrotBoundingSphere = new THREE.Sphere(carrotModel.position, 0.5);
    const maisBoundingSphere = new THREE.Sphere(maisModel.position, 0.5); // Bounding sphere for the maize model
    const chocolateBoundingSphere = new THREE.Sphere(chocolateModel.position, 0.5); // Bounding sphere for the chocolate model

    // Check collision between first cat and carrot
    if (catBoundingSphere.intersectsSphere(carrotBoundingSphere)) {
        console.log("¡El primer gato ha chocado con la zanahoria!");
        window.cat1Counter++; // Incrementar el contador del primer gato
        console.log("Contador del primer gato:", window.cat1Counter); // Console log del contador del primer gato
        updateCounterDisplay(); // Actualizar la visualización del contador
        // Reposition carrot randomly on the x-axis
        const randomX = Math.random() * 10 - 5; // Random number between -5 and 5
        carrotModel.position.x = randomX;
    }

    // Check collision between second cat and carrot
    if (secondCatBoundingSphere.intersectsSphere(carrotBoundingSphere)) {
        console.log("¡El segundo gato ha chocado con la zanahoria!");
        window.cat2Counter++; // Incrementar el contador del segundo gato
        console.log("Contador del segundo gato:", window.cat2Counter); // Console log del contador del segundo gato
        updateCounterDisplay(); // Actualizar la visualización del contador
        // Reposition carrot randomly on the x-axis
        const randomX = Math.random() * 10 - 5; // Random number between -5 and 5
        carrotModel.position.x = randomX;
    }

    // Check collision between first cat and maize
    if (catBoundingSphere.intersectsSphere(maisBoundingSphere)) {
        console.log("¡El primer gato ha chocado con el maíz!");
        window.cat1Counter++; // Incrementar el contador del primer gato
        console.log("Contador del primer gato:", window.cat1Counter); // Console log del contador del primer gato
        updateCounterDisplay(); // Actualizar la visualización del contador
        // Reposition maize randomly on the x-axis
        const randomX = Math.random() * 10 - 5; // Random number between -5 and 5
        maisModel.position.x = randomX;
    }

    // Check collision between second cat and maize
    if (secondCatBoundingSphere.intersectsSphere(maisBoundingSphere)) {
        console.log("¡El segundo gato ha chocado con el maíz!");
        window.cat2Counter++; // Incrementar el contador del segundo gato
        console.log("Contador del segundo gato:", window.cat2Counter); // Console log del contador del segundo gato
        updateCounterDisplay(); // Actualizar la visualización del contador
        // Reposition maize randomly on the x-axis
        const randomX = Math.random() * 10 - 5; // Random number between -5 and 5
        maisModel.position.x = randomX;
    }


       // Check collision between first cat and chocolate
       if (catBoundingSphere.intersectsSphere(chocolateBoundingSphere)) {
        console.log("¡El primer gato ha chocado con el chocolate!");
        window.cat1Counter--; // Decrementar el contador del primer gato
        console.log("Contador del primer gato:", window.cat1Counter); // Console log del contador del primer gato
        updateCounterDisplay(); // Actualizar la visualización del contador
        // Reposition chocolate randomly on the x-axis
        const randomX = Math.random() * 10 - 5; // Random number between -5 and 5
        chocolateModel.position.x = randomX;
    }

    // Check collision between second cat and chocolate
    if (secondCatBoundingSphere.intersectsSphere(chocolateBoundingSphere)) {
        console.log("¡El segundo gato ha chocado con el chocolate!");
        window.cat2Counter--; // Decrementar el contador del segundo gato
        console.log("Contador del segundo gato:", window.cat2Counter); // Console log del contador del segundo gato
        updateCounterDisplay(); // Actualizar la visualización del contador
        // Reposition chocolate randomly on the x-axis
        const randomX = Math.random() * 10 - 5; // Random number between -5 and 5
        chocolateModel.position.x = randomX;
    }


    // Verificar si cat1Counter alcanzó 1 punto
    if (window.cat1Counter === 3 || window.cat1Counter === -1) {
    // Mostrar la ventana modal
    const modal = document.getElementById('myModal');
    modal.style.display = "block";

    // Mensaje a mostrar en la ventana modal
    const modalMessage = document.getElementById('modalMessage');
    if (window.cat1Counter === 3) {
        modalMessage.innerText = "Ganaste 1";

          // Muestra el botón NIVEL 2 en el modal
    const nivel2Button = document.getElementById('nivel2Button');
    nivel2Button.style.display = 'block';

    // Agrega un detector de eventos clic al botón NIVEL 2
    nivel2Button.addEventListener('click', function() {
      // Redirecciona a una nueva ventana usando window.open()
      window.open('SEGUNDO_NIVEL_M_1.php');

    });
    } else if (window.cat1Counter === -1) {
        modalMessage.innerText = "Perdiste 1";

         // Oculta el botón NIVEL 2 en el modal (si existe)
    const nivel2Button = document.getElementById('nivel2Button');
    if (nivel2Button) {
      nivel2Button.style.display = 'none';
    }

    }

    // Función para cerrar la ventana modal
    function closeModal() {
        modal.style.display = "none";
    }

    // Asignar evento de clic al botón cerrar (x)
    const closeBtn = document.getElementsByClassName("close")[0];
    closeBtn.onclick = closeModal;

    // Asignar evento de clic para cerrar la ventana modal al hacer clic fuera de ella
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };
  }


   // Verificar si cat1Counter alcanzó 1 punto
   if (window.cat2Counter === 3 || window.cat2Counter === -1) {
    // Mostrar la ventana modal
    const modal = document.getElementById('myModal');
    modal.style.display = "block";

    // Mensaje a mostrar en la ventana modal
    const modalMessage = document.getElementById('modalMessage');
    if (window.cat2Counter === 3) {
        modalMessage.innerText = "Ganaste 2";


          // Muestra el botón NIVEL 2 en el modal
    const nivel2Button = document.getElementById('nivel2Button');
    nivel2Button.style.display = 'block';

    // Agrega un detector de eventos clic al botón NIVEL 2
    nivel2Button.addEventListener('click', function() {
      // Redirecciona a una nueva ventana usando window.open()
      window.open('SEGUNDO_NIVEL_1.php');
    });

    } else if (window.cat2Counter === -1) {
        modalMessage.innerText = "Perdiste 2";

         // Oculta el botón NIVEL 2 en el modal (si existe)
    const nivel2Button = document.getElementById('nivel2Button');
    if (nivel2Button) {
      nivel2Button.style.display = 'none';
    }
    }

    // Función para cerrar la ventana modal
    function closeModal() {
        modal.style.display = "none";
    }

    // Asignar evento de clic al botón cerrar (x)
    const closeBtn = document.getElementsByClassName("close")[0];
    closeBtn.onclick = closeModal;

    // Asignar evento de clic para cerrar la ventana modal al hacer clic fuera de ella
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };
  }

}

   
// Function to update the counter display
function updateCounterDisplay() {
    // Actualizar la visualización del contador para cada gato
    const cat1CounterElement1 = document.getElementById('cat1Counter1');
    const cat2CounterElement1 = document.getElementById('cat2Counter1');
    const cat1CounterElement2 = document.getElementById('cat1Counter2');
    const cat2CounterElement2 = document.getElementById('cat2Counter2');
    const gato1 = document.getElementById('gato1');
    const gato2 = document.getElementById('gato2');


    if (cat1CounterElement1 && cat2CounterElement1 && cat1CounterElement2 && cat2CounterElement2 && gato1 && gato2) {
        cat1CounterElement1.innerText = `Gato 1: ${window.cat1Counter}`;
        cat2CounterElement1.innerText = `Gato 2: ${window.cat2Counter}`;
        cat1CounterElement2.innerText = `Gato 1: ${window.cat1Counter}`;
        cat2CounterElement2.innerText = `Gato 2: ${window.cat2Counter}`;

        gato1.value = window.cat1Counter ;
        gato2.value = window.cat2Counter;

       
    } else {
        console.error("No se encontraron elementos con los IDs adecuados.");
    }
}

    // Function to rotate the model
    function rotateModel(direction) {
        if (!secondCatModel) return;

        secondCatModel.rotateX(direction.x);
        secondCatModel.rotateY(direction.y);
        secondCatModel.rotateZ(direction.z);
    }

    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }

    function rotateCamera(angleInDegrees) {
        const angleInRadians = angleInDegrees * Math.PI / 180; // Convertir ángulo a radianes
        const pivot = secondCatModel.position.clone(); // Set pivot as the secondCatModel's position
        const distance = 5; // Set distance from pivot to camera

        const x = pivot.x + distance * Math.sin(angleInRadians);
        const z = pivot.z + distance * Math.cos(angleInRadians);

        secondCamera.position.set(x, pivot.y, z);
        secondCamera.lookAt(pivot);
    }

    // Function to start rotation
    function startRotation(speed) {
        rotationSpeed = speed;

        // Start rotating
        if (!rotateTimer) {
            rotateTimer = setInterval(function () {
                rotateCamera(rotationSpeed);
            }, 16); // 60 frames per second
        }
    }

    // Function to stop rotation
    function stopRotation() {
        console.log("Deteniendo la rotación"); // Mensaje de consola para verificar si se ejecuta la función

        rotationSpeed = 0.0;

        // Stop rotating
        if (rotateTimer) {
            clearInterval(rotateTimer);
            rotateTimer = null;
        }
    }

    function animate() {
        requestAnimationFrame(animate);

controls1.update(); // Actualizar los controles de la primera cámara
controls2.update(); // Actualizar los controles de la segunda cámara

renderer1.render(scene, camera); // Renderizar la escena con la primera cámara
renderer2.render(scene, secondCamera); // Renderizar la escena con la segunda cámara
}

// Function to animate the carrot
function animateCarrot() {
requestAnimationFrame(animateCarrot);

// Rotate the carrot
carrotModel.rotation.y += 0.01;

// Oscillate the carrot up and down along the y-axis
const delta = 0.005; // Oscillation speed
const maxY = 0.2; // Maximum y-coordinate
const minY = 0.0; // Minimum y-coordinate

// Update elapsed time
elapsedTime += delta;

checkCollision();


// Calculate new y-coordinate using sine function for oscillation
const y = Math.sin(elapsedTime) * (maxY - minY) * 0.5 + (maxY + minY) * 0.5;
carrotModel.position.y = y;
}


// Function to animate the chocolate model
function animateChocolate(model) {
    requestAnimationFrame(function animate() {
        // Rotar el modelo
        model.rotation.y += 0.01;

        // Oscilar el modelo hacia arriba y abajo a lo largo del eje Y
        const delta = 0.005; // Velocidad de oscilación
        const maxY = 0.2; // Coordenada Y máxima
        const minY = 0.0; // Coordenada Y mínima

        // Actualizar el tiempo transcurrido
        elapsedTime += delta;

        // Calcular la nueva coordenada Y utilizando la función seno para la oscilación
        const y = Math.sin(elapsedTime) * (maxY - minY) * 0.5 + (maxY + minY) * 0.5;
        model.position.y = y;

        // Llamar a la función de animación nuevamente en el siguiente cuadro de animación
        requestAnimationFrame(animate);
    });
}


// Función de animación para el modelo "Mais"
function animateMais(model) {
    requestAnimationFrame(function animate() {
        // Rotar el modelo
        model.rotation.y += 0.01;

        // Oscilar el modelo hacia arriba y abajo a lo largo del eje Y
        const delta = 0.005; // Velocidad de oscilación
        const maxY = 0.2; // Coordenada Y máxima
        const minY = 0.0; // Coordenada Y mínima

        // Actualizar el tiempo transcurrido
        elapsedTime += delta;

        // Calcular la nueva coordenada Y utilizando la función seno para la oscilación
        const y = Math.sin(elapsedTime) * (maxY - minY) * 0.5 + (maxY + minY) * 0.5;
        model.position.y = y;

        // Llamar a la función de animación nuevamente en el siguiente cuadro de animación
        requestAnimationFrame(animate);
    });
}


</script>

</body>
</html>