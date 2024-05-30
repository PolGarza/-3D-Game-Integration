document.addEventListener("DOMContentLoaded", function () {
    var loginForm = document.getElementById("loginForm");
    var submitButton = document.querySelector(".btn-admin");
  
    loginForm.addEventListener("submit", function (event) {
      event.preventDefault();
  
      submitButton.disabled = true;
      submitButton.innerText = "Enviando...";
  
      var formData = new FormData(loginForm);
      var pass = formData.get("pass");
      var correo = formData.get("correo");
  
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "LOGIN/validar_login.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          submitButton.disabled = false;
          submitButton.innerText = "Ingresar";
  
          try {
            var response = JSON.parse(xhr.responseText);
            if (xhr.status === 200) {
              if (response.success) {
                alert("Inicio de sesión exitoso");
                window.location.href = "HOME.php";
              } else {
                alert(response.error_message || "Usuario no encontrado");
              }
            } else if (xhr.status === 400) {
              alert("Error en la clave: " + response.error_message);
            }
          } catch (e) {
            console.error("Error parsing JSON:", xhr.responseText);
            alert("Ocurrió un error. Por favor, intenta nuevamente.");
          }
        }
      };
  
      var data = "pass=" + encodeURIComponent(pass) + "&correo=" + encodeURIComponent(correo);
      xhr.send(data);
    });
  });
  