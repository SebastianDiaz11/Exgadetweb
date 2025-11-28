// Popup Noticia (RRHH + SGI)
const noticias = document.querySelectorAll(".noticia, .sgi-noticia");
const popup = document.getElementById("popupNoticia");
const popupTitulo = document.getElementById("popupTitulo");
const popupFecha = document.getElementById("popupFecha");
const popupCuerpo = document.getElementById("popupCuerpo");
const popupImagen = document.getElementById("popupImagen");
const cerrarBtn = popup.querySelector(".cerrar");

// Versión SGI (si existe)
const popupSGI = document.getElementById("popupNoticiaSGI");
const popupTituloSGI = document.getElementById("popupTituloSGI");
const popupFechaSGI = document.getElementById("popupFechaSGI");
const popupCuerpoSGI = document.getElementById("popupCuerpoSGI");
const popupImagenSGI = document.getElementById("popupImagenSGI");
const cerrarBtnSGI = popupSGI ? popupSGI.querySelector(".cerrar-sgi") : null;

// Abrir popup de RRHH o SGI según clase
noticias.forEach(n => {
  n.addEventListener("click", () => {
    const isSGI = n.classList.contains("sgi-noticia");
    const popupEl = isSGI ? popupSGI : popup;
    const tituloEl = isSGI ? popupTituloSGI : popupTitulo;
    const fechaEl = isSGI ? popupFechaSGI : popupFecha;
    const cuerpoEl = isSGI ? popupCuerpoSGI : popupCuerpo;
    const imagenEl = isSGI ? popupImagenSGI : popupImagen;

    if (!popupEl) return;

    tituloEl.textContent = n.dataset.titulo;
    fechaEl.textContent = "Publicada el " + n.dataset.fecha;
    cuerpoEl.textContent = n.dataset.cuerpoCompleto || n.dataset.cuerpo || '';
    if (n.dataset.imagen) {
      imagenEl.src = n.dataset.imagen;
      imagenEl.style.display = "block";
    } else {
      imagenEl.style.display = "none";
    }
    popupEl.classList.add("active");
  });
});

// Cerrar popups
if (cerrarBtn) cerrarBtn.addEventListener("click", () => popup.classList.remove("active"));
if (popup) popup.addEventListener("click", e => { if (e.target === popup) popup.classList.remove("active"); });

if (cerrarBtnSGI) cerrarBtnSGI.addEventListener("click", () => popupSGI.classList.remove("active"));
if (popupSGI) popupSGI.addEventListener("click", e => { if (e.target === popupSGI) popupSGI.classList.remove("active"); });
