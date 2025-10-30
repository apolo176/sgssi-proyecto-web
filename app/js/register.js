const editUser = JSON.parse(localStorage.getItem("editUser"));

document.addEventListener("DOMContentLoaded", () => {
  window.createProfileCircle();

  //Buscar el formulario en la página
  const registerForm = document.getElementById("register_form");

  //Asegurarse de que el formulario exista en esta página
  if (registerForm) {
    
    //Añadir el "escuchador" para el evento 'submit'
    registerForm.addEventListener("submit", (event) => {
      event.preventDefault(); 


      submitForm(event.target); 
    });
  }
});

function submitForm(formulario) {
  const datos = new FormData(formulario);

  if (
    !validateNameAndSurname(datos) ||
    !validateDNI(datos) ||
    !validatePassword(datos) ||
    !validatePhone(datos) ||
    !validateBirthDate(datos) ||
    !validateEmail(datos)
  )
    return false; //Si la validación falla, no envía el formulario

  fetch("/doregister", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);
      window.alert(data.message);
      if (data.success) {
        localStorage.setItem("usuario", JSON.stringify(data.user));
        window.location.href = "/";
      }
    })
    .catch((error) => console.error("Error:", error));
}

function validateNameAndSurname(datos) {
  const nombreYapellido = (datos.get("nombreapellido") || "").trim(); //Trim nos permite eliminar los espacios en blanco al inicio y al final del string
  const expresionRegular = /^[a-zA-ZÀ-ÿ]+(\s+[a-zA-ZÀ-ÿ]+)+$/; //Expresion regular que nos permite buscar letras (mayusculas y minusculas) y mínimo 2 caracteres

  if (!expresionRegular.test(nombreYapellido)) {
    //Si el el string que almacena el nombre y apellido no cumple la expresión regular
    window.alert(
      "El nombre y apellido no es válido. Debe contener solo letras y espacios, y tener entre minimo 2 caracteres."
    );
    return false;
  }

  return true;
}

function validateDNI(datos) {
  const dni = datos.get("DNI").trim().toUpperCase(); //el uppercase convierte las letras a mayusculas
  const expresionRegular = /^\d{8}-[A-Z]$/; //Expresion regular que nos permite buscar 8 números seguidos de una letra (mayuscula) con un guion
  const letrasDNI = "TRWAGMYFPDXBNJZSQVHLCKE"; //String que contiene las letras del DNI en el orden correcto

  if (!expresionRegular.test(dni)) {
    //Si el el string que almacena el DNI no cumple la expresión regular
    window.alert(
      "El DNI no es válido. Debe tener 8 números seguidos de un guion y una letra mayúscula (por ejemplo, 12345678-A)."
    );
    return false;
  }

  const numeroDNI = parseInt(dni.substring(0, 8)); //Obtiene los 8 primeros caracteres del DNI y los convierte en un número entero
  const letraDNI = dni.charAt(9); //Obtiene la letra del DNI
  const letraCorrecta = letrasDNI.charAt(numeroDNI % 23); //Obtiene la letra correcta del DNI a partir del número

  if (letraDNI !== letraCorrecta) {
    window.alert("El DNI no es válido. Por favor, introduce un DNI correcto.");
    return false;
  }

  return true;
}

function validatePassword(datos) {
  const password = (datos.get("password") || "").trim();

  if (!password) {
    window.alert("La contraseña no puede estar vacía.");
    return false;
  }

  if (password.length < 6) {
    window.alert("La contraseña debe tener al menos 6 caracteres.");
    return false;
  }

  return true;
}

function validatePhone(datos) {
  const telefono = (datos.get("telefono") || "").trim();
  const expresionRegular = /^\d{9}$/; //Expresion regular que nos permite buscar 9 números

  if (!telefono) {
    window.alert("El número de teléfono no puede estar vacío.");
    return false;
  }

  if (!expresionRegular.test(telefono)) {
    window.alert(
      "El número de teléfono no es válido. Debe contener exactamente 9 dígitos numéricos."
    );
    return false;
  }

  return true;
}

function validateBirthDate(datos) {
  const fechaNacimiento = (datos.get("fechanac") || "").trim();
  const expresionRegular = /^\d{4}-\d{2}-\d{2}$/; //Expresion regular que nos permite buscar fechas en formato aaaa-mm-dd

  if (!expresionRegular.test(fechaNacimiento)) {
    window.alert(
      "La fecha de nacimiento no es válida. Debe tener el formato aaaa-mm-dd."
    );
    return false;
  }

  const [anio, mes, dia] = fechaNacimiento.split("-").map(Number);
  const fecha = new Date(anio, mes - 1, dia);

  // Comprobamos que la fecha exista realmente
  if (
    fecha.getFullYear() !== anio ||
    fecha.getMonth() + 1 !== mes ||
    fecha.getDate() !== dia
  ) {
    alert("La fecha de nacimiento no existe.");
    return false;
  }
  return true;
}

function validateEmail(datos) {
  const email = datos.get("email").trim();
  const expresionRegular = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/; //Expresion regular que nos permite buscar un email válido (minimo 5 caracteres e@e.c)

  if (!email) {
    window.alert("El email no puede estar vacío.");
    return false;
  }

  if (!expresionRegular.test(email)) {
    window.alert("El email no es válido.");
    return false;
  }

  return true;
}
