<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

require "../php/mail-incidencias-intranet.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Incidencias</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/incidencia.css">
</head>

<body>
  <?php include './nav.php'; ?>
  <main>
    <a href="usuario.php" class="btn-volver">⬅ Volver</a>
    <h1>📌 Reportar Incidencia</h1>

    <?php if ($msg): ?>
      <div class="msg" style="background:#e6f7e6;padding:10px;border-radius:6px;"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label>Usuario:</label>
      <input type="text" value="<?= htmlspecialchars($nombreCompleto) ?>" disabled>

      <label>Cargo/Área:</label>
      <input type="text" value="<?= htmlspecialchars($cargoUsuario) ?>" disabled>

      <label>Email:</label>
      <input type="text" value="<?= htmlspecialchars($emailUsuario) ?>" disabled>

      <label>Orden de Prioridad:</label>
      <select name="prioridad" required>
        <option value="Alta">Alta</option>
        <option value="Media" selected>Media</option>
        <option value="Baja">Baja</option>
      </select>

      <label>Tarea a realizar:</label>
      <select name="tarea" required>
        <option value="Borrar tarea">Borrar tarea</option>
        <option value="Borrar tarea pendiente">Borrar tarea pendiente</option>
        <option value="Error de llegada">Error de llegada</option>
        <option value="Comunicar/Liberar Tarea">Comunicar/Liberar Tarea</option>
        <option value="Error de expediente">Error de expediente</option>
        <option value="Liberar póliza">Liberar póliza</option>
        <option value="Otro">Otro</option>
      </select>

      <label>Mensaje:</label>
      <textarea name="mensaje" required></textarea>

      <label>Adjuntar Imagen (opcional):</label>
      <input type="file" name="imagen" accept="image/*">

      <button type="submit" class="enviar-incidencia">Enviar Incidencia</button>
    </form>

    <h2>🗂 Mis Incidencias</h2>
    <?php if ($incidencias): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Mensaje</th>
            <th>Área</th>
            <th>Tarea</th>
            <th>Prioridad</th>
            <th>Imagen</th>
            <th>Fecha creación</th>
            <th>Estado</th>
            <th>Terminado por</th>
            <th>Fecha terminado</th>
            <th>Conforme</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($incidencias as $inc): ?>
            <tr>
              <td data-label="ID"><?= $inc["ID"] ?></td>
              <td data-label="Mensaje"><?= htmlspecialchars($inc["MENSAJE"]) ?></td>
              <td data-label="Área"><?= htmlspecialchars($inc["SECTOR"]) ?></td>
              <td data-label="Tarea"><?= htmlspecialchars($inc["TAREA"] ?? '-') ?></td>
              <td data-label="Prioridad"><strong><?= htmlspecialchars($inc["PRIORIDAD"] ?? '—') ?></strong></td>
              <td data-label="Imagen"><?= $inc["IMAGEN"] ? "<img src='{$inc["IMAGEN"]}' alt='img'>" : "-" ?></td>
              <td data-label="Fecha creación"><?= $inc["FECHA_CREACION"] ?></td>
              <td data-label="Estado"><span class="estado <?= strtolower($inc["ESTADO"]) ?>"><?= htmlspecialchars($inc["ESTADO"]) ?></span></td>
              <td data-label="Terminado por"><?= $inc["TERMINADO_POR"] ?: "-" ?></td>
              <td data-label="Fecha terminado"><?= $inc["FECHA_TERMINADO"] ? date("d/m/Y H:i", strtotime($inc["FECHA_TERMINADO"])) : "-" ?></td>
              <td data-label="Conforme">
                <select name="conforme"
                  class="select-conforme"
                  data-id="<?= $inc["ID"] ?>">
                  <option value="">Seleccionar...</option>
                  <option value="Cerrado" <?= ($inc["CONFORME"] ?? '') === "Cerrado" ? 'selected' : '' ?>>Cerrado</option>
                  <option value="No resuelto" <?= ($inc["CONFORME"] ?? '') === "No resuelto" ? 'selected' : '' ?>>No resuelto</option>
                </select>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No has registrado ninguna incidencia todavía.</p>
    <?php endif; ?>
  </main>

  <script>
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
  </script>
</body>

</html>