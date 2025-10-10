document.addEventListener('DOMContentLoaded', () => {
    // Obtener id del query string
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (!id) return;

    fetch(`/app/controllers/DetalleJuegoController.php?id=${id}`) //Llama al controlador PHP
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#tabla-detalle tbody');
            tbody.innerHTML = '';

            if (!data) {
                tbody.innerHTML = '<tr><td colspan="6">Juego no encontrado</td></tr>';
                return;
            }

            const fila = document.createElement('tr'); // Rellena las filas con los datos del juego
            fila.innerHTML = `
                <td>${data.id}</td>
                <td>${data.nombre}</td>
                <td>${data.genero}</td>
                <td>${data.fechaLanzamiento}</td>
                <td>${parseFloat(data.precioSalida).toFixed(2)} €</td>
                <td>${data.notaMetacritic}</td>
            `;
            tbody.appendChild(fila);
        })
        .catch(err => {
            const tbody = document.querySelector('#tabla-detalle tbody');
            tbody.innerHTML = `<tr><td colspan="6" style="color:red;">${err.message}</td></tr>`;
        });
});
