(() => {
  // === CONFIG por defecto ===
  const CFG = Object.assign({
    phone: "+5491123456789",
    message: "Hola, quiero consultar por...",
    position: "right",
    bottom: "20px",
    side: "20px",
    tooltip: "Chatear por WhatsApp",
    zIndex: 99999,
    image: "./imagenes/whatsapp/icono.png" // 🔹 Ruta de tu imagen
  }, (window.WAPPBTN || {}));

  const cleanPhone = CFG.phone.replace(/[^\d]/g, "");
  const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(CFG.message)}`;

  // === Estilos ===
  if (!document.getElementById("wappbtn-style")) {
    const css = `
      .wappbtn-wrap {
        position: fixed;
        bottom: ${CFG.bottom};
        ${CFG.position}: ${CFG.side};
        z-index: ${CFG.zIndex};
      }
      .wappbtn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #25D366;
        border: none;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(0,0,0,.2);
        transition: transform .15s ease, box-shadow .15s ease;
        overflow: hidden;
        padding: 0;
      }
      .wappbtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(0,0,0,.25);
      }
      .wappbtn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .wappbtn-tip {
        position: absolute;
        white-space: nowrap;
        background: #111;
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
        font: 500 12px/1.2 system-ui, -apple-system, Segoe UI, Roboto;
        bottom: 64px;
        opacity: 0;
        pointer-events: none;
        transform: translateY(6px);
        transition: opacity .15s ease, transform .15s ease;
      }
      .wappbtn-wrap:hover .wappbtn-tip {
        opacity: 1;
        transform: translateY(0);
      }
      @media (max-width:480px){
        .wappbtn-tip { display: none }
      }
    `;
    const style = document.createElement("style");
    style.id = "wappbtn-style";
    style.textContent = css;
    document.head.appendChild(style);
  }

  // === Crear elementos ===
  const wrap = document.createElement("div");
  wrap.className = "wappbtn-wrap";

  const tip = document.createElement("div");
  tip.className = "wappbtn-tip";
  tip.textContent = CFG.tooltip || "";
  wrap.appendChild(tip);

  const btn = document.createElement("button");
  btn.className = "wappbtn";
  btn.setAttribute("aria-label", CFG.tooltip || "WhatsApp");
  btn.innerHTML = `<img src="${CFG.image}" alt="WhatsApp">`;
  btn.addEventListener("click", () => window.open(waUrl, "_blank", "noopener"));

  wrap.appendChild(btn);
  document.body.appendChild(wrap);
})();
