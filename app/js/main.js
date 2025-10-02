window.addEventListener('DOMContentLoaded', listUsers);

function listUsers() {
    fetch('users.php')
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

