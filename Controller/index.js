// CONTADOR REGRESIVO
const contador = document.getElementById("contador");

function actualizarContador() {
  const fechaEvento = new Date("2025-10-06T12:00:00").getTime();
  const ahora = new Date().getTime();
  const diferencia = fechaEvento - ahora;

  if (diferencia <= 0) {
    contador.textContent = "✅ ¡El seminario ha comenzado!";
    return;
  }

  const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
  const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
  const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

  contador.textContent = 
    `⏳ Faltan ${dias.toString().padStart(2, "0")}d `
    + `${horas.toString().padStart(2, "0")}h `
    + `${minutos.toString().padStart(2, "0")}m `
    + `${segundos.toString().padStart(2, "0")}s `
    + `para el Seminario ⏳`;

  setTimeout(actualizarContador, 1000);
}

actualizarContador();


// MODAL DE IMAGEN
function abrirModal(src) {
  const modal = document.getElementById("modal");
  const modalImg = document.getElementById("modal-img");

  modal.style.display = "flex";
  modalImg.src = src;
}

function cerrarModal() {
  const modal = document.getElementById("modal");
  modal.style.display = "none";
}
