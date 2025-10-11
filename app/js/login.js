
function submitForm(formulario) {

    const datos = new FormData(formulario)

    //Mismo tipo de validacion que el anterior pero con el email
    validacion = validateEmail(datos) 

    if (!validacion) {
        return false; 
    }
    validacion = validatePassword(datos) 

    if (!validacion) {
        return false; 
    }

    fetch('/login', {
        method: 'POST',
        body: datos
    })
    .then(response => response.text())
    .then(data => {
        window.alert(data); // Mensaje del PHP (por ejemplo, "Registro hecho correctamente")
    })
    .catch(error => console.error('Error:', error));
}

function validateEmail(datos) {
    const email = datos.get('email').trim();
    const expresionRegular = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/; //Expresion regular que nos permite buscar un email válido (minimo 5 caracteres e@e.c)

    if (!expresionRegular.test(email)) { 
        window.alert('El email no es válido.');
        return false;
    } 
    else {
        return true;
    }
}
function validatePassword(datos) {
    const password = datos.get('password').trim();

    if (password === "") {
        window.alert('La contraseña no puede estar vacía.');
        return false;
    } else {
        return true;
    }
}

