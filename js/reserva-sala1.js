// =============================
    // 🧭 Abrir / cerrar modal
    // =============================
    function abrirModal() {
      document.getElementById("modal").style.display = "block";
      document.getElementById("modal-overlay").style.display = "block";
    }

    function cerrarModal() {
      document.getElementById("modal").style.display = "none";
      document.getElementById("modal-overlay").style.display = "none";
    }

    // =============================
    // 📅 Mostrar reuniones del día
    // =============================
    async function mostrarReunionesDia(fecha) {
      const resp = await fetch("", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `accion=get_dia&fecha=${fecha}`
      });
      const data = await resp.json();

      const contenedor = document.querySelector(".reuniones");
      contenedor.innerHTML = `
      <div class="reuniones-header">
        <h2>Reuniones del ${fecha}</h2>
        <button class="btn-nueva" onclick="abrirModal()">+ Nueva reunión</button>
        <a href="./usuario.php"><button class="btn-volver">Volver</button></a>
      </div>
    `;

      if (data.length === 0) {
        contenedor.innerHTML += `<p style="color:#999;">No hay reuniones programadas.</p>`;
      } else {
        data.forEach(r => {
          contenedor.innerHTML += `
    <div class="bloque" style="border-left-color:${r.color}; position:relative;">

      <div class="acciones-bloque">

        <i class="fa-solid fa-copy icono copiar-bloque"
          data-info="
Sala: ${r.EQUIPO}

Día: ${r.DIA}

Hora: ${r.HORA.slice(0,5)} a ${r.HASTA ? r.HASTA.slice(0,5) : ""}

Motivo: ${r.MOTIVO}

Infusiones: ${r.INFUSIONES}

Personas: ${r.PERSONAS}

Elementos tecnologicos: ${r.TECNOLOGIA}

Reservado por: ${r.NOMBRE}"
          title="Copiar"></i>

        <i class="fa-solid fa-eye icono ver-bloque"
          data-info="
Sala: ${r.EQUIPO}

Día: ${r.DIA}

Hora: ${r.HORA.slice(0,5)} a ${r.HASTA ? r.HASTA.slice(0,5) : ""}

Motivo: ${r.MOTIVO}

Infusiones: ${r.INFUSIONES}

Personas: ${r.PERSONAS}

Elementos tecnologicos: ${r.TECNOLOGIA}

Reservado por: ${r.NOMBRE}"
          title="Ver detalles"></i>

      </div>

      <strong>${r.EQUIPO}</strong> • ${r.HORA.slice(0,5)} – ${r.HASTA ? r.HASTA.slice(0,5) : ""} hs<br>

      ${r.MOTIVO}<br>
      <small>${r.NOMBRE}</small>

      <br>Infusiones: <strong>${r.INFUSIONES}</strong>
      <br>Personas: <strong>${r.PERSONAS}</strong>

    </div>`;
        });
      }

      // 🔁 Reasignar eventos de edición y eliminación
      inicializarEventosReuniones();
    }

    // =============================
    // 🕒 Crear nueva reunión (AJAX)
    // =============================
    const form = document.getElementById("formReunion");

    form.addEventListener("submit", async e => {
      e.preventDefault();

      const dia = form.dia.value;
      const hora = form.hora.value;
      const hasta = form.hasta.value;
      const equipo = form.equipo.value;
      const motivo = form.motivo.value;
      const msg = document.getElementById("msgBox");
      msg.innerHTML = "";

      // 1️⃣ Verificar duplicado
      const check = await fetch("", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `accion=check_reunion&dia=${dia}&hora=${hora}&equipo=${encodeURIComponent(equipo)}`
      });
      const existe = await check.json();
      if (existe.exists) {
        msg.innerHTML = "<div class='alerta error'>⚠️ Ya existe una reunión en esa sala, fecha y hora.</div>";
        return;
      }

      // 2️⃣ Crear reunión
      const crear = await fetch("", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `accion=crear&dia=${dia}&hora=${hora}&hasta=${hasta}&equipo=${encodeURIComponent(equipo)}&motivo=${encodeURIComponent(motivo)}&infusiones=${form.infusiones.value}&personas=${form.personas.value}&tecnologia=${form.tecnologia.value}`
      });

      const res = await crear.json();
      if (res.ok) {
        cerrarModal();
        showToast("✅ Reunión creada correctamente");
        mostrarReunionesDia(dia);
        actualizarMisReuniones();
      } else {
        msg.innerHTML = "<div class='alerta error'>❌ Error al crear la reunión.</div>";
      }
    });

    // =============================
    // ✏️ Inicializar eventos (Guardar / Eliminar)
    // =============================
    function inicializarEventosReuniones() {
      document.querySelectorAll(".edit").forEach(btn => {
        btn.onclick = async e => {
          const tr = e.target.closest("tr");
          for (const td of tr.querySelectorAll("[contenteditable]")) {
            const campo = td.dataset.campo;
            const valor = td.innerText.trim();
            await fetch("", {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded"
              },
              body: `accion=editar&id=${tr.dataset.id}&campo=${campo}&valor=${encodeURIComponent(valor)}`
            });
          }
          showToast("✅ Cambios guardados");
          actualizarMisReuniones();
          mostrarReunionesDia(document.querySelector(".hoy")?.textContent || "<?= date('Y-m-d') ?>");
        };
      });

      document.querySelectorAll(".delete").forEach(btn => {
        btn.onclick = async e => {
          if (!confirm("¿Eliminar esta reunión?")) return;
          const tr = e.target.closest("tr");
          await fetch("", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `accion=eliminar&id=${tr.dataset.id}`
          });
          tr.remove();
          showToast("🗑 Reunión eliminada");
          actualizarMisReuniones();
          mostrarReunionesDia(document.querySelector(".hoy")?.textContent || "<?= date('Y-m-d') ?>");
        };
      });
    }

    // =============================
    // 🔁 Refrescar tabla "Mis reuniones"
    // =============================
    async function actualizarMisReuniones() {
      const resp = await fetch(location.href);
      const text = await resp.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(text, "text/html");
      const nuevaTabla = doc.querySelector(".mis-reuniones table tbody");
      document.querySelector(".mis-reuniones table tbody").innerHTML = nuevaTabla.innerHTML;

      // ⚡ Reasignar eventos después de refrescar
      inicializarEventosReuniones();
    }

    // =============================
    // 🔔 Toast de confirmación
    // =============================
    function showToast(msg) {
      const div = document.createElement("div");
      div.textContent = msg;
      div.style.position = "fixed";
      div.style.bottom = "20px";
      div.style.right = "20px";
      div.style.background = "#F9C031";
      div.style.color = "#000";
      div.style.padding = "10px 15px";
      div.style.borderRadius = "6px";
      div.style.fontWeight = "600";
      div.style.zIndex = "10000";
      document.body.appendChild(div);
      setTimeout(() => div.remove(), 2500);
    }

    // Inicializar eventos al cargar
    document.addEventListener("DOMContentLoaded", inicializarEventosReuniones);



    //Popup

    // Abrir popup y copiar texto
    document.addEventListener("click", e => {

      // Copiar
      if (e.target.classList.contains("copiar-bloque")) {
        navigator.clipboard.writeText(e.target.dataset.info);
        showToast("📋 Información copiada");
      }

      // Ver más
      if (e.target.classList.contains("ver-bloque")) {
        document.getElementById("popup-text").textContent = e.target.dataset.info;
        document.getElementById("popup-overlay").style.display = "block";
        document.getElementById("popup-info").style.display = "block";
      }
    });

    // Cerrar popup
    function cerrarPopup() {
      document.getElementById("popup-info").style.display = "none";
      document.getElementById("popup-overlay").style.display = "none";
    }

    document.getElementById("popup-overlay").onclick = cerrarPopup;