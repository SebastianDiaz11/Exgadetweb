<?php
session_start();
require_once "./php/conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $legajo = trim($_POST["text"]);      // número de legajo
  $dni    = trim($_POST["password"]);  // número de documento

  try {
    // ✅ Consulta contra la vista dbo.USUARIOS_DATOS
    $sql = "SELECT LEGAJO, NDNI002, NOMBRE, APELLIDO, SECTOR, CARGO, FECHA_INGRESO, CUMPLEANIOS
                FROM dbo.USUARIOS_DATOS
                WHERE LEGAJO = :legajo AND NDNI002 = :dni";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":legajo", $legajo);
    $stmt->bindParam(":dni", $dni);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      // ✅ Guardar todos los datos en sesión
      $_SESSION["usuario"]   = $user["LEGAJO"];
      $_SESSION["dni"]       = $user["NDNI002"];
      $_SESSION["nombre"]    = $user["NOMBRE"];
      $_SESSION["apellido"]  = $user["APELLIDO"];
      $_SESSION["sector"]    = $user["SECTOR"];
      $_SESSION["cargo"]     = $user["CARGO"];
      $_SESSION["ingreso"]   = $user["FECHA_INGRESO"];
      $_SESSION["cumple"]    = $user["CUMPLEANIOS"];

      // Redirigir al panel del usuario
      header("Location: ./htmlintranet/usuario.php");
      exit;
    } else {
      $errorMsg = "❌ Legajo o DNI incorrecto.";
    }
  } catch (PDOException $e) {
    echo "❌ Error en la consulta: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EXGADET S.A</title>
  <link rel="icon" type="image/png" href="./favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="./favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./css/nav.css">
  <link rel="stylesheet" href="./css/styles.css">
  <style>
    /* ==========================================================
   📱 TABLET (≤ 992px)
   ========================================================== */
@media (max-width: 992px) {
    .trabaja-container {
        grid-template-columns: 1fr;
        text-align: center;
        height: auto;
        padding: 60px 20px;
        gap: 30px;
    }

    .trabaja-img img {
        max-height: 320px;
    }

    .trabaja-info h2 {
        font-size: 1.8rem;
    }

    .trabaja-info p {
        font-size: 1rem;
        padding: 0 10px;
    }

    .trabaja-btn {
        margin: 6px 0;
        width: auto;
    }
}

/* ==========================================================
   📱 CELULAR GRANDE (≤ 768px)
   ========================================================== */
@media (max-width: 768px) {

    .trabaja-container {
        padding: 40px 15px;
    }

    .trabaja-info h2 {
        font-size: 1.6rem;
    }

    .trabaja-info p {
        font-size: 0.95rem;
    }

    .trabaja-img img {
        max-height: 280px;
    }

    .trabaja-btn {
        display: block;
        width: 100%;
        max-width: 250px;
        margin: 8px auto;
        text-align: center;
    }
}

/* ==========================================================
   📱 CELULAR MEDIANO (≤ 576px)
   ========================================================== */
@media (max-width: 576px) {

    .trabaja-info h2 {
        font-size: 1.4rem;
    }

    .trabaja-info p {
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .trabaja-img img {
        max-height: 240px;
    }
}

/* ==========================================================
   📱 CELULAR CHICO (≤ 420px)
   ========================================================== */
@media (max-width: 420px) {

    .trabaja-info h2 {
        font-size: 1.2rem;
    }

    .trabaja-info p {
        font-size: 0.85rem;
    }

    .trabaja-btn {
        padding: 10px 18px;
        font-size: 0.9rem;
    }

    .trabaja-img img {
        max-height: 200px;
    }
}

  </style>

</head>

<body>
  <!-- Navegacion -->
  <nav>
    <!-- Logo -->
    <a href="index.php" class="logo">
      <img src="./imagenes/logo.png" alt="Logo">
    </a>

    <!-- Botón hamburguesa -->
    <button class="hamburger" id="hamburger">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Menú -->
    <ul class="menu" id="menu">
      <li><a href="index.php"><i class="fa-solid fa-house"></i>Inicio</a></li>
      <li><a href="./html/sobre-nosotros.php"><i class="fa-solid fa-users"></i>Nosotros</a></li>
      <li><a href="./html/contacto.php"><i class="fa-solid fa-envelope"></i>Contacto</a></li>
      <li><a href="#" id="temaToggle"><i class="fa-solid fa-moon"></i>Tema</a></li>

      <?php if (isset($_SESSION["usuario"])): ?>
        <!-- Si el usuario está logueado -->
        <li><a href="./htmlintranet/usuario.php" class="perfil"><i class="fa-solid fa-user"></i>Mi Perfil</a></li>
      <?php else: ?>
        <!-- Si el usuario NO está logueado -->
        <li><a href="./html/login.php" class="login"><i class="fa-solid fa-right-to-bracket"></i>Iniciar sesión</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <!-- Carrusel -->
  <section class="carrusel">
    <div class="slide active left" style="background-image: url('./imagenes/carrusel/1.jpg');">
      <div class="content">
        <h2>Bienvenido a EXGADET</h2>
        <p>Con más de 30 años de experiencia en el sector del gas, Exgadet S.A. brinda soluciones integrales en infraestructura, operación y mantenimiento.
          Nos destacamos por nuestra excelencia técnica, personal calificado y compromiso con la innovación y la calidad en cada proyecto.</p>
        <a href="./html/sobre-nosotros.php" class="btn">Conócenos</a>
      </div>
    </div>

    <div class="slide center" style="background-image: url('./imagenes/carrusel/2.jpg');">
      <div class="content">
        <h2>Innovación y Tecnología</h2>
        <p>Nuestra labor se sostiene gracias a una amplia variedad de recursos, destacando una flota propia de más de 200 vehículos que garantizan la eficiencia y capacidad operativa en cada proyecto.
          Además, contamos con sistemas y aplicaciones de desarrollo propio que permiten gestionar la operación y comunicación en tiempo real, optimizando costos, tiempos y control de ejecución en cada tarea.</p>
        <a href="./html/contacto.php" class="btn">Contáctanos</a>
      </div>
    </div>

    <div class="slide right" style="background-image: url('./imagenes/carrusel/3.jpg');">
      <div class="content">
        <h2>Soporte Personalizado</h2>
        <p>En Exgadet S.A. destacamos que nuestro recurso más valioso es el capital humano. Contamos con un equipo de más de 400 colaboradores, capacitados permanentemente a través de una plataforma online propia que nos permite mantenernos actualizados y brindar un soporte personalizado, eficiente y acorde a las demandas del mercado.</p>
        <a href="./html/servicios.php" class="btn">Ver Servicios</a>
      </div>
    </div>

    <!-- Controles -->
    <button class="prev"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="next"><i class="fa-solid fa-chevron-right"></i></button>
  </section>

  <!-- SECCION SGI -->
  <section class="info-section">
    <!-- SERVICIOS -->
    <a href="./html/servicios.php" class="info-link">
      <div class="info-card">
        <div class="info-image">
          <div class="info-overlay">
            <i class="fa-solid fa-hand-holding-water info-icon"></i>
          </div>
          <img src="./imagenes/servicios/servicios.png" alt="Servicios">
        </div>
        <h3>Servicios</h3>
        <p>Brindamos soluciones integrales que impactan directamente en la comunidad.
          Más habitantes alcanzados, más hogares conectados y más resoluciones atendidas cada año.</p>
      </div>
    </a>

    <!-- OBRAS -->
    <a href="./html/obras.php" class="info-link">
      <div class="info-card">
        <div class="info-image">
          <div class="info-overlay">
            <i class="fa-solid fa-industry info-icon"></i>
          </div>
          <img src="./imagenes/servicios/obras.png" alt="Obras">
        </div>
        <h3>Obras</h3>
        <p>Con una trayectoria sólida, desarrollamos obras de gran magnitud que fortalecen
          la infraestructura nacional. Más de 11.000 km renovados y en constante expansión.</p>
      </div>
    </a>

    <!-- Flota -->
    <a href="./html/flota.php" class="info-link">
      <div class="info-card">
        <div class="info-image">
          <div class="info-overlay">
            <i class="fa-solid fa-truck info-icon"></i>
          </div>
          <img src="./imagenes/servicios/flota.png" alt="Flota Exgadet">
        </div>
        <h3>Flota</h3>
        <p>Contamos con una flota compuesta por más de 100 vehículos propios que garantizan
          una operación eficiente, segura y adaptable a las necesidades de cada proyecto.</p>
      </div>
    </a>

  </section>

  <!-- Sección ¿Quiénes nos eligen? con carrusel -->
  <section class="clientes">
    <h2>¿Quiénes nos eligen?</h2>
    <div class="carrusel-clientes" id="carrusel-clientes">
      <div class="carrusel-track">
        <img src="./imagenes/nosotros/cliente/Alemarsa.jpg" alt="Cliente 1" data-dark="./imagenes/nosotros/cliente/Alemarsa.png">
        <img src="./imagenes/nosotros/cliente/Camuzzi.png" alt="Cliente 2" data-dark="./imagenes/nosotros/cliente/Camuzzi.png">
        <img src="./imagenes/nosotros/cliente/Bagsa.jpg" alt="Cliente 3" data-dark="./imagenes/nosotros/cliente/Bagsa.png">
        <img src="./imagenes/nosotros/cliente/Metrogas.png" alt="Cliente 4" data-dark="./imagenes/nosotros/cliente/Metrogas-dark.png">
        <img src="./imagenes/nosotros/cliente/municipio de la matanza.jpg" alt="Cliente 5" data-dark="./imagenes/nosotros/cliente/municipio de la matanza.jpg">
        <img src="./imagenes/nosotros/cliente/municipio moreno.png" alt="Cliente 6" data-dark="./imagenes/nosotros/cliente/municipio moreno.png">
        <img src="./imagenes/nosotros/cliente/municipio tigre.png" alt="Cliente 7" data-dark="./imagenes/nosotros/cliente/municipio tigre.png">
        <img src="./imagenes/nosotros/cliente/Naturgy.png" alt="Cliente 8" data-dark="./imagenes/nosotros/cliente/Naturgy.png">
        <img src="./imagenes/nosotros/cliente/TGN.png" alt="Cliente 9" data-dark="./imagenes/nosotros/cliente/TGN.png">

        <!-- 👇 duplicamos para efecto infinito -->
        <img src="./imagenes/nosotros/cliente/Alemarsa.jpg" alt="Cliente 1" data-dark="./imagenes/nosotros/cliente/Alemarsa.png">
        <img src="./imagenes/nosotros/cliente/Camuzzi.png" alt="Cliente 2" data-dark="./imagenes/nosotros/cliente/Camuzzi.png">
        <img src="./imagenes/nosotros/cliente/Bagsa.jpg" alt="Cliente 3" data-dark="./imagenes/nosotros/cliente/Bagsa.png">
        <img src="./imagenes/nosotros/cliente/Metrogas.png" alt="Cliente 4" data-dark="./imagenes/nosotros/cliente/Metrogas-dark.png">
        <img src="./imagenes/nosotros/cliente/municipio de la matanza.jpg" alt="Cliente 5" data-dark="./imagenes/nosotros/cliente/municipio de la matanza.jpg">
        <img src="./imagenes/nosotros/cliente/municipio moreno.png" alt="Cliente 6" data-dark="./imagenes/nosotros/cliente/municipio moreno.png">
        <img src="./imagenes/nosotros/cliente/municipio tigre.png" alt="Cliente 7" data-dark="./imagenes/nosotros/cliente/municipio tigre.png">
        <img src="./imagenes/nosotros/cliente/Naturgy.png" alt="Cliente 8" data-dark="./imagenes/nosotros/cliente/Naturgy.png">
        <img src="./imagenes/nosotros/cliente/TGN.png" alt="Cliente 9" data-dark="./imagenes/nosotros/cliente/TGN.png">
      </div>
    </div>
  </section>

  <!-- ======== SECCIÓN TRABAJA CON NOSOTROS ======== -->

  <section id="trabaja" class="trabaja">
    <div class="trabaja-container">
      <div class="trabaja-img">
        <img src="./imagenes/contacto/trabaja.png" alt="Trabajá con nosotros">
      </div>
      <div class="trabaja-info">
        <h2>¡Sumate a nuestro equipo!</h2>
        <p>
          En <strong>Exgadet S.A.</strong> creemos en el talento, la innovación y la pasión
          por lo que hacemos. Si compartís nuestros valores y querés crecer profesionalmente,
          esta es tu oportunidad de formar parte de un equipo que marca la diferencia.
        </p>
        <a href="./html/trabaja.php" class="trabaja-btn">Mandar CV</a>
        <a href="./html/ofertas-activas.php" class="trabaja-btn">Ofertas activas</a>
        <a href="./html/proveedores.php" class="trabaja-btn">Ser proveedor</a>
      </div>
    </div>
  </section>


  <!-- ======== SECCIÓN PROVEEDORES ========
  <section class="socios">
    <div class="socio-card">
      <div class="socio-card-content">
        <i class="fa-solid fa-truck"></i>
        <h3>Unite como proveedor</h3>
        <p>
          Buscamos socios estratégicos comprometidos con la excelencia y la innovación.
          Si compartís nuestros valores y querés formar parte de una cadena de suministro sólida,
          <strong>Exgadet S.A.</strong> es tu mejor aliado.
        </p>
        <div class="btn-wrapper">
          <a href="./html/proveedores.php" class="socio-btn">Ser proveedor</a>
        </div>
      </div>
    </div>
  </section> -->

  <footer class="footer">
    <div class="footer-links">
      <a href="./html/politicas_de_privacidad.php">Políticas de Privacidad</a>
      <a href="./html/politicas_de_cookies.php">Políticas de Cookies</a>
      <a href="./html/politicas_proteccion_datos_personales.php">Protección de Datos Personales</a>
      <a href="./html/higiene_seguridad.php">Sistema de Gestión Integrado</a>
    </div>
    <div class="footer-copy">
      <p>2025 © Exgadet S.A. | Todos los derechos reservados.</p>
    </div>
  </footer>

  <script src="./js/menu_hambur-modo.js"></script>
  <script src="./js/carruseles.js"></script>
  <script src="./js/nos-eligen.js"></script>
  <!-- <script src="./js/whatsapp-buttons-nav.js"></script> -->
</body>

</html>