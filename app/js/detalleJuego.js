document.addEventListener("DOMContentLoaded", () => {
    window.createProfileCircle()

  // Obtener id del query string
  const params = new URLSearchParams(window.location.search);
  const id = params.get("item");
  if (!id) return;

fetch(`/getItemData?item=${id}`)
  .then((res) => res.json())
  .then((data) => {
    const container = document.getElementById("game-detail");

    // Limpia contenido previo
    container.innerHTML = "";

    if (!data) {
      const p = document.createElement("p");
      p.textContent = "Juego no encontrado";
      container.appendChild(p);
      return;
    }
 
    const h2 = document.createElement("h2");
    h2.textContent = data.nombre;

    const genero = document.createElement("p");
    genero.innerHTML = `<strong>Género:</strong> `;
    const generoSpan = document.createElement("span");
    generoSpan.textContent = data.genero;
    genero.appendChild(generoSpan);

    const fecha = document.createElement("p");
    fecha.innerHTML = `<strong>Lanzamiento:</strong> `;
    const fechaSpan = document.createElement("span");
    fechaSpan.textContent = data.fechaLanzamiento;
    fecha.appendChild(fechaSpan);

    const precio = document.createElement("p");
    precio.innerHTML = `<strong>Precio:</strong> €`;
    const precioSpan = document.createElement("span");
    precioSpan.textContent = data.precioSalida;
    precio.appendChild(precioSpan);

    const nota = document.createElement("p");
    nota.innerHTML = `<strong>Nota Metacritic:</strong> `;
    const notaSpan = document.createElement("span");
    notaSpan.textContent = data.notaMetacritic;
    nota.appendChild(notaSpan);

    const btn = document.createElement("a");
    btn.href = `/modify_item?item=${data.id}`;
    btn.className = "btn modify";
    btn.textContent = "Modificar ✏️";

    container.append(h2, genero, fecha, precio, nota, btn);
  })
  .catch((err) => {
    const container = document.getElementById("game-detail");
    container.textContent = "Error: " + err.message;
  });
  window.createProfileCircle();
});
