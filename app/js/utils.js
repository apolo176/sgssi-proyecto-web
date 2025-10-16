window.createProfileCircle = function () {
  const usuario = JSON.parse(localStorage.getItem("usuario"));

  if (usuario) {
    // Crear el círculo de usuario
    const inicial = usuario.nombre?.charAt(0).toUpperCase() || "U";
    userArea.innerHTML = `
            <div class="user-circle" id="userCircle">${inicial}</div>
            <div class="user-menu" id="userMenu">
                <a href="/" id="home">🏠 Home</a>
                <a href="/show_user?user=${usuario.id}">👤 ${usuario.nombre}</a>
                <a href="#" id="logout">🚪 Cerrar sesión</a>
            </div>
        `;

    const userCircle = document.getElementById("userCircle");
    const userMenu = document.getElementById("userMenu");
    const logoutLink = document.getElementById("logout");

    // Toggle del menú al hacer clic en el círculo
    userCircle.addEventListener("click", () => {
      userMenu.classList.toggle("active");
    });

    // Cerrar sesión
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      localStorage.removeItem("usuario");
      localStorage.removeItem("editUser");
      window.location.reload();
    });

    // Cerrar el menú si se hace clic fuera
    document.addEventListener("click", (e) => {
      if (!userArea.contains(e.target)) {
        userMenu.classList.remove("active");
      }
    });
  }else{
    userArea.innerHTML = `
      <a href="/" id="home"><div class="user-circle" id="userCircle">🏠</div></a>`;
  }
};
