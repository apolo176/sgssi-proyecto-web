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

  // 5️⃣ Función para renderizar el usuario
  function renderUser(usuario) {
    userCard.innerHTML = `
      <h2>${usuario.nombre}</h2>
      <p><strong>Email:</strong> ${usuario.email}</p>
      <p><strong>DNI:</strong> ${usuario.dni}</p>
      <p><strong>Teléfono:</strong> ${usuario.telefono}</p>
      <p><strong>Fecha de nacimiento:</strong> ${usuario.fechaNacimiento}</p>
      <button class="btn modify">Modificar datos</button>
    `;

    const modifyBtn = userCard.querySelector(".modify");
    modifyBtn.addEventListener("click", () => {
      localStorage.setItem("editUser", JSON.stringify(usuario));
      window.location.href = `/modify_user?user=${usuario.id}`;
    });
  }
});
