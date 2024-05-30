// Validación de nombre completo
document.addEventListener("DOMContentLoaded", function () {
    var nombreCompletoInput = document.getElementById("nombre_completo");
    var mensajeErrorNombreCompleto = document.getElementById("mensaje-error-nombre-completo");

    nombreCompletoInput.addEventListener("focus", function () {
        mensajeErrorNombreCompleto.style.display = "none";
    });

    nombreCompletoInput.addEventListener("blur", function () {
        var valorCampo = nombreCompletoInput.value.trim();
        if (!/^[A-Za-z\s]+$/.test(valorCampo)) {
            mensajeErrorNombreCompleto.style.display = "block";
        }
    });

    var formulario = document.querySelector("form");
    formulario.addEventListener("submit", function (event) {
        var valorCampo = nombreCompletoInput.value.trim();
        if (!/^[A-Za-z\s]+$/.test(valorCampo)) {
            mensajeErrorNombreCompleto.style.display = "block";
            nombreCompletoInput.focus();
            event.preventDefault();
        }
    });
});

// Validación de nombre de usuario
document.addEventListener("DOMContentLoaded", function () {
    var nombreUsuarioInput = document.getElementById("nombre_usuario");
    var mensajeErrorUsuario = document.getElementById("mensaje-error-usuario");

    nombreUsuarioInput.addEventListener("focus", function () {
        mensajeErrorUsuario.style.display = "none";
    });

    nombreUsuarioInput.addEventListener("blur", function () {
        var valorCampo = nombreUsuarioInput.value.trim();
        if (valorCampo.length < 5) {
            mensajeErrorUsuario.style.display = "block";
        }
    });

    var formulario = document.querySelector("form");
    formulario.addEventListener("submit", function (event) {
        var valorCampo = nombreUsuarioInput.value.trim();
        if (valorCampo.length < 5) {
            mensajeErrorUsuario.style.display = "block";
            nombreUsuarioInput.focus();
            event.preventDefault();
        }
    });
});

// Validación de correo electrónico
document.addEventListener("DOMContentLoaded", function () {
    var correoInput = document.getElementById("correo");
    var mensajeErrorCorreo = document.getElementById("mensaje-error-correo");

    correoInput.addEventListener("focus", function () {
        mensajeErrorCorreo.style.display = "none";
    });

    correoInput.addEventListener("blur", function () {
        var valorCampo = correoInput.value.trim();
        var patronCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!patronCorreo.test(valorCampo)) {
            mensajeErrorCorreo.style.display = "block";
        }
    });

    var formulario = document.querySelector("form");
    formulario.addEventListener("submit", function (event) {
        var valorCampo = correoInput.value.trim();
        var patronCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!patronCorreo.test(valorCampo)) {
            mensajeErrorCorreo.style.display = "block";
            correoInput.focus();
            event.preventDefault();
        }
    });
});

// Validación de confirmación de contraseña
document.addEventListener("DOMContentLoaded", function () {
    var confirmarPassInput = document.getElementById("confirmar_pass");
    var mensajeErrorConfirmarPass = document.getElementById("mensaje-error-confirmar-pass");

    confirmarPassInput.addEventListener("focus", function () {
        mensajeErrorConfirmarPass.style.display = "none";
    });

    confirmarPassInput.addEventListener("blur", function () {
        var valorCampo = confirmarPassInput.value.trim();
        if (valorCampo.length < 8) {
            mensajeErrorConfirmarPass.style.display = "block";
        }
    });

    var formulario = document.querySelector("form");
    formulario.addEventListener("submit", function (event) {
        var valorCampo = confirmarPassInput.value.trim();
        if (valorCampo.length < 8) {
            mensajeErrorConfirmarPass.style.display = "block";
            confirmarPassInput.focus();
            event.preventDefault();
        }
    });
});
