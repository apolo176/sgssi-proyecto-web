
function submitForm(formulario) {
    const datos = new FormData(formulario)

    //Mismo tipo de validacion que el anterior pero con los demás campos

    validacion = validateLaunchDate(datos)

    if (!validacion) {
        return false; 
    }

    validacion = validatePrice(datos) 

    if (!validacion) {
        return false;
    }

    validacion = validateMetacriticScore(datos)

    if (!validacion) {
        return false;
    }

    fetch('/app/controllers/AnadirJuegoController.php', {
        method: 'POST',
        body: datos
    })
    .then(response => response.text())
    .then(data => {
        window.alert(data); // Mensaje del PHP (por ejemplo, "Registro hecho correctamente")
    })
    .catch(error => console.error('Error:', error));
}


function validateLaunchDate(datos) {
    const fechaLanzamiento = datos.get('fechaLanzamiento').trim();
    const expresionRegular = /^\d{4}-\d{2}-\d{2}$/; //Expresion regular que nos permite buscar fechas en formato aaaa-mm-dd

    if (!expresionRegular.test(fechaLanzamiento)) {
        window.alert('La fecha de lanzamiento no es válida. Debe tener el formato aaaa-mm-dd.');
        return false;
    }
    else {
        return true;
    }
}

function validatePrice(datos) {
    const precio = datos.get('precioSalida').trim();
    const expresionRegular = /^\d+(\.\d{1,2})?$/; //Expresion regular que nos permite buscar un precio válido

    if (!expresionRegular.test(precio)) {
        window.alert('El precio de salida no es válido. Debe ser un número positivo con hasta dos decimales.');
        return false;
    }
    else {
        return true;
    }
}

function validateMetacriticScore(datos) {
    const notaMetacritic = datos.get('notaMetacritic').trim();
    const expresionRegular = /^(10(\.0{1,2})?|([0-9](\.[0-9]{1,2})?))$/;     // Expresión regular para número entre 0 y 10 con hasta 2 decimales

    if (!expresionRegular.test(notaMetacritic)) {
        window.alert('La nota Metacritic no es válida. Debe ser un número entre 0 y 10, con máximo 2 decimales.');
        return false;
    } else {
        return true; 
    }  
}
