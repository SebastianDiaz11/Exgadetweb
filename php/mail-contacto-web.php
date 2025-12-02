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