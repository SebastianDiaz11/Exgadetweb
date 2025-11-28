// Carrusel Noticias RRHH
const slider = document.querySelector('.noticias-slider');
const prevBtn = document.querySelector('.carrusel-btn.prev');
const nextBtn = document.querySelector('.carrusel-btn.next');

if (slider && prevBtn && nextBtn) {
  const noticiaWidth = slider.querySelector('.noticia')?.offsetWidth + 20 || 0;
  prevBtn.addEventListener('click', () => slider.scrollBy({ left: -noticiaWidth, behavior: 'smooth' }));
  nextBtn.addEventListener('click', () => slider.scrollBy({ left: noticiaWidth, behavior: 'smooth' }));
}

// Carrusel Noticias SGI
const sliderSGI = document.querySelector('.sgi-slider');
const prevBtnSGI = document.querySelector('.sgi-carrusel-btn.sgi-prev');
const nextBtnSGI = document.querySelector('.sgi-carrusel-btn.sgi-next');

if (sliderSGI && prevBtnSGI && nextBtnSGI) {
  const noticiaWidthSGI = sliderSGI.querySelector('.sgi-noticia')?.offsetWidth + 20 || 0;
  prevBtnSGI.addEventListener('click', () => sliderSGI.scrollBy({ left: -noticiaWidthSGI, behavior: 'smooth' }));
  nextBtnSGI.addEventListener('click', () => sliderSGI.scrollBy({ left: noticiaWidthSGI, behavior: 'smooth' }));
}
