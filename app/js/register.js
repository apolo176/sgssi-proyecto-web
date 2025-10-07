window.addEventListener('DOMContentLoaded', login);

function login() {
    fetch('register.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#usersTable tbody');
            tbody.innerHTML = ''; // Limpiar tabla
            data.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${user.id}</td><td>${user.nombre}</td>`;
                tbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error:', error));
}

function submitForm(formulario) {
    const datos = new FormData(formulario)

    validacion = validateNameAndSurname(datos) //Comprueba que el nombre y apellido son válidos

    if (!validacion) {
        return false; //Si la validación falla, no envía el formulario
    }
    //Mismo tipo de validacion que el anterior pero con los demás campos
    validacion = validateDNI(datos) 

    if (!validacion) {
        return false; 
    }

    validacion = validatePhone(datos)

    if (!validacion) {
        return false; 
    }

    validacion = validateBirthDate(datos)

    if (!validacion) {
        return false; 
    }

    validacion = validateEmail(datos) 

    if (!validacion) {
        return false;
    }
}

function validateNameAndSurname(datos) {
    const nombreYapellido = datos.get('nombreapellido').trim(); //Trim nos permite eliminar los espacios en blanco al inicio y al final del string
    const expresionRegular = /^[a-zA-ZÀ-ÿ]{1,}\s[a-zA-ZÀ-ÿ]{1,}$/; //Expresion regular que nos permite buscar letras (mayusculas y minusculas) y mínimo 2 caracteres

    if (!expresionRegular.test(nombreYapellido)) { //Si el el string que almacena el nombre y apellido no cumple la expresión regular
        window.alert('El nombre y apellido no es válido. Debe contener solo letras y espacios, y tener entre minimo 2 caracteres.');
        return false;
    } else {
        return true;
    }
}

function validateDNI(datos) {
    const dni = datos.get('dni').trim().toUpperCase(); //el uppercase convierte las letras a mayusculas
    const expresionRegular = /^\d{8}-[A-Z]$/; //Expresion regular que nos permite buscar 8 números seguidos de una letra (mayuscula) con un guion
    const letrasDNI = 'TRWAGMYFPDXBNJZSQVHLCKE'; //String que contiene las letras del DNI en el orden correcto

    if (!expresionRegular.test(dni)) { //Si el el string que almacena el DNI no cumple la expresión regular
        window.alert('El DNI no es válido. Debe tener 8 números seguidos de un guion y una letra mayúscula (por ejemplo, 12345678-A).');
        return false;
    } else {
        const numeroDNI = parseInt(dni.substring(0, 8)); //Obtiene los 8 primeros caracteres del DNI y los convierte en un número entero
        const letraDNI = dni.charAt(9); //Obtiene la letra del DNI
        const letraCorrecta = letrasDNI.charAt(numeroDNI % 23); //Obtiene la letra correcta del DNI a partir del número

        if (letraDNI !== letraCorrecta) { 
            window.alert('El DNI no es válido. Por favor, introduce un DNI correcto.');
            return false;
        } else {
            return true;
        }
    }
}

function validatePhone(datos) {
    const telefono = datos.get('telefono').trim();
    const expresionRegular = /^\d{9}$/; //Expresion regular que nos permite buscar 9 números
    
    if (!expresionRegular.test(telefono)) {
        window.alert('El número de teléfono no es válido. Debe contener exactamente 9 dígitos numéricos.');
        return false;
    } else {
        return true; 
    }  
}

function validateBirthDate(datos) {
    const fechaNacimiento = datos.get('fechanacimiento').trim();
    const expresionRegular = /^\d{4}-\d{2}-\d{2}$/; //Expresion regular que nos permite buscar fechas en formato aaaa-mm-dd

    if (!expresionRegular.test(fechaNacimiento)) {
        window.alert('La fecha de nacimiento no es válida. Debe tener el formato aaaa-mm-dd.');
        return false;
    }
    else {
        return true;
    }
}
function validateEmail(datos) {
    const email = datos.get('email').trim();
    const expresionRegular = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{5,}$/; //Expresion regular que nos permite buscar un email válido (minimo 5 caracteres e@e.c)

    if (!expresionRegular.test(email)) { 
        window.alert('El email no es válido.');
        return false;
    } 
    else {
        return true;
    }
}
