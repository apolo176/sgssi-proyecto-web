document.addEventListener("DOMContentLoaded", () => {
  window.createProfileCircle()

  const addButton = document.getElementById("addGame");
  if (addButton) {
    addButton.addEventListener("click", () => {
      window.location.href = "/add_item";
    });
  }

  fetch("/showItems")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error al obtener datos del servidor");
      }
      return response.json();
    })
    .then((data) => {
      const tbody = document.querySelector("#tabla-juegos tbody");
      tbody.innerHTML = "";

      if (data.length === 0) {
        const fila = document.createElement("tr");
        fila.innerHTML = `<td colspan="6">No hay videojuegos disponibles.</td>`;
        tbody.appendChild(fila);
        return;
      }

      data.forEach((juego) => {
        const fila = document.createElement("tr");

        const tdId = document.createElement("td");
        tdId.textContent = juego.id;

        const tdNombre = document.createElement("td");
        const linkNombre = document.createElement("a");
        linkNombre.textContent = juego.nombre;
        linkNombre.href = `/show_item?item=${juego.id}`;
        tdNombre.appendChild(linkNombre);

        const tdFecha = document.createElement("td");
        tdFecha.textContent = juego.fechaLanzamiento;

        const tdPrecio = document.createElement("td");
        tdPrecio.textContent = juego.precioSalida;

        const tdNota = document.createElement("td");
        tdNota.textContent = juego.notaMetacritic;

        const tdAcciones = document.createElement("td");

        const btnVer = document.createElement("a");
        btnVer.href = `/show_item?item=${juego.id}`;
        btnVer.textContent = "👁️";
        btnVer.className = "detalle-btn";

        const btnEditar = document.createElement("a");
        btnEditar.href = `/modify_item?item=${juego.id}`;
        btnEditar.textContent = "✏️";
        btnEditar.className = "detalle-btn";

        const btnBorrar = document.createElement("a");
        btnBorrar.href = `/delete_item?item=${juego.id}`;
        btnBorrar.textContent = "❌";
        btnBorrar.className = "detalle-btn";

        tdAcciones.append(btnVer, btnEditar, btnBorrar);

        fila.append(tdId, tdNombre, tdFecha, tdPrecio, tdNota, tdAcciones);
        tbody.appendChild(fila);
      });
      document.querySelectorAll(".borrar-btn").forEach((btn) => {
        btn.addEventListener("click", async (e) => {
          try {
            const id = e.target.dataset.id;
            const confirmar = confirm("¿Estás seguro de borrar este juego?");
            if (!confirmar) return;

            const response = await fetch(`/doDeleteItem?item=${id}`, {
              method: "DELETE",
            });

            if (!response.ok) {
              throw new Error("Error al eliminar el juego");
            }

            // Recargar la tabla sin recargar toda la página
            e.target.closest("tr").remove();
          } catch (err) {
            console.error("Error al eliminar el juego:", err);
            alert("❌ No se pudo eliminar el juego: " + err.message);
          }
        });
      });
    })
    .catch((err) => {
      const tbody = document.querySelector("#tabla-juegos tbody");
      tbody.innerHTML = `<tr><td colspan="6" style="color:red;">${err.message}</td></tr>`;
    });
});
