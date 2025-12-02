const obras = document.querySelectorAll(".obra");
    const overlay = document.querySelector(".overlay");
    const modal = document.getElementById("modalObra");
    const modalImg = modal.querySelector(".main");
    const modalTitulo = modal.querySelector("h2");
    const modalDesc = modal.querySelector(".descripcion");
    const modalGaleria = modal.querySelector(".galeria");
    const btnVolver = modal.querySelector(".btn-volver");

    obras.forEach(obra => {
      obra.addEventListener("click", () => {
        const titulo = obra.dataset.title || obra.querySelector("h2").textContent;
        const desc = obra.dataset.desc || "";
        const comitente = obra.dataset.comitente || "";
        const inicio = obra.dataset.inicio || "";
        const fin = obra.dataset.final || "";
        const imgs = obra.dataset.images ? obra.dataset.images.split(",") : [];

        modalImg.src = obra.querySelector("img").src;
        modalTitulo.textContent = titulo;
        modalDesc.innerHTML = `
          <p>${desc}</p>
          <p><strong>Comitente:</strong> ${comitente}</p>
          <p><strong>Plazo:</strong> ${inicio} - ${fin}</p>
        `;
        modalGaleria.innerHTML = "";
        imgs.forEach(img => {
          const el = document.createElement("img");
          el.src = img;
          modalGaleria.appendChild(el);
        });

        overlay.classList.add("active");
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
      });
    });

    function cerrarModal() {
      modal.classList.remove("active");
      overlay.classList.remove("active");
      document.body.style.overflow = "auto";
    }

    btnVolver.addEventListener("click", cerrarModal);
    overlay.addEventListener("click", cerrarModal);