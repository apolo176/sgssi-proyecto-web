document.addEventListener('DOMContentLoaded', () => {
    // Obtener id del query string
    const params = new URLSearchParams(window.location.search);
    const id = params.get('item');
    if (!id) return;

    fetch(`/getItemData?item=${id}`) //Llama al controlador PHP
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("game-detail");

            if (!data) {
                container.innerHTML = 'Juego no encontrado';
                return;
            }


            container.innerHTML = `
                <h2>${data.nombre}</h2>
                <p><strong>Género:</strong> ${data.genero}</p>
                <p><strong>Lanzamiento:</strong> ${data.fechaLanzamiento}</p>
                <p><strong>Precio:</strong> €${data.precioSalida}</p>
                <p><strong>Nota Metacritic:</strong> ${data.notaMetacritic}</p>
                <a href="/modify_item?item=${data.id}" class="btn modify">Modificar ✏️</a>
            `;

        })
        .catch(err => {
            const container = document.getElementById("game-detail");

            container.innerHTML = err.message;
        });
                window.createProfileCircle()

});
