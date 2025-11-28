// ===============================
// 🎠 Lógica del carrusel
// ===============================
const slides = document.querySelectorAll(".carrusel .slide");
const prevBtn = document.querySelector(".carrusel .prev");
const nextBtn = document.querySelector(".carrusel .next");

let index = 0;

function showSlide(i) {
  slides.forEach((slide, idx) => {
    slide.classList.remove("active");
    slide.style.left = (idx - i) * 100 + "%";
  });
  slides[i].classList.add("active");
}

function nextSlide() {
  index = (index + 1) % slides.length;
  showSlide(index);
}

function prevSlide() {
  index = (index - 1 + slides.length) % slides.length;
  showSlide(index);
}

// Si existen botones de control, se asignan eventos
if (nextBtn && prevBtn) {
  nextBtn.addEventListener("click", nextSlide);
  prevBtn.addEventListener("click", prevSlide);
}

// Cambio automático cada 5 segundos
setInterval(nextSlide, 5000);

// Inicial
if (slides.length > 0) {
  showSlide(index);
}

// ===============================
// 🌙 Cambiar imágenes según modo
// ===============================
function actualizarImagenesPorModo() {
  const isDark = document.body.classList.contains("dark-mode");
  document.querySelectorAll(".carrusel-track img").forEach(img => {
    const originalSrc = img.getAttribute("src");
    const darkSrc = img.getAttribute("data-dark");

    // Si hay versión dark y estamos en modo oscuro → usarla
    if (isDark && darkSrc) {
      if (!img.dataset.original) {
        img.dataset.original = originalSrc; // guardar original si no se guardó
      }
      img.src = darkSrc;
    }
    // Si volvemos al modo claro → restaurar imagen original
    else if (!isDark && img.dataset.original) {
      img.src = img.dataset.original;
    }
  });
}

// Ejecutar al cargar la página
document.addEventListener("DOMContentLoaded", actualizarImagenesPorModo);

// Observar cambios de clase en el body (para detectar cambio de modo)
const observer = new MutationObserver(actualizarImagenesPorModo);
observer.observe(document.body, { attributes: true, attributeFilter: ["class"] });
