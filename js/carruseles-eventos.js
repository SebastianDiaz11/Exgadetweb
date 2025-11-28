function lanzarConfeti() {
  var duration = 4000;
  var end = Date.now() + duration;
  (function frame() {
    confetti({ particleCount: 6, angle: 60, spread: 55, origin: { x: 0 } });
    confetti({ particleCount: 6, angle: 120, spread: 55, origin: { x: 1 } });
    if (Date.now() < end) requestAnimationFrame(frame);
  })();
}

document.addEventListener("DOMContentLoaded", () => {
  if (window.esSuAniversario) {
    lanzarConfeti();
    const pop = document.getElementById("popupAniversario");
    if (pop) pop.style.display = "flex";
  }
});

document.addEventListener("DOMContentLoaded", () => {
  if (window.esSuCumpleanios) {
    lanzarConfeti();
    const pop = document.getElementById("popupCumpleanio");
    if (pop) pop.style.display = "flex";
  }
});

// Popup Empleado Efectivo
document.addEventListener("DOMContentLoaded", () => {
  if (window.esSuEfectivacion) {
    lanzarConfeti();
    const pop = document.getElementById("popupEfectivo");
    if (pop) pop.style.display = "flex";
  }
});

// Cerrar popup Efectivo
document.getElementById("cerrarPopupEfec")?.addEventListener("click", () => {
  const pop = document.getElementById("popupEfectivo");
  if (pop) pop.style.display = "none";
});

// Cerrar popupAniversario
    document.getElementById("cerrarPopup")?.addEventListener("click", () => {
      const pop = document.getElementById("popupAniversario");
      if (pop) pop.style.display = "none";
    });

// Cerrar popupCumpleanios
    document.getElementById("cerrarPopupCump")?.addEventListener("click", () => {
      const pop = document.getElementById("popupCumpleanio");
      if (pop) pop.style.display = "none";
    });

    // Toggle panel usuario (mobile)
    const toggleBtn = document.getElementById("toggleUsuario");
    const panel = document.getElementById("panelUsuario");
    toggleBtn?.addEventListener("click", () => {
        panel.classList.toggle("active");
    });

    // Carrusel (solo si hay más de un slide)
    function iniciarCarrusel(id) {
      const cont = document.getElementById(id);
      if (!cont) return;
      const slides = cont.querySelectorAll(".slide");
      if (!slides || slides.length <= 1) { if (slides[0]) slides[0].classList.add("active"); return; }
      let index = 0;
      slides[index].classList.add("active");
      setInterval(() => {
        slides[index].classList.remove("active");
        index = (index + 1) % slides.length;
        slides[index].classList.add("active");
      }, 4000);
    }
    document.addEventListener("DOMContentLoaded", () => {
      iniciarCarrusel("carruselCumpleHoy");
      iniciarCarrusel("carruselAnivHoy");
      iniciarCarrusel("carruselCumple");
      iniciarCarrusel("carruselAniv");
      iniciarCarrusel("carruselNuevos");
      iniciarCarrusel("carruselEfectivos");
    });