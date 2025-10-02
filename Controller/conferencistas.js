document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modalBio");
    const modalTitle = document.getElementById("modalTitle");
    const modalContent = document.getElementById("modalContent");
    const modalImg = document.getElementById("modalImg");
    const modalFlag = document.getElementById("modalFlag");
    const closeBtn = document.querySelector(".modal-close");
    const btnChips = document.querySelectorAll(".btn-chip");

    // Abrir modal
    btnChips.forEach(btn => {
        btn.addEventListener("click", () => {
            modalTitle.textContent = btn.dataset.titulo;
            modalContent.textContent = btn.dataset.bio;

            // Manejo de la imagen y la bandera
            const imgSrc = btn.dataset.img;
            const flagSrc = btn.dataset.flag;

            if (imgSrc) {
                modalImg.src = imgSrc;
                modalImg.style.display = "block";
            } else {
                modalImg.style.display = "none";
            }

            if (flagSrc) {
                modalFlag.src = flagSrc;
                modalFlag.style.display = "block";
            } else {
                modalFlag.style.display = "none";
            }

            modal.style.display = "flex";
        });
    });

    // Cerrar modal
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });
    modal.addEventListener("click", e => {
        if (e.target === modal) modal.style.display = "none";
    });
});