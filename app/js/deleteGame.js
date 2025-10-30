document.addEventListener('DOMContentLoaded', () => {
  
  //Buscar el botón de borrar
  const deleteButton = document.getElementById('item_delete_submit');
  
  //Buscar el botón de cancelar
  const cancelButton = document.getElementById('item_delete_cancel');

  //Asignar la función deleteSubmit al clic del botón de borrar
  if (deleteButton) {
    deleteButton.addEventListener('click', () => {
      deleteSubmit(); 
    });
  }

  // Asignar la función deleteCancel al clic del botón de cancelar
  if (cancelButton) {
    cancelButton.addEventListener('click', () => {
      deleteCancel();
    });
  }
});

async function deleteSubmit(){
   try {
            const params = new URLSearchParams(window.location.search);
            const id = params.get("item");

            const response = await fetch(`/doDeleteItem?item=${id}`, {
              method: "DELETE",
            })

            if (!response.ok) {
              throw new Error("Error al eliminar el juego");
            }
            window.location.href = '/items'
            // Recargar la tabla sin recargar toda la página
          } catch (err) {
            console.error("Error al eliminar el juego:", err);
            alert("❌ No se pudo eliminar el juego: " + err.message);
          }
}
function deleteCancel(){
  window.location.href='/items'
}