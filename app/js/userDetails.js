document.addEventListener("DOMContentLoaded", () => {
  window.createProfileCircle();

  const userCard = document.getElementById("user-detail");
  const params = new URLSearchParams(window.location.search);
  const id = params.get("user");
  if (!id) return;

  // 1️⃣ Intentamos obtener usuario del localStorage
  const localUser = JSON.parse(localStorage.getItem("usuario"));

  // 2️⃣ Si NO hay usuario logueado, no dejamos avanzar
  if (!localUser) {
    userCard.innerHTML = `
      <p>No hay ningún usuario logueado.</p>
      <a href="/login" class="btn view">Iniciar sesión</a>
    `;
    return;
  }

  // 3️⃣ Verificamos que el ID del localStorage coincida con el de la URL
  if (localUser.id !== Number(id)) {
    userCard.innerHTML = `
      <p>No tienes permisos para ver este perfil.</p>
      <a href="/" class="btn view">Volver al inicio</a>
    `;
    return;
  }

  // 4️⃣ Si todo está bien, pedimos los datos completos al servidor
  fetch(`/getUserData?user=${id}`)
    .then((res) => res.json())
    .then((usuario) => {
      if (!usuario) {
        userCard.innerHTML = `
          <p>No se encontraron datos del usuario.</p>
        `;
        return;
      }

      renderUser(usuario);
    })
    .catch((err) => {
      console.error("Error al obtener los datos del usuario:", err);
      userCard.innerHTML = `<p>Error al cargar los datos del usuario.</p>`;
    });


  function renderUser(usuario) {
    // Limpiar contenido previo
    userCard.innerHTML = "";

    // Crear elementos
    const name = document.createElement("h2");
    name.textContent = usuario.nombre;

    const email = document.createElement("p");
    email.innerHTML = `<strong>Email:</strong> `;
    const emailSpan = document.createElement("span");
    emailSpan.textContent = usuario.email;
    email.appendChild(emailSpan);

    const dni = document.createElement("p");
    dni.innerHTML = `<strong>DNI:</strong> `;
    const dniSpan = document.createElement("span");
    dniSpan.textContent = usuario.dni;
    dni.appendChild(dniSpan);

    const tel = document.createElement("p");
    tel.innerHTML = `<strong>Teléfono:</strong> `;
    const telSpan = document.createElement("span");
    telSpan.textContent = usuario.telefono;
    tel.appendChild(telSpan);

    const birth = document.createElement("p");
    birth.innerHTML = `<strong>Fecha de nacimiento:</strong> `;
    const birthSpan = document.createElement("span");
    birthSpan.textContent = usuario.fechaNacimiento;
    birth.appendChild(birthSpan);

    const button = document.createElement("button");
    button.className = "btn modify";
    button.textContent = "Modificar datos";

    // Añadir al contenedor
    userCard.append(name, email, dni, tel, birth, button);

    // Evento del botón
    button.addEventListener("click", () => {
      localStorage.setItem("editUser", JSON.stringify(usuario));
      window.location.href = `/modify_user?user=${usuario.id}`;
    });
  }
});
