<?php
session_start();
require_once "../php/mail-contacto-web.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto</title>
    <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/contacto.css">
</head>

<body>

    <?php include '../html/nav.php'; ?>

    <section id="contacto" class="contacto">
        <div class="container">
            <div class="box-info">
                <h1>CONTÁCTATE CON NOSOTROS</h1>
                <!-- LINKS REDES -->
                <div class="links">
                    <a href="https://www.facebook.com/Exgadet/"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/exgadetsa/"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/exgadet-sa/posts/?feedView=all"><i class="fa-brands fa-linkedin"></i></a>
                </div>
                <!-- MAPA DE UBICACIÓN -->
                <div class="mapa">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3246.5271886959184!2d-58.5830036!3d-34.6751391!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bcc7b35504dd45%3A0xafae42fdd34fab16!2sExgadet%20S.A.!5e0!3m2!1ses!2sar!4v1709550000000!5m2!1ses!2sar"
                        width="400" height="250"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

            </div>

            <form method="POST" action="#contacto">
                <div class="input-box">
                    <input type="text" name="nombre" placeholder="Nombre y apellido" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="number" name="telefono" required placeholder="Teléfono">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="mail" required placeholder="Correo electrónico">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="text" name="asunto" placeholder="Asunto">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div class="input-box">
                    <textarea name="mensaje" placeholder="Escribe tu mensaje..." required></textarea>
                </div>
                <button type="submit">Enviar mensaje</button>
                <?php if (isset($mensaje_enviado)): ?>
                    <div class="mensaje"><?php echo $mensaje_enviado; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <?php include './footer.php'; ?>
    <script src="../js/menu_hambur-modo.js"></script>
    <!-- <script src="../js/whatsapp-buttons.js"></script> -->
</body>

</html>