const params = new URLSearchParams(window.location.search);
const id = params.get("item");
let editGame;

async function fetchGame() {
  editGame = await fetch(`/getItemData?item=${id}`, {
    method: "GET",
  })
    .then((response) => response.text())
    .then((data) => {
      return JSON.parse(data)
    })
    .catch((error) => console.error("Error:", error));
}

document.addEventListener("DOMContentLoaded", async () => {
  window.createProfileCircle()
  await fetchGame()
  const form = document.getElementById("item_modify_form");
  form.nombre.value = editGame.nombre;
  form.genero.value = editGame.genero;
  //form.password.value = editUser.password; // opcional, o dejar vacío
  form.precioSalida.value = editGame.precioSalida;
  form.fechaLanzamiento.value = editGame.fechaLanzamiento;
  form.notaMetacritic.value = editGame.notaMetacritic;

  //Asignar el evento 'submit' al formulario
  form.addEventListener('submit', (event) => {
  event.preventDefault();
  submitForm(event.target); // event.target es el formulario

  });
});
function submitForm(formulario) {
  const datos = new FormData(formulario);

  //Mismo tipo de validacion que el anterior pero con los demás campos

  if (
    !validateLaunchDate(datos) ||
    !validatePrice(datos) ||
    !validateMetacriticScore(datos)
  )
    return false;
  const cambios = {};
  datos.forEach((value, key) => {
    // Solo añadimos si cambia
    if (value && editGame[key] !== value) {
      cambios[key] = value;
    }
  });
  cambios.id = editGame.id;
  if (Object.keys(cambios).length < 2) {
    window.alert("Haz algun cambio");
    return false;
  }
  fetch("/modifyItem", {
    method: "POST",
    body: JSON.stringify(cambios),
  })
    .then((response) => response.text())
    .then((data) => {
      window.alert(data); // Mensaje del PHP (por ejemplo, "Registro hecho correctamente")
      window.location.href = "/items";
    })
    .catch((error) => console.error("Error:", error));
}

function validateLaunchDate(datos) {
  const fechaLanzamiento = (datos.get("fechaLanzamiento") || "").trim();
  const expresionRegular = /^\d{4}-\d{2}-\d{2}$/; //Expresion regular que nos permite buscar fechas en formato aaaa-mm-dd

  if (!expresionRegular.test(fechaLanzamiento)) {
    window.alert(
      "La fecha de lanzamiento no es válida. Debe tener el formato aaaa-mm-dd."
    );
    return false;
  }

  const [anio, mes, dia] = fechaLanzamiento.split("-").map(Number); //Separa en 3 variables con desestructuración cada parte de la fecha
  const fecha = new Date(anio, mes - 1, dia);

  if (
    fecha.getFullYear() !== anio ||
    fecha.getMonth() + 1 !== mes /*+1 porque los meses van de 0 a 11 en JS */ ||
    fecha.getDate() !== dia
  ) {
    //Comprueba que la fecha válida automáticamente por JS y la que introduce el usuario son válidas
    alert("La fecha de lanzamiento no es válida.");
    return false;
  }

  return true;
}

function validatePrice(datos) {
  const precio = (datos.get("precioSalida") || "").trim();

  if (!precio) {
    window.alert("El precio de salida no puede estar vacío.");
    return false;
  }

  const expresionRegular = /^(?:0|[1-9]\d*)(?:\.\d{1,2})?$/; //Expresion regular que nos permite buscar un precio válido

  if (!expresionRegular.test(precio)) {
    window.alert(
      "El precio de salida no es válido. Debe ser un número positivo con hasta dos decimales."
    );
    return false;
  }

  return true;
}

function validateMetacriticScore(datos) {
  const notaMetacritic = (datos.get("notaMetacritic") || "").trim();

  if (!notaMetacritic) {
    window.alert("La nota Metacritic no puede estar vacía.");
    return false;
  }

  const expresionRegular = /^(10(\.0{1,2})?|([0-9](\.[0-9]{1,2})?))$/; // Expresión regular para número entre 0 y 10 con hasta 2 decimales

  if (!expresionRegular.test(notaMetacritic)) {
    window.alert(
      "La nota Metacritic no es válida. Debe ser un número entre 0 y 10, con máximo 2 decimales."
    );
    return false;
  }

  return true;
}
