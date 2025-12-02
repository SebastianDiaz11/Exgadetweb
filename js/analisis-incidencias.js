    const modal = document.getElementById("modalIncidencia");
    const contenido = document.getElementById("contenidoModal");
    const cerrar = document.getElementById("cerrarModal");

    document.querySelectorAll(".editar").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation();
        const fila = btn.closest("tr");
        const datos = fila.dataset;

        contenido.innerHTML = `
          <form method="POST" enctype="multipart/form-data" action="../php/editar-incidencia.php">
            <input type="hidden" name="id" value="${datos.id}">
            <label><b>Mensaje:</b></label>
            <textarea name="mensaje" style="width:100%;min-height:120px;">${datos.mensaje}</textarea>
            <label><b>Imagen (opcional):</b></label>
            ${datos.imagen ? `<p>Imagen actual:<br><img src="${datos.imagen}" style="max-width:100px;"></p>` : ""}
            <input type="file" name="imagen" accept="image/*">
            <button type="submit" style="margin-top:10px;background:#28a745;color:#fff;border:none;padding:10px 15px;border-radius:6px;cursor:pointer;">Guardar cambios</button>
          </form>
        `;
        modal.style.display = "flex";
      });
    });

    document.querySelectorAll(".eliminar").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation();
        const id = btn.closest("tr").dataset.id;
        if (confirm("¿Seguro que querés eliminar la incidencia " + id + "?")) {
          location.href = "../php/eliminar-incidencia.php?id=" + id;
        }
      });
    });

    cerrar.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => {
      if (e.target === modal) modal.style.display = "none";
    });