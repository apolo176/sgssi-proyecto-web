document.addEventListener("DOMContentLoaded", () => {
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
        fila.innerHTML = `
          <td>${juego.id}</td>
          <td><a href="/show_item?item=${juego.id}">${juego.nombre}</a></td>
          <td>${juego.fechaLanzamiento}</td>
          <td>${juego.precioSalida}</td>
          <td>${juego.notaMetacritic}</td>
          <td>
            <a href="/show_item?item=${juego.id}" class="detalle-btn">👁️</a>
            <a href="/modify_item?item=${juego.id}" class="detalle-btn">✏️</a>
            <button class="detalle-btn borrar-btn" data-id="${juego.id}">❌</button>
          </td>`;
        tbody.appendChild(fila);
      });

      // ✅ Agregar los listeners una vez creadas las filas
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

  // Si existe tu función de perfil
  window.createProfileCircle?.();
});
