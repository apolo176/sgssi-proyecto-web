document.addEventListener("DOMContentLoaded", () => {
  const menu = document.getElementById("menu");
  const usuario = JSON.parse(localStorage.getItem("usuario"));

  if (usuario) {
    menu.innerHTML = "";

    const links = [
      { href: "/items", text: "🎮 Ver todos los videojuegos" },
      { href: "/add_item", text: "➕ Añadir un nuevo videojuego" },
      { href: `/show_user?user=${usuario.id}`, text: "⚙️ Modificar mis datos" },
      { href: "#", text: "🚪 Cerrar sesión", id: "logoutMain", class: "logout" }
    ];

    links.forEach(linkData => {
      const li = document.createElement("li");
      const a = document.createElement("a");

      a.href = linkData.href;
      a.textContent = linkData.text;

      if (linkData.id) a.id = linkData.id;
      if (linkData.class) a.className = linkData.class;

      li.appendChild(a);
      menu.appendChild(li);
    });

    userArea.innerHTML = ""; // limpiar
    const userCircle = document.getElementById("userCircle");
    const userMenu = document.getElementById("userMenu");
    const logoutLinks = document.querySelectorAll(".logout");
    const inicial = usuario.nombre?.charAt(0).toUpperCase() || "U";

    const circle = document.createElement("div");
    circle.className = "user-circle";
    circle.id = "userCircle";
    circle.textContent = inicial;

    userMenu.className = "user-menu";
    userMenu.id = "userMenu";

    // Enlaces del user menu
    const userLinks = [
      { href: "/", text: "🏠 Home", id: "home" },
      { href: `/show_user?user=${usuario.id}`, text: `👤 ${usuario.nombre}` },
      { href: "#", text: "🚪 Cerrar sesión", id: "logout", class: "logout" }
    ];

    userLinks.forEach(linkData => {
      const a = document.createElement("a");
      a.href = linkData.href;
      a.textContent = linkData.text;

      if (linkData.id) a.id = linkData.id;
      if (linkData.class) a.className = linkData.class;

      userMenu.appendChild(a);
    });

    userArea.appendChild(circle);
    userArea.appendChild(userMenu);


    userCircle.addEventListener("click", () => {
      userMenu.classList.toggle("active");
    });

    logoutLinks.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        localStorage.removeItem("usuario");
        localStorage.removeItem("editUser");
        window.location.reload();
      });
    });

    document.addEventListener("click", (e) => {
      if (!userArea.contains(e.target)) {
        userMenu.classList.remove("active");
      }
    });
  }
});
