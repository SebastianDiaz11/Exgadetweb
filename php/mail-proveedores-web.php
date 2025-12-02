<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nombre    = trim($_POST["nombre"] ?? "");
  $empresa   = trim($_POST["empresa"] ?? "");
  $email     = trim($_POST["email"] ?? "");
  $telefono  = trim($_POST["telefono"] ?? "");
  $locacion  = trim($_POST["locacion"] ?? "");
  $servicio  = trim($_POST["servicio"] ?? "");
  $mensaje   = trim($_POST["mensaje"] ?? "");

  if ($nombre && $email && $mensaje) {
    try {
      // Configuración de PHPMailer
      $mail = new PHPMailer(true);
      $mail->isSMTP();
      $mail->Host       = 'hd87.wcaup.com';
      $mail->SMTPAuth   = true;
      $mail->Username   = 'soportesistemas@exgadetsa.com.ar';
      $mail->Password   = 'JSkN9TFl';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = 587;

      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';

      // Remitente y destinatario
      $mail->setFrom('soportesistemas@exgadetsa.com.ar', 'Formulario Proveedores');
      $mail->addAddress('compras@exgadetsa.com.ar', 'Departamento de Compras');
      $mail->addReplyTo($email, $nombre);

      // 📎 Adjuntar archivo si existe
      if (!empty($_FILES['archivo']['name'])) {
        $tmpFilePath = $_FILES['archivo']['tmp_name'];
        $fileName = basename($_FILES['archivo']['name']);
        if (is_uploaded_file($tmpFilePath)) {
          $mail->addAttachment($tmpFilePath, $fileName);
        }
      }

      // Contenido del correo
      $mail->isHTML(true);
      $mail->Subject = "Nuevo contacto de proveedor: $nombre";
      $mail->Body = "
        <h2>Nuevo formulario de proveedor recibido</h2>
        <p><strong>Nombre:</strong> $nombre</p>
        <p><strong>Empresa:</strong> $empresa</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Teléfono:</strong> $telefono</p>
        <p><strong>Locación:</strong> $locacion</p>
        <p><strong>Servicio:</strong> $servicio</p>
        <p><strong>Motivo de contacto:</strong><br>$mensaje</p>
        " . (!empty($_FILES['archivo']['name']) ? "<p>📎 Se adjuntó un archivo: <strong>{$_FILES['archivo']['name']}</strong></p>" : "") . "
      ";
      $mail->AltBody = "Nombre: $nombre\nEmpresa: $empresa\nEmail: $email\nTeléfono: $telefono\nLocación: $locacion\nServicio: $servicio\nMensaje:\n$mensaje";

      $mail->send();
      $msg = "✅ Gracias $nombre, tus datos se enviaron correctamente.";
    } catch (Exception $e) {
      $msg = "⚠️ Error al enviar el mensaje. Por favor, intentá nuevamente.<br><small>" . $mail->ErrorInfo . "</small>";
    }
  } else {
    $msg = "⚠️ Por favor completá los campos obligatorios (*).";
  }
}
?>