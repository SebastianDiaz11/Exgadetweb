 document.addEventListener("DOMContentLoaded", () => {
      const selects = document.querySelectorAll(".select-conforme");

      selects.forEach(select => {
        const id = select.dataset.id;
        const valorInicial = select.value;

        // 🔒 Si ya está "Cerrado" en la base, lo bloqueamos desde el inicio
        if (valorInicial === "Cerrado") {
          select.disabled = true;
          select.style.opacity = "0.7";
          select.style.cursor = "not-allowed";
          return; // no agregamos el evento
        }

        // 📩 Evento al cambiar el valor
        select.addEventListener("change", async () => {
          const valor = select.value;
          if (!valor) return;

          try {
            const res = await fetch("../php/actualizar_conforme.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded"
              },
              body: `id=${encodeURIComponent(id)}&conforme=${encodeURIComponent(valor)}`
            });

            const texto = await res.text();
            console.log("Respuesta del servidor:", texto);

            // 💚 efecto visual
            select.style.background = "#d8ffd8";
            setTimeout(() => select.style.background = "", 1500);

            // 🔒 Si se seleccionó "Cerrado", bloquear el select
            if (valor === "Cerrado") {
              select.disabled = true;
              select.style.opacity = "0.7";
              select.style.cursor = "not-allowed";
            }

          } catch (err) {
            console.error("Error:", err);
            select.style.background = "#ffd8d8";
          }
        });
      });
    });