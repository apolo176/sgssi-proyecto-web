document.addEventListener("DOMContentLoaded", () => {
  window.createProfileCircle();

  //Buscar el formulario en la página
  const loginForm = document.getElementById("login_form");

  //Asegurarse de que el formulario exista en esta página
  if (loginForm) {
    
    //Añadir el "escuchador" para el evento 'submit'
    loginForm.addEventListener("submit", (event) => {
      event.preventDefault(); 
      submitForm(event.target); 
    });
  }
});


function submitForm(formulario) {
  const datos = new FormData(formulario);

  if (!validateEmail(datos) || !validatePassword(datos)) return false; //Si no pasa las validaciones, no se envía el formulario

  fetch("/dologin", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.text())
    .then((rawData) => {
      data = JSON.parse(rawData);
      if (data.success) {
        // Guardar al usuario en localStorage
        localStorage.setItem("usuario", JSON.stringify(data.user));
        window.alert(`Bienvenido, ${data.user.nombre}`);

        // Redirigir a la home
        window.location.href = "/";
      } else {
        window.alert("Email o contraseña incorrectos");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function validateEmail(datos) { //Método para validar el email
  const email = (datos.get("email") || "").trim();

  const expresionRegular = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/; //Expresion regular que nos permite buscar un email válido

  if (!expresionRegular.test(email)) {
    window.alert("El email no es válido.");
    return false;
  }

  return true;
}

function validatePassword(datos) { //Método para validar la contraseña
  const password = (datos.get("password") || "").trim();

  if (password === "") {
    window.alert("La contraseña no puede estar vacía.");
    return false;
  }

  return true;
}
