<?php
require "../php/conexion.php";

if (!$conn) {
  die("⚠️ No se pudo conectar a la base de datos.");
}

require "../php/traer-obra.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Obras | Exgadet S.A.</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/obras.css">
</head>

<body>

  <?php include './nav.php'; ?>

  <header>
    <h1>Obras</h1>
    <p>Proyectos de gran magnitud que refuerzan la infraestructura energética del país.</p>
  </header>

  <section>
    <div class="obra" data-obra="1">
      <img src="../imagenes/obras/obra1.png" alt="">
      <div class="obra-content">
        <h2>GASODUCTO Y RED EN PATRICIOS, 9 DE JULIO</h2>
        <p>11.552 metros de cañerías PE, estaciones de medición y regulación de presión.</p>
        <p><strong>Comitente:</strong> BAGSA</p>
        <p><strong>Plazo:</strong> 14/08/2023 - 30/06/2025</p>
      </div>
    </div>

    <div class="obra" data-obra="2">
      <img src="../imagenes/obras/obra2.png" alt="">
      <div class="obra-content">
        <h2>GASODUCTO Y RED DE DISTRIBUCIÓN EN MORENO</h2>
        <p>Red de 9.405 metros de cañerías PE y estación reguladora de presión 25-15 kg/cm2.</p>
        <p><strong>Comitente:</strong> Municipalidad de Moreno</p>
        <p><strong>Plazo:</strong> 20/07/2022 - 19/07/2024</p>
      </div>
    </div>

    <div class="obra" data-obra="3">
      <img src="../imagenes/obras/obra3.png" alt="">
      <div class="obra-content">
        <h2>RENOVACIÓN DE REDES DE BAJA PRESIÓN</h2>
        <p>Banfield - Temperley. Renovación de red de baja presión HºFº a media presión PE.</p>
        <p><strong>Comitente:</strong> MetroGas S.A.</p>
        <p><strong>Plazo:</strong> 01/06/2021 - 31/06/2024</p>
      </div>
    </div>

    <div class="obra" data-obra="4">
      <img src="../imagenes/obras/obra4.png" alt="">
      <div class="obra-content">
        <h2>OBRA GASODUCTO Y RED DE DISTRIBUCIÓN EN MÁXIMO PAZ</h2>
        <p>36.800 metros de cañerías de polietileno, ramal de alimentación y estación de medición.</p>
        <p><strong>Comitente:</strong> BAGSA</p>
      </div>
    </div>

    <div class="obra" data-obra="5">
      <img src="../imagenes/obras/obra5.png" alt="">
      <div class="obra-content">
        <h2>SUMINISTRO DE GAS NATURAL EN LAS LOCALIDADES DE GUAMINÍ Y LAGUNA ALSINA</h2>
        <p>56,90 km de extensión de gasoducto. Construcción de estaciones reductoras y una de medición TGS.</p>
        <p><strong>Comitente:</strong> BAGSA</p>
        <p><strong>Plazo:</strong> 01/08/2018 - 31/08/2021</p>
      </div>
    </div>

    <!-- DINÁMICAS DESDE BD -->
    <?php foreach ($cardsDinamicas as $obra): 
      $imgs = implode(',', array_map(fn($x)=>htmlspecialchars($x,ENT_QUOTES),$obra['imgsSecundarias']));
    ?>
      <div class="obra" 
           data-title="<?= htmlspecialchars($obra['titulo']) ?>"
           data-desc="<?= htmlspecialchars($obra['cuerpo']) ?>"
           data-comitente="<?= htmlspecialchars($obra['comitente']) ?>"
           data-inicio="<?= htmlspecialchars($obra['inicio']) ?>"
           data-final="<?= htmlspecialchars($obra['final']) ?>"
           data-images="<?= $imgs ?>">
        <img src="<?= htmlspecialchars($obra['imgPrincipal']) ?>" alt="">
        <div class="obra-content">
          <h2><?= htmlspecialchars($obra['titulo']) ?></h2>
          <p><?= htmlspecialchars($obra['cuerpo']) ?></p>
          <p><strong>Comitente:</strong> <?= htmlspecialchars($obra['comitente']) ?></p>
          <p><strong>Plazo:</strong> <?= htmlspecialchars($obra['inicio']) ?> - <?= htmlspecialchars($obra['final']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

<!-- Modal -->
  <div class="overlay"></div>
  <div class="modal-obra" id="modalObra">
    <img src="" class="main" alt="">
    <div class="modal-content">
      <h2></h2>
      <p class="descripcion"></p>
      <div class="galeria"></div>
      <button class="btn-volver">Volver</button>
    </div>
  </div>

  <?php include './footer.php'; ?>

  <script>
    const obras = document.querySelectorAll(".obra");
    const overlay = document.querySelector(".overlay");
    const modal = document.getElementById("modalObra");
    const modalImg = modal.querySelector(".main");
    const modalTitulo = modal.querySelector("h2");
    const modalDesc = modal.querySelector(".descripcion");
    const modalGaleria = modal.querySelector(".galeria");
    const btnVolver = modal.querySelector(".btn-volver");

    obras.forEach(obra => {
      obra.addEventListener("click", () => {
        const titulo = obra.dataset.title || obra.querySelector("h2").textContent;
        const desc = obra.dataset.desc || "";
        const comitente = obra.dataset.comitente || "";
        const inicio = obra.dataset.inicio || "";
        const fin = obra.dataset.final || "";
        const imgs = obra.dataset.images ? obra.dataset.images.split(",") : [];

        modalImg.src = obra.querySelector("img").src;
        modalTitulo.textContent = titulo;
        modalDesc.innerHTML = `
          <p>${desc}</p>
          <p><strong>Comitente:</strong> ${comitente}</p>
          <p><strong>Plazo:</strong> ${inicio} - ${fin}</p>
        `;
        modalGaleria.innerHTML = "";
        imgs.forEach(img => {
          const el = document.createElement("img");
          el.src = img;
          modalGaleria.appendChild(el);
        });

        overlay.classList.add("active");
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
      });
    });

    function cerrarModal() {
      modal.classList.remove("active");
      overlay.classList.remove("active");
      document.body.style.overflow = "auto";
    }

    btnVolver.addEventListener("click", cerrarModal);
    overlay.addEventListener("click", cerrarModal);
  </script>
</body>

</html>