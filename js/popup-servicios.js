const cards = document.querySelectorAll(".card");
    const overlay = document.getElementById("modalOverlay");
    const modalTitle = document.getElementById("modalTitle");
    const modalDesc = document.getElementById("modalDesc");
    const modalComitente = document.getElementById("modalComitente");
    const modalImages = document.getElementById("modalImages");
    const btnVolver = document.getElementById("btnVolver");

    cards.forEach(card => {
      card.addEventListener("click", () => {
        const title = card.dataset.title || "";
        const desc = card.dataset.desc || "";
        const comitente = card.dataset.comitente || "";
        const imagesAttr = (card.dataset.images || "").trim();
        const images = imagesAttr ? imagesAttr.split(",") : [];

        modalTitle.textContent = title;
        modalDesc.textContent = desc;
        modalComitente.textContent = comitente;

        modalImages.innerHTML = "";
        images.forEach(src => {
          const img = document.createElement("img");
          img.src = src;
          modalImages.appendChild(img);
        });

        overlay.classList.add("active");
        document.body.style.overflow = "hidden";
      });
    });

    const closeModal = () => {
      overlay.classList.remove("active");
      document.body.style.overflow = "auto";
    };

    btnVolver.addEventListener("click", closeModal);
    overlay.addEventListener("click", e => {
      if (e.target === overlay) closeModal();
    });