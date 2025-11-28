<?php
session_start();

// Ruta base de los CV
$baseDir = __DIR__ . "/../CV";
$carpetas = [];

// --- Eliminar archivo ---
if (isset($_GET['eliminar']) && isset($_GET['carpeta'])) {
  $carpeta = basename($_GET['carpeta']);
  $archivo = basename($_GET['eliminar']);
  $rutaArchivo = "$baseDir/$carpeta/$archivo";

  if (file_exists($rutaArchivo)) {
    unlink($rutaArchivo);
    // También eliminamos el JSON asociado si existe
    $jsonAsociado = "$baseDir/$carpeta/" . pathinfo($archivo, PATHINFO_FILENAME) . ".json";
    if (file_exists($jsonAsociado)) {
      unlink($jsonAsociado);
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=archivo_eliminado");
    exit;
  }
}

// --- Escanear carpetas y archivos ---
if (is_dir($baseDir)) {
  foreach (scandir($baseDir) as $carpeta) {
    if ($carpeta !== "." && $carpeta !== ".." && is_dir("$baseDir/$carpeta")) {
      $archivos = [];
      foreach (scandir("$baseDir/$carpeta") as $file) {
        if ($file !== "." && $file !== ".." && is_file("$baseDir/$carpeta/$file") && !str_ends_with($file, ".json")) {
          $archivos[] = $file;
        }
      }
      if (!empty($archivos)) {
        sort($archivos);
        $carpetas[$carpeta] = $archivos;
      }
    }
  }
  ksort($carpetas);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CV Recibidos</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/cv-recibidos.css">
</head>

<body>
  <?php include './nav.php'; ?>

  <main>
    <div class="header-cv">
      <h1>📂 CV Recibidos</h1>
      <a href="usuario.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'archivo_eliminado'): ?>
      <div class="msg">🗑 El archivo se eliminó correctamente.</div>
    <?php endif; ?>

    <?php if (empty($carpetas)): ?>
      <p>No se encontraron CVs en el sistema.</p>
    <?php else: ?>
      <?php foreach ($carpetas as $carpeta => $archivos): ?>
        <div class="carpeta">
          <h2>📌 <?php echo htmlspecialchars($carpeta); ?></h2>
          <ul class="lista-cv">
            <?php foreach ($archivos as $cv): ?>
              <?php
              $jsonPath = "$baseDir/$carpeta/" . pathinfo($cv, PATHINFO_FILENAME) . ".json";
              $datos = null;
              if (file_exists($jsonPath)) {
                $datos = json_decode(file_get_contents($jsonPath), true);
              }
              ?>
              <li>
                <div class="cv-info">
                  <div>
                    <i class="fa-solid fa-file"></i>
                    <a href="<?php echo "../CV/$carpeta/$cv"; ?>" target="_blank">
                      <?php echo htmlspecialchars($cv); ?>
                    </a>
                  </div>
                  <div class="acciones">
                    <a class="btn-eliminar"
                      href="?carpeta=<?php echo urlencode($carpeta); ?>&eliminar=<?php echo urlencode($cv); ?>"
                      onclick="return confirm('¿Seguro que deseas eliminar este archivo?')">
                      Eliminar
                    </a>
                    <?php if ($datos): ?>
                      <button type="button" class="toggle" onclick="this.closest('li').querySelector('.datos-cv').classList.toggle('mostrar')">
                        Ver datos
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
                <?php if ($datos): ?>
                  <div class="datos-cv">
                    <p><b>Nombre:</b> <?= htmlspecialchars($datos['nombre']) ?></p>
                    <p><b>Apellido:</b> <?= htmlspecialchars($datos['apellido']) ?></p>
                    <p><b>Localidad:</b> <?= htmlspecialchars($datos['localidad']) ?></p>
                    <p><b>Edad:</b> <?= htmlspecialchars($datos['edad']) ?></p>
                    <p><b>Descripción:</b> <?= nl2br(htmlspecialchars($datos['descripcion'])) ?></p>
                  </div>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>
</body>

</html>