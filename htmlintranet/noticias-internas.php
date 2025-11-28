<?php
session_start();
require "../php/conexion.php"; // tu conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

// --- Crear nueva noticia ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nueva'])) {
  $titulo = trim($_POST['titulo']);
  $cuerpo = trim($_POST['cuerpo']);

  if ($titulo && $cuerpo) {
    $sql = "INSERT INTO M900_NOTICIAS (TITULO, CUERPO, ACTIVA) 
                OUTPUT INSERTED.ID_NOTICIA 
                VALUES (:titulo, :cuerpo, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":cuerpo", $cuerpo);
    $stmt->execute();
    $idNoticia = $stmt->fetchColumn();

    // 📂 Imagen
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
      $directorio = dirname(__DIR__) . "/imagenesintranet/noticias/";
      if (!file_exists($directorio)) mkdir($directorio, 0777, true);

      $nombreArchivo = "noticia_" . $idNoticia . "." . $ext;
      $rutaDestino = $directorio . $nombreArchivo;

      if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $rutaRelativa = "../imagenesintranet/noticias/" . $nombreArchivo;
        $sqlImg = "UPDATE M900_NOTICIAS SET IMAGEN = :imagen WHERE ID_NOTICIA = :id";
        $stmtImg = $conn->prepare($sqlImg);
        $stmtImg->bindParam(":imagen", $rutaRelativa);
        $stmtImg->bindParam(":id", $idNoticia);
        $stmtImg->execute();
      }
    }

    header("Location: noticias-internas.php?msg=noticia_creada");
    exit;
  }
}

// --- Guardar cambios al editar ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
  $id     = intval($_POST['id']);
  $titulo = trim($_POST['titulo']);
  $cuerpo = trim($_POST['cuerpo']);

  $sql = "UPDATE M900_NOTICIAS 
            SET TITULO = :titulo, CUERPO = :cuerpo 
            WHERE ID_NOTICIA = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":titulo", $titulo);
  $stmt->bindParam(":cuerpo", $cuerpo);
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  // 📂 Nueva imagen si se carga
  if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
    $directorio = dirname(__DIR__) . "/imagenesintranet/noticias/";
    if (!file_exists($directorio)) mkdir($directorio, 0777, true);

    $nombreArchivo = "noticia_" . $id . "." . $ext;
    $rutaDestino = $directorio . $nombreArchivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
      $rutaRelativa = "../imagenesintranet/noticias/" . $nombreArchivo;
      $sqlImg = "UPDATE M900_NOTICIAS SET IMAGEN = :imagen WHERE ID_NOTICIA = :id";
      $stmtImg = $conn->prepare($sqlImg);
      $stmtImg->bindParam(":imagen", $rutaRelativa);
      $stmtImg->bindParam(":id", $id);
      $stmtImg->execute();
    }
  }

  header("Location: noticias-internas.php?msg=noticia_editada");
  exit;
}

// --- Eliminar noticia (marcar como inactiva) ---
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  $sql = "UPDATE M900_NOTICIAS SET ACTIVA = 0 WHERE ID_NOTICIA = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  header("Location: noticias-internas.php?msg=noticia_eliminada");
  exit;
}

// --- Consultar todas las noticias activas ---
$sql = "SELECT * FROM M900_NOTICIAS 
        WHERE ACTIVA = 1 
        ORDER BY FECHA_CREACION DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === SUBIR ORGANIGRAMA ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['subirOrganigrama'])) {

  if (isset($_FILES["organigrama"]) && $_FILES["organigrama"]["error"] === UPLOAD_ERR_OK) {

    $ext = strtolower(pathinfo($_FILES["organigrama"]["name"], PATHINFO_EXTENSION));

    if ($ext === "pdf") {

      $directorioOrg = dirname(__DIR__) . "/documentos/";
      if (!file_exists($directorioOrg)) {
        mkdir($directorioOrg, 0777, true);
      }

      $rutaDestinoOrg = $directorioOrg . "Organigrama.pdf";

      move_uploaded_file($_FILES["organigrama"]["tmp_name"], $rutaDestinoOrg);

      header("Location: noticias-internas.php?msg=organigrama_subido");
      exit;
    }
  }

  header("Location: noticias-internas.php?msg=organigrama_error");
  exit;
}
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