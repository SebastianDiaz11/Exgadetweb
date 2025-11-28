const track = document.querySelector(".carrusel-track");
let scrollAmount = 0;
let speed = 0.5; // velocidad del carrusel (ajustá a gusto)

function animate() {
  scrollAmount -= speed;
  track.style.transform = `translateX(${scrollAmount}px)`;

  // si la mitad del track ya pasó, reseteamos para efecto infinito
  const trackWidth = track.scrollWidth / 2;
  if (Math.abs(scrollAmount) >= trackWidth) {
    scrollAmount = 0;
  }

  requestAnimationFrame(animate);
}

// Pausar en hover
track.addEventListener("mouseenter", () => speed = 0);
track.addEventListener("mouseleave", () => speed = 0.5);

// Iniciar
animate();
