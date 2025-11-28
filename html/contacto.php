<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// RUTAS PHPMailer (ajustadas a tu estructura)
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// CONTACTO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = htmlspecialchars($_POST["nombre"]);
    $telefono = htmlspecialchars($_POST["telefono"]);
    $mailUsuario = htmlspecialchars($_POST["mail"]);
    $asunto = htmlspecialchars($_POST["asunto"]);
    $mensaje = htmlspecialchars($_POST["mensaje"]);

    // 📌 LISTA DE DESTINATARIOS INTERNOS
    $destinatarios = [
        "info@exgadetsa.com.ar"
    ];

    // 📌 FORMATO DEL MENSAJE
    $contenido = "
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombre:</strong> $nombre</p>
    <p><strong>Teléfono:</strong> $telefono</p>
    <p><strong>Email:</strong> $mailUsuario</p>
    <p><strong>Asunto:</strong> $asunto</p>
    <p><strong>Mensaje:</strong><br>$mensaje</p>
    ";

    // ===========================
    // 📧 CONFIGURAR PHPMailer
    // ===========================
    $mail = new PHPMailer(true);

    try {
        // 💡 CONFIGURAR SMTP (CAMBIAR A TUS DATOS REALES)
        $mail->isSMTP();
        $mail->Host       = 'hd87.wcaup.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'soportesistemas@exgadetsa.com.ar';
        $mail->Password   = 'JSkN9TFl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // 📨 REMITENTE
        $mail->setFrom('soportesistemas@exgadetsa.com.ar', 'Formulario de Contacto Web');
        $mail->addReplyTo($mailUsuario, $nombre); // responder al usuario

        // 📨 DESTINATARIOS (LISTA INTERNA)
        foreach ($destinatarios as $dest) {
            $mail->addAddress($dest);
        }

        // ASUNTO Y CONTENIDO
        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de contacto: $asunto";
        $mail->Body    = $contenido;

        $mail->send();
        $mensaje_enviado = "¡Mensaje enviado correctamente!";
    } catch (Exception $e) {
        $mensaje_enviado = "Error al enviar el mensaje: " . $mail->ErrorInfo;
    }
}
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
    <style>
        /* ==========================================================
   📱 RESPONSIVE – TABLET (≤ 992px)
   ========================================================== */
@media (max-width: 992px) {

    .contacto {
        height: auto;
        padding: 60px 0;
    }

    .container {
        grid-template-columns: 1fr;
        width: 90%;
        max-width: 700px;
        gap: 40px;
    }

    .box-info {
        text-align: center;
        align-items: center;
        gap: 30px;
    }

    .box-info h1 {
        text-align: center;
        font-size: 1.8rem;
        letter-spacing: 3px;
    }

    .links {
        justify-content: center;
    }

    .mapa iframe {
        width: 100%;
        height: 260px;
    }
}

/* ==========================================================
   📱 RESPONSIVE – CELULAR GRANDE (≤ 768px)
   ========================================================== */
@media (max-width: 768px) {

    .contacto {
        padding: 40px 0;
    }

    .box-info h1 {
        font-size: 1.6rem;
        letter-spacing: 2px;
    }

    form {
        padding: 0 5px;
    }

    .input-box textarea {
        min-height: 150px;
    }

    .links a {
        width: 35px;
        height: 35px;
    }

    .links a i {
        font-size: 16px;
        line-height: 35px;
    }
}

/* ==========================================================
   📱 RESPONSIVE – CELULAR MEDIANO (≤ 576px)
   ========================================================== */
@media (max-width: 576px) {

    .container {
        width: 92%;
        gap: 25px;
    }

    .box-info h1 {
        font-size: 1.4rem;
    }

    form button {
        font-size: 0.9rem;
        padding: 10px;
    }
}

/* ==========================================================
   📱 RESPONSIVE – CELULAR CHICO (≤ 420px)
   ========================================================== */
@media (max-width: 420px) {

    .box-info h1 {
        font-size: 1.2rem;
        letter-spacing: 1px;
    }

    .links {
        gap: 12px;
    }

    .mapa iframe {
        height: 200px;
    }

    .input-box input,
    .input-box textarea {
        font-size: 0.9rem;
    }

    .input-box i {
        font-size: 14px;
    }
}
    </style>
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