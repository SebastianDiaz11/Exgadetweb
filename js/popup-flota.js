const cards = document.querySelectorAll('.card');
    const overlay = document.querySelector('.overlay');

    cards.forEach(card => {
      const closeBtn = card.querySelector('.btn-volver');
      if (closeBtn) {
        closeBtn.addEventListener('click', e => {
          e.stopPropagation();
          card.classList.remove('active');
          overlay.classList.remove('active');
          cards.forEach(c => c.classList.remove('fade-out'));
          document.body.style.overflow = "auto";
        });
      }

      card.addEventListener('click', () => {
        if (card.classList.contains('active')) return;
        cards.forEach(c => { if (c !== card) c.classList.add('fade-out'); });
        overlay.classList.add('active');
        card.classList.add('active');
        document.body.style.overflow = "hidden";
      });
    });

    overlay.addEventListener('click', () => {
      const activeCard = document.querySelector('.card.active');
      if (activeCard) {
        activeCard.classList.remove('active');
        overlay.classList.remove('active');
        cards.forEach(c => c.classList.remove('fade-out'));
        document.body.style.overflow = "auto";
      }
    });