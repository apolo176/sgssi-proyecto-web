document.addEventListener('DOMContentLoaded', () => {
    fetch('/app/controllers/VideogameController.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener datos del servidor');
            }
            return response.json(); // convierte el JSON del PHP a un array JS
        })
        .then(data => {
            const tbody = document.querySelector('#tabla-juegos tbody');
            tbody.innerHTML = ''; // limpia el contenido por si había algo

            if (data.length === 0) {
                const fila = document.createElement('tr');
                fila.innerHTML = `<td colspan="6">No hay videojuegos disponibles.</td>`;
                tbody.appendChild(fila);
                return;
            }

            data.forEach(juego => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${juego.id}</td>
                    <td>
                        <a href="detalleJuego.html?id=${juego.id}">${juego.nombre}</a>
                    </td>
                `;
                tbody.appendChild(fila);
            });
        })
        .catch(err => {
            const tbody = document.querySelector('#tabla-juegos tbody');
            tbody.innerHTML = `<tr><td colspan="6" style="color:red;">${err.message}</td></tr>`;
        });
});
