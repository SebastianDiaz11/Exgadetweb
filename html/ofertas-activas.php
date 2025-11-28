<?php
session_start();
require "../php/conexion.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

$sql = "SELECT TITULO, DIRECCION, LINKEDIN, FECHA_PUBLICACION, ACERCA 
        FROM M950_PUESTOS
        WHERE PAUSADA = 0
        ORDER BY FECHA_PUBLICACION DESC";
$stmt = $conn->query($sql);
$puestos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Ofertas Activas</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/ofertas-activas.css">
</head>

<body>

  <?php include './nav.php'; ?>

  <main>
    <a href="./contacto.php" class="btn-volver">← Volver</a>
    <h1>💼 Ofertas Activas</h1>

    <?php if ($puestos): ?>
      <div class="ofertas-container">
        <?php foreach ($puestos as $p): ?>
          <div class="oferta"
            data-titulo="<?= htmlspecialchars($p['TITULO']) ?>"
            data-direccion="<?= htmlspecialchars($p['DIRECCION']) ?>"
            data-acerca="<?= htmlspecialchars(json_encode($p['ACERCA']), ENT_QUOTES, 'UTF-8') ?>"
            data-linkedin="<?= htmlspecialchars($p['LINKEDIN']) ?>"
            data-fecha="<?= date('d/m/Y', strtotime($p['FECHA_PUBLICACION'])) ?>">
            <img src="../imagenes/logo.png" alt="Logo Empresa">
            <div class="titulo"><?= htmlspecialchars($p['TITULO']) ?></div>
            <div class="fecha"><?= date('d/m/Y', strtotime($p['FECHA_PUBLICACION'])) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p style="text-align:center;">No hay ofertas activas por el momento.</p>
    <?php endif; ?>
  </main>

  <!-- Modal -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitulo"></h2>
        <span class="close" id="cerrarModal">&times;</span>
      </div>
      <div class="modal-body" id="modalBody"></div>
    </div>
  </div>

  <?php include './footer.php'; ?>

  <script src="../js/menu_hambur-modo.js"></script>

  <script>
    const ofertas = document.querySelectorAll('.oferta');
    const modal = document.getElementById('modal');
    const modalTitulo = document.getElementById('modalTitulo');
    const modalBody = document.getElementById('modalBody');
    const cerrar = document.getElementById('cerrarModal');

    ofertas.forEach(oferta => {
      oferta.addEventListener('click', () => {
        const titulo = oferta.dataset.titulo;
        const direccion = oferta.dataset.direccion;
        const acerca = JSON.parse(oferta.dataset.acerca);
        const linkedin = oferta.dataset.linkedin;
        const fecha = oferta.dataset.fecha;

        modalTitulo.textContent = titulo;
        modalBody.innerHTML = `
        <p><b>📅 Fecha de publicación:</b> ${fecha}</p>
        <p><b>📍 Dirección:</b> ${direccion}</p>
        <p><b>📝 Descripción:</b></p>
        <div style="margin-left:10px;">${acerca}</div>
        <a href="${linkedin}" target="_blank">Ver oferta en LinkedIn</a>

        <hr style="margin:20px 0;">

        <h3>📤 Postularse a esta oferta</h3>
        <form method="POST" enctype="multipart/form-data" action="../php/subir_cv.php">
          <input type="hidden" name="puesto" value="${titulo}">
          
          <label style="font-weight:600;">Adjuntar CV (PDF o Word):</label><br>
          <input type="file" name="cv" accept=".pdf,.doc,.docx" required
            style="margin:10px 0; padding:6px; border:1px solid #ccc; border-radius:6px;">
          <br>
          <button type="submit" 
            style="background:#003F6B;color:#fff;padding:10px 15px;
            border:none;border-radius:6px;font-weight:600;cursor:pointer;">
            Enviar CV
          </button>
        </form>
      `;
        modal.style.display = 'flex';
      });
    });

    cerrar.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => {
      if (e.target === modal) modal.style.display = 'none';
    });
  </script>

</body>

</html>