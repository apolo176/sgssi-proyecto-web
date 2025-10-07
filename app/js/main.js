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

function submitForm(form)
{
    console.log("hey")
    const data = new FormData(form)
    const datos = Object.fromEntries(data.entries());
    console.log(datos);

     validateForm(data)
}

function validateForm(data){
    console.log(Object.fromEntries(data))
}
