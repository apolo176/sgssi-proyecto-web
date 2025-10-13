document.addEventListener("DOMContentLoaded", () => {
  const menu = document.getElementById("menu");
  const usuario = JSON.parse(localStorage.getItem("usuario"));

  if (usuario) {
    // Mostrar opciones de usuario en el menú
    menu.innerHTML = `
            <li><a href="/items">🎮 Ver todos los videojuegos</a></li>
            <li><a href="/add_item">➕ Añadir un nuevo videojuego</a></li>
            <li><a href="/show_user?user=${usuario.id}">⚙️ Modificar mis datos</a></li>
            <li><a href="#" class="logout" id="logoutMain">🚪 Cerrar sesión</a></li>

        `;

    // Crear el círculo de usuario
    const inicial = usuario.nombre?.charAt(0).toUpperCase() || "U";
    userArea.innerHTML = `
            <div class="user-circle" id="userCircle">${inicial}</div>
            <div class="user-menu" id="userMenu">
                <a href="/" id="home">🏠 Home</a>

                <a href="/show_user?user=${usuario.id}">👤 ${usuario.nombre}</a>

                <a href="#" class="logout" id="logout">🚪 Cerrar sesión</a>
            </div>
        `;

    const userCircle = document.getElementById("userCircle");
    const userMenu = document.getElementById("userMenu");
    const logoutLinks = document.querySelectorAll(".logout");

    // Toggle del menú al hacer clic en el círculo
    userCircle.addEventListener("click", () => {
      userMenu.classList.toggle("active");
    });

    // Cerrar sesión
    logoutLinks.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        localStorage.removeItem("usuario");
        localStorage.removeItem("editUser");
        window.location.reload();
      });
    });

    // Cerrar el menú si se hace clic fuera
    document.addEventListener("click", (e) => {
      if (!userArea.contains(e.target)) {
        userMenu.classList.remove("active");
      }
    });
  }
});
