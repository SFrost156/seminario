// ============================
// Contador regresivo en texto
// ============================
(function () {
  const lbl = document.getElementById("contadorTexto");
  if (!lbl) return;

  const fechaStr = (window.__FECHA_SEMINARIO__ || "2025-10-06 07:00:00").replace(" ", "T");

  function tick() {
    const objetivo = new Date(fechaStr).getTime();
    const ahora = Date.now();
    const dif = objetivo - ahora;

    if (Number.isNaN(objetivo)) {
      lbl.textContent = "⚠️ Fecha no válida.";
      return;
    }

    if (dif <= 0) {
      lbl.textContent = "✅ ¡El seminario ha comenzado!";
      return;
    }

    const d = Math.floor(dif / (1000 * 60 * 60 * 24));
    const h = Math.floor((dif % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((dif % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((dif % (1000 * 60)) / 1000);

    lbl.textContent = `⏳ Faltan ${d}d ${h}h ${m}m ${s}s para el Seminario ⏳`;
    setTimeout(tick, 1000);
  }

  tick();
})();
