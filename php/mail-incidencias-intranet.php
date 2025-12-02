<?php
// 📧 Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php?error=1");
  exit;
}

$legajo = $_SESSION["usuario"];
$msg = "";

// 🔹 Traer datos del usuario
$sql = "SELECT DNI, NOMBRE, APELLIDO, LEGAJO, SECTOR, CARGO, FECHA_INGRESO, CUMPLEANIOS, EMAIL
        FROM USUARIOS_DATOS
        WHERE LEGAJO = :legajo";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":legajo", $legajo);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
  $nombreCompleto = trim($usuario["NOMBRE"] . " " . $usuario["APELLIDO"]);
  $emailUsuario   = $usuario["EMAIL"] ?? "";
  $cargoUsuario   = !empty($usuario["SECTOR"]) ? $usuario["SECTOR"] : ($usuario["CARGO"] ?? "Sin cargo/área");
} else {
  $nombreCompleto = "Usuario desconocido";
  $cargoUsuario = "Sin cargo/área";
  $emailUsuario = "";
}

// 🔹 Guardar incidencia
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["mensaje"])) {
  $mensaje   = trim($_POST["mensaje"] ?? "");
  $prioridad = $_POST["prioridad"] ?? "Media";
  $tarea     = $_POST["tarea"] ?? "Sin especificar";
  $imagenRuta = null;

  if ($mensaje) {
    // Insertar nueva incidencia con prioridad y tarea
    $sql = "INSERT INTO INCIDENCIAS (LEGAJO, NOMBRE, SECTOR, MENSAJE, PRIORIDAD, TAREA)
            OUTPUT INSERTED.ID
            VALUES (:legajo, :nombre, :sector, :mensaje, :prioridad, :tarea)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":legajo", $legajo);
    $stmt->bindParam(":nombre", $nombreCompleto);
    $stmt->bindParam(":sector", $cargoUsuario);
    $stmt->bindParam(":mensaje", $mensaje);
    $stmt->bindParam(":prioridad", $prioridad);
    $stmt->bindParam(":tarea", $tarea);
    $stmt->execute();
    $incidenciaId = $stmt->fetchColumn();

    // 📷 Imagen (opcional)
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
      $directorio = dirname(__DIR__) . "/imagenesintranet/incidencias/";
      if (!file_exists($directorio)) mkdir($directorio, 0777, true);

      $nombreArchivo = "incidencia_" . $incidenciaId . "." . $ext;
      $rutaDestino = $directorio . $nombreArchivo;

      if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $imagenRuta = "../imagenesintranet/incidencias/" . $nombreArchivo;

        $sqlImg = "UPDATE INCIDENCIAS SET IMAGEN = :imagen WHERE ID = :id";
        $stmtImg = $conn->prepare($sqlImg);
        $stmtImg->bindParam(":imagen", $imagenRuta);
        $stmtImg->bindParam(":id", $incidenciaId);
        $stmtImg->execute();
      }
    }

    // ✉️ Enviar correo con PHPMailer
    try {
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

      $mail->setFrom('soportesistemas@exgadetsa.com.ar', 'Intranet Exgadet');
      $mail->addAddress('sistemas@exgadetsa.com.ar', 'Sistemas Exgadet');
      if (!empty($emailUsuario)) {
        $mail->addReplyTo($emailUsuario, $nombreCompleto);
      }

      if ($imagenRuta && file_exists($rutaDestino)) {
        $mail->addAttachment($rutaDestino);
      }

      $mail->isHTML(true);
      $mail->Subject = "🧩 Nueva incidencia reportada por $nombreCompleto";
      $mail->Body = "
        <meta charset='UTF-8'>
        <h2>Se ha generado una nueva incidencia</h2>
        <p><strong>Usuario:</strong> {$nombreCompleto}</p>
        <p><strong>Legajo:</strong> {$legajo}</p>
        <p><strong>Área/Cargo:</strong> {$cargoUsuario}</p>
        <p><strong>Email:</strong> {$emailUsuario}</p>
        <p><strong>Tarea a realizar:</strong> {$tarea}</p>
        <p><strong>Prioridad:</strong> {$prioridad}</p>
        <p><strong>Mensaje:</strong><br>" . nl2br(htmlspecialchars($mensaje)) . "</p>
        " . ($imagenRuta ? "<p><strong>Imagen adjunta:</strong> <a href='https://www.exgadetsa.com.ar/$imagenRuta'>Ver imagen</a></p>" : "") . "
        <hr><small>Este mensaje fue generado automáticamente por la Intranet Exgadet.</small>
      ";

      $mail->send();
      $msg = "✅ Incidencia registrada correctamente con prioridad <strong>$prioridad</strong> y tarea <strong>$tarea</strong>. Se notificó al área de Sistemas.";
    } catch (Exception $e) {
      $msg = "⚠️ La incidencia fue guardada, pero no se pudo enviar el correo: {$mail->ErrorInfo}";
    }
  } else {
    $msg = "⚠️ Debés completar el mensaje.";
  }
}

// 🔹 Mostrar incidencias del usuario
$sql = "SELECT ID, MENSAJE, SECTOR, IMAGEN, FECHA_CREACION, ESTADO, PRIORIDAD, TAREA,
               TERMINADO_POR, FECHA_TERMINADO, CONFORME
        FROM INCIDENCIAS
        WHERE LEGAJO = :legajo
        ORDER BY FECHA_CREACION DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":legajo", $legajo);
$stmt->execute();
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>