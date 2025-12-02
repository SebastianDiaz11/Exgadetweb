<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nosotros - EXGADET S.A</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/sobre-nosotros.css">
</head>

<body>
  <!-- Navegacion -->
  <?php include './nav.php'; ?>

  <!-- Sección Quienes somos + Origen -->
  <section class="info-section">
    <div class="info-container right">
      <div class="info-text">
        <h2>¿Quiénes somos?</h2>
        <p>
          Somos Exgadet S.A., una empresa con más de 30 años de experiencia en el rubro del gas.
          Nos posicionamos como aliados estratégicos de compañías de servicios públicos y privados.
          Brindamos asesoramiento técnico, provisión de personal calificado, soluciones operativas y
          construcciones de proyectos de infraestructura llave en mano a empresas de servicio.
        </p>

        <h2>Origen</h2>
        <p>
          Corría el año 1992 en nuestro país, precisamente en el mes de diciembre; fecha en la que sucedió la
          privatización de la distribución del gas en la República Argentina. Como consecuencia de este hecho, se
          abrió una nueva etapa en la que personal altamente capacitado y con más de 20 años de experiencia en
          esta industria, pudo aplicar sus conocimientos en un nuevo proyecto.
          A tal fin se creó EXGADET S.A., un nuevo concepto de empresa que nuclea gran cantidad de
          colaboradores especializados en temas referentes a la construcción y el mantenimiento de redes de
          baja, media y alta presión; y con conocimiento de materiales de dichas redes (como hierro fundido,
          acero, polietileno, entre otros). Dentro de cada proceso, comenzamos a dominar la operación de
          tecnologías de obturación, corte y remoción en redes con gas activo.
        </p>
      </div>
      <img src="../imagenes/nosotros/origen.jpg" alt="Origen">
    </div>
  </section>

  <!-- Sección Misión y Visión -->
  <section class="mision-vision">
    <div class="mv-card">
      <i class="fa-solid fa-bullseye"></i>
      <h2>Misión</h2>
      <p>
        Ser aliado estratégico de empresas proveedoras de servicios de distribución de gas, ofreciendo
        de manera eficiente soluciones operativas, de capital humano y obras de infraestructura.
      </p>
    </div>
    <div class="mv-card">
      <i class="fa-solid fa-eye"></i>
      <h2>Visión</h2>
      <p>
        Estar entre los referentes en el país como proveedor de servicios, ofreciendo de manera
        eficiente soluciones operativas, de capital humano y obras de infraestructura.
      </p>
    </div>
  </section>

  <!-- Sección ¿Quiénes nos eligen? con carrusel -->
  <section class="clientes">
    <h2>¿Quiénes nos eligen?</h2>
    <div class="carrusel-clientes" id="carrusel-clientes">
      <div class="carrusel-track">
        <img src="../imagenes/nosotros/cliente/Alemarsa.jpg" alt="Cliente 1" data-dark="../imagenes/nosotros/cliente/Alemarsa.png">
        <img src="../imagenes/nosotros/cliente/Camuzzi.png" alt="Cliente 2" data-dark="../imagenes/nosotros/cliente/Camuzzi.png">
        <img src="../imagenes/nosotros/cliente/Bagsa.jpg" alt="Cliente 3" data-dark="../imagenes/nosotros/cliente/Bagsa.png">
        <img src="../imagenes/nosotros/cliente/Metrogas.png" alt="Cliente 4" data-dark="../imagenes/nosotros/cliente/Metrogas-dark.png">
        <img src="../imagenes/nosotros/cliente/municipio de la matanza.jpg" alt="Cliente 5" data-dark="../imagenes/nosotros/cliente/municipio de la matanza.jpg">
        <img src="../imagenes/nosotros/cliente/municipio moreno.png" alt="Cliente 6" data-dark="../imagenes/nosotros/cliente/municipio moreno.png">
        <img src="../imagenes/nosotros/cliente/municipio tigre.png" alt="Cliente 7" data-dark="../imagenes/nosotros/cliente/municipio tigre.png">
        <img src="../imagenes/nosotros/cliente/Naturgy.png" alt="Cliente 8" data-dark="../imagenes/nosotros/cliente/Naturgy.png">
        <img src="../imagenes/nosotros/cliente/TGN.png" alt="Cliente 9" data-dark="../imagenes/nosotros/cliente/TGN.png">

        <!-- 👇 duplicamos para efecto infinito -->
        <img src="../imagenes/nosotros/cliente/Alemarsa.jpg" alt="Cliente 1" data-dark="../imagenes/nosotros/cliente/Alemarsa.png">
        <img src="../imagenes/nosotros/cliente/Camuzzi.png" alt="Cliente 2" data-dark="../imagenes/nosotros/cliente/Camuzzi.png">
        <img src="../imagenes/nosotros/cliente/Bagsa.jpg" alt="Cliente 3" data-dark="../imagenes/nosotros/cliente/Bagsa.png">
        <img src="../imagenes/nosotros/cliente/Metrogas.png" alt="Cliente 4" data-dark="../imagenes/nosotros/cliente/Metrogas-dark.png">
        <img src="../imagenes/nosotros/cliente/municipio de la matanza.jpg" alt="Cliente 5" data-dark="../imagenes/nosotros/cliente/municipio de la matanza.jpg">
        <img src="../imagenes/nosotros/cliente/municipio moreno.png" alt="Cliente 6" data-dark="../imagenes/nosotros/cliente/municipio moreno.png">
        <img src="../imagenes/nosotros/cliente/municipio tigre.png" alt="Cliente 7" data-dark="../imagenes/nosotros/cliente/municipio tigre.png">
        <img src="../imagenes/nosotros/cliente/Naturgy.png" alt="Cliente 8" data-dark="../imagenes/nosotros/cliente/Naturgy.png">
        <img src="../imagenes/nosotros/cliente/TGN.png" alt="Cliente 9" data-dark="../imagenes/nosotros/cliente/TGN.png">
      </div>
    </div>
  </section>

  <!-- Sección Diferenciales -->
  <section class="diferenciales" id="diferenciales">
    <div class="dif-card">
      <h2>¿Qué nos diferencia?</h2>
      <p>
        Nuestro enfoque centrado en el cliente, soluciones a medida y un equipo comprometido con la excelencia.
      </p>
    </div>
    <div class="dif-card">
      <h2>Nuestros Valores</h2>
      <p>
        Honestidad, innovación, trabajo en equipo y responsabilidad social son los pilares que guían cada decisión.
      </p>
    </div>
    <div class="dif-card">
      <h2>RSE</h2>
      <p>
        Apostamos al crecimiento sostenible, cuidando el medioambiente y apoyando causas sociales que generan impacto positivo.
      </p>
    </div>
    <div class="dif-card">
      <h2>Orientacion al cliente</h2>
      <p>
        Nos comprometemos a cumplir con altos estándares técnicos y operativos, alineando nuestras prácticas con los requisitos y procedimientos establecidos por nuestros clientes estratégicos. Buscamos construir relaciones sostenibles y de confianza, brindando soluciones seguras, precisas y oportunas que respondan a sus expectativas y aporten valor a sus operaciones.
      </p>
    </div>
    <div class="dif-card">
      <h2>Excelencia Operativa y Eficiencia</h2>
      <p>
        Impulsamos procesos ágiles, confiables y orientados a resultados, buscando eliminar desperdicios, reducir reprocesos y optimizar el uso de los recursos disponibles.
      </p>
    </div>
    <div class="dif-card">
      <h2>Compromiso con la Calidad</h2>
      <p>
        Asumimos la calidad como un compromiso organizacional nos enfocamos en diseñar, ejecutar y mejorar continuamente nuestros procesos, promoviendo una cultura de excelencia y prevención de errores. Con el fin de ofrecer servicios confiables, eficientes y sostenibles que generan valor para nuestros clientes y partes interesadas.
      </p>
    </div>
  </section>

  <?php include './footer.php'; ?>

  <script src="../js/carruseles.js"></script>
  <script src="../js/menu_hambur-modo.js"></script>
  <script src="../js/nos-eligen.js"></script>
  <!-- <script src="../js/whatsapp-buttons.js"></script> -->
</body>

</html>