
function submitForm(formulario) {
    const datos = new FormData(formulario)

    validacion = validateNameAndSurname(datos) //Comprueba que el nombre y apellido son válidos

    if (!validacion) {
        return false; //Si la validación falla, no envía el formulario
    }
    //Mismo tipo de validacion que el anterior pero con los demás campos
    validacion = validateEmail(datos) 

    if (!validacion) {
        return false; 
    }

    fetch('LoginController.php', {
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
