<?php
session_start();

// ⚠️ Evitar caché del navegador (importante para cerrar sesión correctamente)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Si no hay sesión, redirigir al login
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php?error=1");
  exit();
}

require "../php/conexion.php";
require "../php/cumpleaños-noticias-aniversario.php";
require "../php/contador-accidentes.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Intranet Exgadet</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/usuario.css">
  <style></style>
</head>

<body>
  <?php include './nav.php'; ?>

  <div class="wrapper">
    <div class="contenido">

      <!-- Sección de botones destacados -->
      <section class="botones-destacados">
        <h2>📌 Accesos rápidos</h2>

        <div class="botones-grid">

          <!-- ✔ Tarjeta 1 -->
          <a href="https://capacitacionesexgadet.com.ar/capacitacion/" class="tarjeta-boton">
            <i class="fa-solid fa-chalkboard-user"></i>
            <h3>NUESTRAS CAPACITACIONES</h3>
            <p>Accedé a las formaciones y cursos internos disponibles.</p>
          </a>

          <!-- ✔ Tarjeta 2 -->
          <a href="https://app.miportalonline.com/login.htm" class="tarjeta-boton">
            <i class="fa-solid fa-file-signature"></i>
            <h3>Recordá firmar tu recibo</h3>
            <p>Ingresá para completar la firma de tu recibo mensual.</p>
          </a>

          <!-- ❌ Se elimina esta tarjeta (Comunicado SGI)
    <a href="./comunicado-sgi.php" class="tarjeta-boton">
      <i class="fa-solid fa-lightbulb"></i>
      <h3>Comunicacion SGI</h3>
      <p>Mantenernos informados es parte de trabajar con calidad y seguridad.</p>
    </a>
    -->

          <!-- ✔ Tarjeta 3 -->
          <a href="./organigrama.php" class="tarjeta-boton">
            <i class="fa-solid fa-sitemap"></i>
            <h3>Organigrama Institucional</h3>
            <p>Comunicate siempre que tengas alguna consulta con el area necesaria.</p>
          </a>

          <!-- ✔ Tarjeta del contador integrado -->
          <a href="reiniciar-contador.php" class="tarjeta-boton tarjeta-contador">
            <i class="fa-solid fa-shield-heart"></i>
            <h3>Días sin accidentes</h3>
            <p class="numero-dias"><?php echo $diasActuales; ?> días</p>
          </a>

        </div>
      </section>

      <!-- Noticias internas RRHH Y CALIDAD-->
      <section class="noticias">
        <h2>📰 Noticias y Novedades</h2>

        <?php if (empty($noticias)): ?>
          <p>No hay noticias activas en este momento.</p>
        <?php else: ?>
          <div class="noticias-carrusel">
            <button class="carrusel-btn prev"><i class="fa-solid fa-chevron-left"></i></button>

            <div class="noticias-slider">
              <?php foreach ($noticias as $n): ?>
                <?php
                $tituloAttr = htmlspecialchars($n['TITULO'], ENT_QUOTES, 'UTF-8');
                $cuerpoFull = $n['CUERPO'];
                $cuerpoAttr = htmlspecialchars(mb_strimwidth($cuerpoFull, 0, 100, "..."), ENT_QUOTES, 'UTF-8');
                $cuerpoFullAttr = htmlspecialchars($cuerpoFull, ENT_QUOTES, 'UTF-8');
                $fechaAttr = date("d/m/Y", strtotime($n['FECHA_CREACION']));
                $imgAttr = !empty($n['IMAGEN']) ? htmlspecialchars($n['IMAGEN'], ENT_QUOTES, 'UTF-8') : '';
                ?>
                <div class="noticia"
                  data-titulo="<?= $tituloAttr ?>"
                  data-cuerpo="<?= $cuerpoAttr ?>"
                  data-cuerpo-completo="<?= $cuerpoFullAttr ?>"
                  data-fecha="<?= $fechaAttr ?>"
                  data-imagen="<?= $imgAttr ?>">
                  <?php if (!empty($imgAttr)): ?>
                    <img src="<?= $imgAttr; ?>" alt="Imagen de la noticia">
                  <?php endif; ?>
                  <h3><?= $tituloAttr; ?></h3>
                  <small><?= $fechaAttr; ?></small>
                </div>
              <?php endforeach; ?>
            </div>

            <button class="carrusel-btn next"><i class="fa-solid fa-chevron-right"></i></button>
          </div>

        <?php endif; ?>
      </section>

      <section class="beneficios">
        <h2>💎 Beneficios para nuestros empleados</h2>

        <div class="beneficios-grid">
          <!-- MUTUAL -->
          <div class="beneficio">
            <img src="../imagenesintranet/beneficios/mutual.png" alt="Mutual">
            <h3>Mutual Gas</h3>
            <p>Accedé a préstamos personales, descuentos en comercios, seguros y más ventajas exclusivas para empleados.</p>
            <!-- <a href="#" class="btn-beneficio">Ver más</a> -->
          </div>

          <!-- GOLOMAX -->
          <div class="beneficio">
            <img src="../imagenesintranet/beneficios/golomax1.png" alt="Golomax">
            <h3>Golomax</h3>
            <p>Descuentos especiales en golosinas, snacks y productos seleccionados para empleados Exgadet.</p>
            <!-- <a href="#" class="btn-beneficio">Ver más</a> -->
          </div>

          <!-- MOVISTAR -->
          <div class="beneficio">
            <img src="../imagenesintranet/beneficios/movistar.png" alt="Movistar">
            <h3>Movistar</h3>
            <p>Planes corporativos con tarifas preferenciales en telefonía móvil y beneficios exclusivos en equipos.</p>
            <!-- <a href="#" class="btn-beneficio">Ver más</a> -->
          </div>
        </div>
      </section>

      <!-- Cumpleaños y Aniversarios -->
      <section class="eventos">

        <div class="evento nuevo-ingreso">
          <h3>🏁 Nuevo Ingreso</h3>
          <?php if (count($nuevosIngresos) > 1): ?>
            <div class="carrusel" id="carruselNuevos">
              <?php foreach ($nuevosIngresos as $ni): ?>
                <div class="slide">
                  <img src="../imagenesintranet/usuarios/<?= $ni['LEGAJO'] ?>.jpg"
                    onerror="this.src='../imagenesintranet/usuario.jpg'">
                  <p><strong><?= htmlspecialchars($ni["NOMBRE"] . " " . $ni["APELLIDO"]) ?></strong></p>
                  <p><?= htmlspecialchars($ni["SECTOR"]) ?> - <?= htmlspecialchars($ni["CARGO"]) ?></p>
                  <p class="fecha">📅 Ingreso: <?= (new DateTime($ni["FECHA_INGRESO"]))->format("d/m/Y") ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php elseif (!empty($nuevosIngresos)): ?>
            <?php $ni = $nuevosIngresos[0]; ?>
            <div class="slide active">
              <img src="../imagenesintranet/usuarios/<?= $ni['LEGAJO'] ?>.jpg"
                onerror="this.src='../imagenesintranet/usuario.jpg'">
              <p><strong><?= htmlspecialchars($ni["NOMBRE"] . " " . $ni["APELLIDO"]) ?></strong></p>
              <p><?= htmlspecialchars($ni["SECTOR"]) ?> - <?= htmlspecialchars($ni["CARGO"]) ?></p>
              <p class="fecha">📅 Ingreso: <?= (new DateTime($ni["FECHA_INGRESO"]))->format("d/m/Y") ?></p>
            </div>
          <?php else: ?>
            <p>Hoy no hay nuevos ingresos.</p>
          <?php endif; ?>
        </div>

        <!-- Cumpleaños de Hoy -->
        <div class="evento">
          <h3>🎈 Cumpleaños de hoy</h3>
          <?php if (count($cumplesHoy) > 1): ?>
            <div class="carrusel" id="carruselCumpleHoy">
              <?php foreach ($cumplesHoy as $ch): ?>
                <div class="slide">
                  <img src="../imagenesintranet/usuarios/<?= $ch['LEGAJO'] ?>.jpg"
                    onerror="this.src='../imagenesintranet/usuario.jpg'">
                  <p><strong><?= htmlspecialchars($ch["NOMBRE"] . " " . $ch["APELLIDO"]) ?></strong></p>
                  <p><?= htmlspecialchars($ch["SECTOR"]) ?> - <?= htmlspecialchars($ch["CARGO"]) ?></p>
                  <p class="fecha">🎉 ¡Feliz cumpleaños!</p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php elseif (!empty($cumplesHoy)): ?>
            <?php $ch = $cumplesHoy[0]; ?>
            <div class="slide active">
              <img src="../imagenesintranet/usuarios/<?= $ch['LEGAJO'] ?>.jpg"
                onerror="this.src='../imagenesintranet/usuario.jpg'">
              <p><strong><?= htmlspecialchars($ch["NOMBRE"] . " " . $ch["APELLIDO"]) ?></strong></p>
              <p><?= htmlspecialchars($ch["SECTOR"]) ?> - <?= htmlspecialchars($ch["CARGO"]) ?></p>
              <p class="fecha">🎉 ¡Feliz cumpleaños!</p>
            </div>
          <?php else: ?>
            <p>Hoy no hay cumpleaños.</p>
          <?php endif; ?>
        </div>

        <!-- Aniversarios de Hoy -->
        <div class="evento">
          <h3>🏆 Aniversarios de hoy</h3>
          <?php if (count($anivsHoy) > 1): ?>
            <div class="carrusel" id="carruselAnivHoy">
              <?php foreach ($anivsHoy as $ah): ?>
                <div class="slide">
                  <img src="../imagenesintranet/usuarios/<?= $ah['LEGAJO'] ?>.jpg"
                    onerror="this.src='../imagenesintranet/usuario.jpg'">
                  <p><strong><?= htmlspecialchars($ah["NOMBRE"] . " " . $ah["APELLIDO"]) ?></strong></p>
                  <p><?= htmlspecialchars($ah["SECTOR"]) ?> - <?= htmlspecialchars($ah["CARGO"]) ?></p>
                  <p class="fecha">🎊 ¡Feliz aniversario!</p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php elseif (!empty($anivsHoy)): ?>
            <?php $ah = $anivsHoy[0]; ?>
            <div class="slide active">
              <img src="../imagenesintranet/usuarios/<?= $ah['LEGAJO'] ?>.jpg"
                onerror="this.src='../imagenesintranet/usuario.jpg'">
              <p><strong><?= htmlspecialchars($ah["NOMBRE"] . " " . $ah["APELLIDO"]) ?></strong></p>
              <p><?= htmlspecialchars($ah["SECTOR"]) ?> - <?= htmlspecialchars($ah["CARGO"]) ?></p>
              <p class="fecha">🎊 ¡Feliz aniversario!</p>
            </div>
          <?php else: ?>
            <p>Hoy no hay aniversarios.</p>
          <?php endif; ?>
        </div>

        <!-- Proximo Cumpleaños -->
        <div class="evento">
          <h3>🎂 Próximos cumpleaños</h3>
          <?php if (count($proximosCumples) > 1): ?>
            <div class="carrusel" id="carruselCumple">
              <?php foreach ($proximosCumples as $c): ?>
                <div class="slide">
                  <img src="../imagenesintranet/usuarios/<?= $c['LEGAJO'] ?>.jpg"
                    onerror="this.src='../imagenesintranet/usuario.jpg'">
                  <p><strong><?= htmlspecialchars($c["NOMBRE"] . " " . $c["APELLIDO"]) ?></strong></p>
                  <p><?= htmlspecialchars($c["SECTOR"]) ?> - <?= htmlspecialchars($c["CARGO"]) ?></p>
                  <p class="fecha"><?= $c["PROX_FECHA"]->format("d/m/Y") ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php elseif (!empty($proximosCumples)): ?>
            <?php $c = $proximosCumples[0]; ?>
            <div class="slide active">
              <img src="../imagenesintranet/usuarios/<?= $c['LEGAJO'] ?>.jpg"
                onerror="this.src='../imagenesintranet/usuario.jpg'">
              <p><strong><?= htmlspecialchars($c["NOMBRE"] . " " . $c["APELLIDO"]) ?></strong></p>
              <p><?= htmlspecialchars($c["SECTOR"]) ?> - <?= htmlspecialchars($c["CARGO"]) ?></p>
              <p class="fecha"><?= $c["PROX_FECHA"]->format("d/m/Y") ?></p>
            </div>
          <?php else: ?>
            <p>No hay cumpleaños próximos.</p>
          <?php endif; ?>
        </div>

        <!-- Aniversarios -->
        <div class="evento">
          <h3>🎉 Próximos aniversarios</h3>
          <?php if (count($proximosAnivs) > 1): ?>
            <div class="carrusel" id="carruselAniv">
              <?php foreach ($proximosAnivs as $a): ?>
                <div class="slide">
                  <img src="../imagenesintranet/usuarios/<?= $a['LEGAJO'] ?>.jpg"
                    onerror="this.src='../imagenesintranet/usuario.jpg'">
                  <p><strong><?= htmlspecialchars($a["NOMBRE"] . " " . $a["APELLIDO"]) ?></strong></p>
                  <p><?= htmlspecialchars($a["SECTOR"]) ?> - <?= htmlspecialchars($a["CARGO"]) ?></p>
                  <p class="fecha"><?= $a["PROX_FECHA"]->format("d/m/Y") ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php elseif (!empty($proximosAnivs)): ?>
            <?php $a = $proximosAnivs[0]; ?>
            <div class="slide active">
              <img src="../imagenesintranet/usuarios/<?= $a['LEGAJO'] ?>.jpg"
                onerror="this.src='../imagenesintranet/usuario.jpg'">
              <p><strong><?= htmlspecialchars($a["NOMBRE"] . " " . $a["APELLIDO"]) ?></strong></p>
              <p><?= htmlspecialchars($a["SECTOR"]) ?> - <?= htmlspecialchars($a["CARGO"]) ?></p>
              <p class="fecha"><?= $a["PROX_FECHA"]->format("d/m/Y") ?></p>
            </div>
          <?php else: ?>
            <p>No hay aniversarios próximos.</p>
          <?php endif; ?>
        </div>

        <!-- Empleado Efectivo 
        <div class="evento">
          <h3>🎉 Empleado Efectivo</h3>

          <?php if (!empty($proximosEfectivos)): ?>
            <?php if (count($proximosEfectivos) > 1): ?>
              <div class="carrusel" id="carruselEfectivos">
                <?php foreach ($proximosEfectivos as $ef): ?>
                  <div class="slide">
                    <img src="../imagenesintranet/usuarios/<?= $ef['LEGAJO'] ?>.jpg"
                      onerror="this.src='../imagenesintranet/usuario.jpg'">
                    <p><strong><?= htmlspecialchars($ef['NOMBRE'] . ' ' . $ef['APELLIDO']) ?></strong></p>
                    <p><?= htmlspecialchars($ef['SECTOR']) ?> - <?= htmlspecialchars($ef['CARGO']) ?></p>
                    <p class="fecha">🗓️ Será efectivo: <?= $ef['FECHA_EFECTIVA']->format('d/m/Y') ?></p>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <?php $ef = $proximosEfectivos[0]; ?>
              <div class="slide active">
                <img src="../imagenesintranet/usuarios/<?= $ef['LEGAJO'] ?>.jpg"
                  onerror="this.src='../imagenesintranet/usuario.jpg'">
                <p><strong><?= htmlspecialchars($ef['NOMBRE'] . ' ' . $ef['APELLIDO']) ?></strong></p>
                <p><?= htmlspecialchars($ef['SECTOR']) ?> - <?= htmlspecialchars($ef['CARGO']) ?></p>
                <p class="fecha">🗓️ Será efectivo: <?= $ef['FECHA_EFECTIVA']->format('d/m/Y') ?></p>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <p>No hay próximos empleados efectivos.</p>
          <?php endif; ?>
        </div> -->

      </section>
    </div>

    <!-- Panel Usuario -->
    <aside class="panel-usuario" id="panelUsuario">
      <img src="<?php echo $fotoPerfil; ?>?t=<?php echo time(); ?>" alt="Foto de usuario">
      <h2>Bienvenido, <br><?php echo htmlspecialchars($nombreCompleto); ?> 👋</h2>
      <p>Sector: <?php echo htmlspecialchars($sector); ?></p>
      <p>Cargo: <?php echo htmlspecialchars($cargo); ?></p>
      <div class="botones">
        <a href="perfil.php">Mi perfil</a>
        <a href="reserva.php">Reserva de sala de reunión</a>
        <a href="incidencias.php">Incidencias</a>
        <!-- <a href="datos-bot.php">Datos Bot</a> -->
        <a href="https://exgadet.dyndns.org/(S(c5odmjdjzm44w304n25uhown))/FORMS/LOGIN.aspx?ReturnUrl=%2fFORMS%2fP008_OPERARIOS.aspx">Puntos Operario</a>

        <?php if (strtoupper($sector) === "RRHH"): ?>
          <a href="ver-denuncias.php">Ver denuncias</a>
        <?php else: ?>
          <a href="denuncias.php">Buzón de denuncias</a>
        <?php endif; ?>

        <?php if ($cargo === "RRHH" || strtoupper($sector) === "RRHH"): ?>
          <a href="noticias-internas.php">Noticias internas</a>
          <a href="cv-recibidos.php">CV Recibidos</a>
          <a href="crear-puesto.php">Crear puesto</a>
        <?php endif; ?>

        <?php if (
          strtoupper($sector) === "OPERACIONES" &&
          in_array(strtoupper($cargo), ["ANALISTA", "SUPERVISOR"])
        ): ?>
          <a href="analisis-incidencias.php">Análisis de incidencias</a>
        <?php endif; ?>

        <?php if (
          strtoupper($sector) === "SISTEMA DE GESTION INTEGRADO" &&
          in_array(strtoupper($cargo), ["JEFE", "AUDITOR", "ANALISTA", "TECNICO EN HIG Y SEG"])
        ): ?>
          <a href="reiniciar-contador.php">Reiniciar contador</a>
          <!-- <a href="subir-comunicado-sgi.php">Subir comunicado SGI</a> -->
          <a href="noticias-internas.php">Novedades</a>
          <!-- <a href="noticias-SGI.php">Noticias SGI</a> -->
          <a href="crear-servicio-obras-flota.php">Crear Servicio/Obra/Flota</a>
        <?php endif; ?>
      </div>
    </aside>
  </div>

  <!-- Botón flotante para móviles -->
  <button class="toggle-usuario" id="toggleUsuario" title="Panel de usuario">
    <i class="fa-solid fa-user"></i>
  </button>

  <!-- 🪟 Popup Noticias -->
  <div class="popup-noticia" id="popupNoticia">
    <div class="popup-contenido">
      <button class="cerrar">&times;</button>
      <img id="popupImagen" src="" alt="Imagen de la noticia">
      <h3 id="popupTitulo"></h3>
      <p id="popupFecha"></p>
      <p id="popupCuerpo"></p>
    </div>
  </div>

  <!-- Popup de felicitación de aniversario -->
  <div id="popupAniversario" class="popup">
    <div class="popup-content">
      <h2>🎉 ¡Feliz aniversario, <?php echo htmlspecialchars($nombreCompleto); ?>! 🎉</h2>
      <p>Gracias por un año más de compromiso y dedicación en Exgadet S.A.</p>
      <p>¡Que sigan muchos éxitos más! 🚀</p>
      <button id="cerrarPopup" class="btn-cerrar">Cerrar</button>
    </div>
  </div>

  <!-- Popup de felicitación de cumpleanios -->
  <div id="popupCumpleanio" class="popup">
    <div class="popup-content">
      <h2>🎉 ¡Feliz cumpleaños, <?php echo htmlspecialchars($nombreCompleto); ?>! 🎉</h2>
      <p>Gracias por un año más de compromiso y dedicación en Exgadet S.A.</p>
      <p>¡Que sigan muchos éxitos más! 🚀</p>
      <p>Disfrutá tu día rodeado de alegría, buenos momentos y muchas sonrisas. 🎈</p>
      <button id="cerrarPopupCump" class="btn-cerrar">Cerrar</button>
    </div>
  </div>

  <!-- Popup de empleado efectivo -->
  <div id="popupEfectivo" class="popup">
    <div class="popup-content">
      <h2>🎉 ¡Felicitaciones <?php echo htmlspecialchars($nombreCompleto); ?>! 🎉</h2>
      <p>Hoy te convertís en empleado efectivo de Exgadet S.A.</p>
      <p>Tu esfuerzo y compromiso son muy valorados por toda la compañía. 🙌</p>
      <p>¡A seguir creciendo juntos! 🚀</p>
      <button id="cerrarPopupEfec" class="btn-cerrar">Cerrar</button>
    </div>
  </div>

  <!-- Confeti -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script>
    window.esSuAniversario = <?php echo $esSuAniversario ? 'true' : 'false'; ?>;
    window.esSuCumpleanios = <?php echo $esSuCumpleanios ? 'true' : 'false'; ?>;
    window.esSuEfectivacion = <?php echo $esSuEfectivacion ? 'true' : 'false'; ?>;
  </script>
  <script src="../js/carruseles-eventos.js"></script>
  <script src="../js/popup-noticias.js"></script>
  <script src="../js/carrusel-noticias.js"></script>

</body>

</html>