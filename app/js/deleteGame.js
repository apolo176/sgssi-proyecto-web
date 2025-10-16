document.addEventListener("DOMContentLoaded", () => {
  window.createProfileCircle()
})
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