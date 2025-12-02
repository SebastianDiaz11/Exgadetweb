<?php
require "../php/conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo conectar a la base de datos.");
}

require "../php/traer-servicios.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Servicios | Exgadet S.A.</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/servicios.css">
</head>

<body>
  <?php include './nav.php'; ?>

  <header>
    <h1>Servicios</h1>
    <p>Más de 30 años brindando soluciones integrales en gas, infraestructura y mantenimiento.</p>
  </header>

  <section id="servicios">
    <!-- ===== TARJETAS DE MUESTRA EXISTENTES ===== -->
    <!-- (Dejé tal cual tu contenido de ejemplo) -->

    <!-- ===== SERVICIO 1 ===== -->
    <div class="card"
      data-title="Mantenimiento de Redes de Gas"
      data-desc="Construcción y mantenimiento de redes de gas. Incluye renovación de servicios y eliminación de fugas."
      data-comitente="Naturgy BAN S.A."
      data-images="../imagenes/obras/servicios1-1.png,../imagenes/obras/servicios1-2.png,../imagenes/obras/servicios1-3.png">
      <i class="fa-solid fa-network-wired"></i>
      <img src="../imagenes/obras/servicios1.png" alt="">
      <h2>Mantenimiento de Redes de Gas</h2>
      <p>Construcción y mantenimiento de redes de gas. Incluye renovación de servicios y eliminación de fugas.</p>
      <p><strong>Comitente:</strong> Naturgy BAN S.A.</p>
    </div>

    <!-- ===== SERVICIO 2 ===== -->
    <div class="card"
      data-title="Mantenimiento Correctivo y Preventivo"
      data-desc="Renovación de redes, mantenimiento de módulos, reconexiones y cortes domiciliarios."
      data-comitente="MetroGas S.A."
      data-images="../imagenes/obras/servicios2-1.png,../imagenes/obras/servicios2-2.png">
      <i class="fa-solid fa-screwdriver-wrench"></i>
      <img src="../imagenes/obras/servicios2.png" alt="">
      <h2>Mantenimiento Correctivo y Preventivo</h2>
      <p>Renovación de redes, mantenimiento de módulos, reconexiones y cortes domiciliarios.</p>
      <p><strong>Comitente:</strong> MetroGas S.A.</p>
    </div>

    <!-- ===== SERVICIO 3 ===== -->
    <div class="card"
      data-title="Operaciones Domiciliarias"
      data-desc="Servicios de interrupción, rehabilitación y relevamiento de medidores domiciliarios."
      data-comitente="Naturgy BAN S.A."
      data-images="../imagenes/obras/servicios3-1.png,../imagenes/obras/servicios3-2.png">
      <i class="fa-solid fa-house-chimney"></i>
      <img src="../imagenes/obras/servicios3.png" alt="">
      <h2>Operaciones Domiciliarias</h2>
      <p>Servicios de interrupción, rehabilitación y relevamiento de medidores domiciliarios.</p>
      <p><strong>Comitente:</strong> Naturgy BAN S.A.</p>
    </div>

    <!-- ===== SERVICIO 4 ===== -->
    <div class="card"
      data-title="Detección de Fugas"
      data-desc="Servicio de detección sistemática de fugas con tecnología avanzada."
      data-comitente="Naturgy BAN S.A."
      data-images="../imagenes/obras/servicios4-1.png,../imagenes/obras/servicios4-2.png,../imagenes/obras/servicios4-3.png">
      <i class="fa-solid fa-fire-extinguisher"></i>
      <img src="../imagenes/obras/servicios4.png" alt="">
      <h2>Detección de Fugas</h2>
      <p>Servicio de detección sistemática de fugas con tecnología avanzada.</p>
      <p><strong>Comitente:</strong> Naturgy BAN S.A.</p>
    </div>

    <!-- ====== DINÁMICOS DESDE BD ====== -->
    <?php foreach ($cardsDinamicas as $c): 
      // construir data-images con secundarias
      $dataImages = '';
      if (!empty($c['imgsSecundarias'])) {
        $dataImages = implode(',', array_map(function($p){ return htmlspecialchars($p, ENT_QUOTES); }, $c['imgsSecundarias']));
      }
    ?>
      <div class="card"
        data-title="<?= htmlspecialchars($c['titulo']) ?>"
        data-desc="<?= htmlspecialchars($c['cuerpo']) ?>"
        data-comitente="<?= htmlspecialchars($c['comitente']) ?>"
        data-images="<?= $dataImages ?>">
        <?php if (!empty($c['emoji'])): ?>
          <i class="<?= htmlspecialchars($c['emoji']) ?>"></i>
        <?php endif; ?>
        <?php if (!empty($c['imgPrincipal'])): ?>
          <img src="<?= htmlspecialchars($c['imgPrincipal']) ?>" alt="">
        <?php endif; ?>
        <h2><?= htmlspecialchars($c['titulo']) ?></h2>
        <p><?= htmlspecialchars($c['cuerpo']) ?></p>
        <p><strong>Comitente:</strong> <?= htmlspecialchars($c['comitente']) ?></p>
      </div>
    <?php endforeach; ?>

  </section>

  <!-- ===== MODAL ===== -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal" id="modalContent">
      <h2 id="modalTitle"></h2>
      <p id="modalDesc"></p>
      <p><strong>Comitente:</strong> <span id="modalComitente"></span></p>
      <div class="extra-images" id="modalImages"></div>
      <button class="btn-volver" id="btnVolver">Volver</button>
    </div>
  </div>

  <?php include './footer.php'; ?>

  <script src="../js/popup-servicios.js"></script>
</body>
</html>
