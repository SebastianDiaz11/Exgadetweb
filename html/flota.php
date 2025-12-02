<?php
require "../php/conexion.php";

if (!$conn) {
  die("⚠️ No se pudo conectar a la base de datos.");
}

require "../php/traer-flota.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flota y Maquinaria | Exgadet S.A.</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/flota.css">
</head>

<body>

  <?php include './nav.php'; ?>

  <header>
    <h1>Flota y Maquinaria</h1>
    <p>Recursos técnicos y operativos que garantizan eficiencia y capacidad en cada proyecto.</p>
  </header>

  <div class="overlay"></div>

  <section>
    <!-- 🔸 TARJETAS ESTÁTICAS -->
    <div class="card">
      <i class="fa-solid fa-truck icon"></i>
      <img src="../imagenes/obras/flota1.png" alt="Flota de Camiones">
      <h2>NUESTRA FLOTA VEHICULAR</h2>
      <p>Más de 100 vehículos propios entre camiones, hidrogrúas, tuneleras, retroexcavadoras, pick ups y furgones.</p>
      <div class="card-content">
        <div class="extra-images">
          <img src="../imagenes/obras/flota1-1.png" alt="">
          <img src="../imagenes/obras/flota1-2.png" alt="">
        </div>
        <button class="btn-volver">Cerrar</button>
      </div>
    </div>

    <div class="card">
      <i class="fa-solid fa-helmet-safety icon"></i>
      <img src="../imagenes/obras/flota2.png" alt="Maquinaria pesada">
      <h2>NUESTRAS MAQUINARIAS</h2>
      <p>Tuneleras inteligentes, bod-cat, tractores, compresores y retroexcavadoras para la ejecución de tareas especializadas.</p>
      <div class="card-content">
        <div class="extra-images">
          <img src="../imagenes/obras/flota2-1.png" alt="">
          <img src="../imagenes/obras/flota2-2.png" alt="">
        </div>
        <button class="btn-volver">Cerrar</button>
      </div>
    </div>

    <!-- 🔸 TARJETAS DINÁMICAS DESDE BD -->
    <?php foreach ($flotas as $index => $f): 
      $imgPrincipal = $principales[$index] ?? "";
      $secundariasFlota = array_filter($secundarias, fn($x) => str_starts_with(basename($x), pathinfo($imgPrincipal, PATHINFO_FILENAME)));
    ?>
      <div class="card">
        <?php if (!empty($f['EMOJI'])): ?>
          <i class="<?= htmlspecialchars($f['EMOJI']) ?> icon"></i>
        <?php endif; ?>
        <img src="<?= htmlspecialchars($imgPrincipal) ?>" alt="">
        <h2><?= htmlspecialchars($f['TITULO']) ?></h2>
        <p><?= htmlspecialchars($f['CUERPO']) ?></p>
        <div class="card-content">
          <?php if (!empty($secundariasFlota)): ?>
            <div class="extra-images">
              <?php foreach ($secundariasFlota as $sf): ?>
                <img src="<?= htmlspecialchars($sf) ?>" alt="">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <button class="btn-volver">Cerrar</button>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <?php include './footer.php'; ?>

  <script src="../js/popup-flota.js"></script>

</body>

</html>