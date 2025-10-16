document.addEventListener("DOMContentLoaded", () => {
  window.createProfileCircle()

  const params = new URLSearchParams(window.location.search);
  const id = params.get("user");
  if (!id) return;
  fetch(`/getUserData?user=${id}`) //Llama al controlador PHP
    .then((res) => res.json())
    .then((data) => {
      const userCard = document.getElementById("user-detail");

      // Recupera usuario del localStorage
      const usuario = data;

      if (!usuario) {
        userCard.innerHTML = `
                    <p>No hay ningún usuario logueado.</p>
                    <a href="/login" class="btn view">Iniciar sesión</a>
                `;
        return;
      }

      // Genera la card con los datos del usuario
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
        // Guardamos temporalmente el usuario en localStorage para el formulario
        localStorage.setItem("editUser", JSON.stringify(usuario));
        // Redirigimos al formulario de registro
        window.location.href = `/modify_user?user=${usuario.id}`;
      });
    });
});
