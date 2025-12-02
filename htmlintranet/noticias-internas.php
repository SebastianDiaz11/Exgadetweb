<?php
session_start();
require "../php/conexion.php"; // tu conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

require "../php/subir-noticias.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Noticias y Novedades</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/noticias-internas.css">
</head>

<body>

  <?php include './nav.php'; ?>

  <main>
    <div class="header-noticias">
      <h1>📰 Noticias y Novedades</h1>
      <a href="usuario.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="noticia">
      <h2>📄 Subir nuevo Organigrama (PDF)</h2>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="subirOrganigrama" value="1">

        <input type="file" name="organigrama" accept="application/pdf" required>

        <button type="submit" style="
          background:#003F6B;
          color:#fff;
          padding:8px 14px;
          border-radius:6px;
          border:none;
          cursor:pointer;
          font-weight:600;">
          🔼 Subir Organigrama
        </button>
      </form>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="msg">
        <?php
        if ($_GET['msg'] === 'noticia_creada') echo "✅ Noticia creada correctamente.";
        if ($_GET['msg'] === 'noticia_editada') echo "✏️ Noticia editada correctamente.";
        if ($_GET['msg'] === 'noticia_eliminada') echo "🗑 Noticia eliminada correctamente.";
        if ($_GET['msg'] === 'organigrama_subido') echo "📄 Organigrama actualizado correctamente.";
        if ($_GET['msg'] === 'organigrama_error') echo "❌ Error al subir el PDF del organigrama.";
        ?>
      </div>
    <?php endif; ?>

    <!-- Formulario para nueva noticia -->
    <div class="noticia">
      <h2>➕ Crear nueva noticia</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="nueva" value="1">
        <input type="text" name="titulo" placeholder="Título de la noticia" required>
        <textarea name="cuerpo" rows="5" placeholder="Escribe la noticia aquí..." required></textarea>
        <label>Imagen (opcional):</label>
        <input type="file" name="imagen" accept="image/*">
        <button type="submit">Publicar</button>
      </form>
    </div>

    <!-- Listado de noticias -->
    <?php if (empty($noticias)): ?>
      <p>No hay noticias activas.</p>
    <?php else: ?>
      <?php foreach ($noticias as $n): ?>
        <div class="noticia">
          <h2><?php echo htmlspecialchars($n['TITULO']); ?></h2>

          <?php if (!empty($n['IMAGEN'])): ?>
            <img src="<?php echo htmlspecialchars($n['IMAGEN']); ?>" alt="Imagen de la noticia">
          <?php endif; ?>

          <p><?php echo nl2br(htmlspecialchars($n['CUERPO'])); ?></p>
          <small><i>Publicada el <?php echo date("d/m/Y H:i", strtotime($n['FECHA_CREACION'])); ?></i></small>

          <div class="acciones">
            <a href="?editar=<?php echo $n['ID_NOTICIA']; ?>">✏️ Editar</a>
            <a href="?eliminar=<?php echo $n['ID_NOTICIA']; ?>" onclick="return confirm('¿Eliminar esta noticia?')">🗑 Eliminar</a>
          </div>

          <!-- Formulario para editar -->
          <?php if (isset($_GET['editar']) && $_GET['editar'] == $n['ID_NOTICIA']): ?>
            <form method="POST" enctype="multipart/form-data" class="form-editar">
              <input type="hidden" name="id" value="<?php echo $n['ID_NOTICIA']; ?>">
              <input type="text" name="titulo" value="<?php echo htmlspecialchars($n['TITULO']); ?>" required>
              <textarea name="cuerpo" rows="5" required><?php echo htmlspecialchars($n['CUERPO']); ?></textarea>

              <?php if (!empty($n['IMAGEN'])): ?>
                <p>Imagen actual:</p>
                <img src="<?php echo htmlspecialchars($n['IMAGEN']); ?>" style="max-width:150px;border-radius:6px;margin-bottom:10px;">
              <?php endif; ?>

              <label>Nueva imagen (opcional):</label>
              <input type="file" name="imagen" accept="image/*">

              <button type="submit">Guardar cambios</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <script>
    document.getElementById('organigrama').addEventListener('change', function() {
      const fileSpan = document.getElementById('organigrama-name');
      fileSpan.textContent = this.files.length ? this.files[0].name : "";
    });
  </script>
</body>

</html>