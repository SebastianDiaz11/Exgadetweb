<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// =========================
// CONEXIÓN BD SQL SERVER
// =========================
require "../php/conexion.php";

// =========================
// VALIDAR SESIÓN
// =========================
$legajoSesion = $_SESSION["usuario"] ?? null;
if (!$legajoSesion) {
    die("No hay sesión iniciada.");
}

// =========================
// OBTENER DATOS DEL TRABAJADOR
// =========================
$sqlUser = "SELECT LEGAJO, NOMBRE, APELLIDO
            FROM USUARIOS_DATOS
            WHERE LEGAJO = :legajo";

$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bindParam(":legajo", $legajoSesion);
$stmtUser->execute();
$trabajador = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    die("Error: No se encontró el usuario.");
}

$legajo = $trabajador["LEGAJO"];
$nombreTrabajador = trim($trabajador["NOMBRE"] . " " . $trabajador["APELLIDO"]);

// =========================
// IMAP (SOLO LECTURA)
// =========================
$hostname = '{hd87.wcaup.com:143/imap/notls/readonly}INBOX';
$username = 'sdiaz@exgadetsa.com.ar';
$password = 'z2rdeEQRCN';

$inbox = @imap_open($hostname, $username, $password)
    or die("❌ Error IMAP: " . imap_last_error());

// ======================================================
// DECODIFICADOR UTF-8 seguro
// ======================================================
function fixEncoding($text)
{
    if (!is_string($text) || trim($text) === "") return "";

    $encoding = mb_detect_encoding($text, "UTF-8, ISO-8859-1, WINDOWS-1252", true);

    if ($encoding && $encoding !== "UTF-8") {
        $text = mb_convert_encoding($text, "UTF-8", $encoding);
    }

    return $text;
}

// ======================================================
// DECODIFICAR CABECERAS MIME (=?iso-8859-1?Q?...?=)
// ======================================================
function decodeMimeHeader($text)
{
    if (!is_string($text) || $text === "") return "";

    $elements = imap_mime_header_decode($text);
    $decoded  = "";

    foreach ($elements as $el) {
        $part    = $el->text;
        $charset = $el->charset;

        if ($charset && strtoupper($charset) !== "UTF-8" && strtoupper($charset) !== "DEFAULT") {
            $part = @mb_convert_encoding($part, "UTF-8", $charset);
        }
        $decoded .= $part;
    }

    return fixEncoding($decoded);
}

// ======================================================
// RESPONDER CORREO
// ======================================================
if (isset($_POST["respuesta_enviar"])) {

    require "../PHPMailer/src/PHPMailer.php";
    require "../PHPMailer/src/SMTP.php";
    require "../PHPMailer/src/Exception.php";

    $emailCliente = $_POST["email_cliente"];
    // Asunto ya viene decodificado desde el input hidden
    $asunto       = $_POST["asunto"];
    $respuesta    = $_POST["respuesta"];
    $idMail       = $_POST["id_mail"];

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = "hd87.wcaup.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sdiaz@exgadetsa.com.ar';
        $mail->Password   = 'z2rdeEQRCN';
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;

        $mail->CharSet  = "UTF-8";
        $mail->Encoding = "quoted-printable";

        // REMITENTE REAL (el que envía el servidor)
        $mail->setFrom("sistemas@exgadetsa.com.ar", "Exgadet Reclamos");

        // A DONDE DEBE RESPONDER EL CLIENTE
        $mail->addReplyTo("sistemas@exgadetsa.com.ar", "Exgadet Reclamos");

        // OBLIGA QUE EL RETURN-PATH SEA EL MISMO QUE EL FROM
        $mail->Sender = "sdiaz@exgadetsa.com.ar";

        // DESTINATARIO (el cliente)
        $mail->addAddress($emailCliente);

        $mail->Subject = "Re: " . $asunto;
        $mail->Body    = $respuesta;

        // Adjuntar archivo si se subió
        if (!empty($_FILES["adjunto"]["tmp_name"])) {
            $mail->addAttachment($_FILES["adjunto"]["tmp_name"], $_FILES["adjunto"]["name"]);
        }

        $mail->send();

        // ======================================
        // SI SE RESPONDE → QUITAR DE TABLA VISTOS
        // ======================================
        $uid = intval($idMail);

        $sqlDeleteVisto = "DELETE FROM reclamos_vistos WHERE mail_uid = :id";
        $stmtDeleteVisto = $conn->prepare($sqlDeleteVisto);
        $stmtDeleteVisto->execute([":id" => $uid]);

        $sql = "INSERT INTO respuestas_reclamos
                (trabajador_legajo, trabajador_nombre, email_cliente, asunto, mensaje_enviado)
                VALUES (:legajo, :nombre, :email, :asunto, :msg)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":legajo" => $legajo,
            ":nombre" => $nombreTrabajador,
            ":email"  => $emailCliente,
            ":asunto" => $asunto,
            ":msg"    => $respuesta
        ]);

        echo "<script>alert('📨 Respuesta enviada');window.location='reclamos.php?ver=$idMail';</script>";
    } catch (Exception $e) {
        echo "❌ Error enviando: " . $mail->ErrorInfo . "<br>";
        echo "📌 Exception: " . $e->getMessage() . "<br>";
    }
    exit();
}

// ======================================================
// FILTRO + PAGINACIÓN
// ======================================================
$filtro = $_GET["filtro"] ?? "naturgy";
$pagina = max(1, intval($_GET["pagina"] ?? 1));

// Siempre traer todos los correos
$emailsAll = imap_search($inbox, "ALL");
if (!is_array($emailsAll)) $emailsAll = [];

$emails = [];

foreach ($emailsAll as $id) {

    $ov = imap_fetch_overview($inbox, $id, 0)[0] ?? null;
    if (!$ov) continue;

    // FROM siempre como string
    $from = $ov->from ?? "";
    $from = is_string($from) ? trim($from) : "";
    $mailLower = strtolower($from);

    // NATURGY
    if ($filtro === "naturgy") {
        if ($from !== "" && str_contains($mailLower, "naturgy.com.ar")) {
            $emails[] = $id;
        }
        continue;
    }

    // RESTO
    if ($filtro === "resto") {
        if ($from === "" || !str_contains($mailLower, "naturgy.com.ar")) {
            $emails[] = $id;
        }
        continue;
    }

    // TODOS
    if ($filtro === "todos") {
        $emails[] = $id;
        continue;
    }
}

rsort($emails);

$porPagina     = 20;
$totalCorreos  = count($emails);
$totalPaginas  = max(1, ceil($totalCorreos / $porPagina));
$pagina        = min($pagina, $totalPaginas);
$inicio        = ($pagina - 1) * $porPagina;
$emailsPagina  = array_slice($emails, $inicio, $porPagina);

// ======================================================
// FUNCIONES RECURSIVAS PARA MULTIPART / ADJUNTOS
// ======================================================
function decodeBodyWithCharset($inbox, $id, $partNum, $part)
{
    $body = imap_fetchbody($inbox, $id, $partNum);

    switch ($part->encoding) {
        case 3:
            $body = base64_decode($body);
            break;
        case 4:
            $body = quoted_printable_decode($body);
            break;
    }

    $charset = null;
    if (isset($part->parameters)) {
        foreach ($part->parameters as $p) {
            if (strtolower($p->attribute) === 'charset') {
                $charset = $p->value;
            }
        }
    }

    if ($charset && strtoupper($charset) !== 'UTF-8') {
        $body = @mb_convert_encoding($body, "UTF-8", $charset);
    } else {
        $body = fixEncoding($body);
    }

    return $body;
}

function analyzeStructureRecursive($inbox, $id, $structure, $prefix = "")
{
    $result = [
        "html"   => null,
        "plain"  => null,
        "inline" => [],
        "files"  => []
    ];

    // multipart/*
    if ($structure->type == 1 && isset($structure->parts)) {
        foreach ($structure->parts as $i => $part) {

            $partNum = $prefix === "" ? ($i + 1) : "$prefix." . ($i + 1);
            $sub = analyzeStructureRecursive($inbox, $id, $part, $partNum);

            if ($sub["html"]  && !$result["html"])  $result["html"]  = $sub["html"];
            if ($sub["plain"] && !$result["plain"]) $result["plain"] = $sub["plain"];

            $result["inline"] = array_merge($result["inline"], $sub["inline"]);
            $result["files"]  = array_merge($result["files"],  $sub["files"]);
        }

        return $result;
    }

    // Parte simple
    $partNum = $prefix === "" ? "1" : $prefix;

    $type    = $structure->type;
    $subtype = strtoupper($structure->subtype ?? "");

    // Detectar nombre de archivo
    $filename = "";
    if (isset($structure->dparameters)) {
        foreach ($structure->dparameters as $p) {
            if (strtolower($p->attribute) == "filename") $filename = $p->value;
        }
    }
    if (isset($structure->parameters)) {
        foreach ($structure->parameters as $p) {
            if (strtolower($p->attribute) == "name" && !$filename) $filename = $p->value;
        }
    }

    $disposition = strtolower($structure->disposition ?? "");
    $cid = isset($structure->id) ? trim($structure->id, "<>") : "";

    // TEXTO (cuerpo)
    if ($type == 0 && $filename == "" && ($disposition == "" || $disposition == "inline")) {
        $body = decodeBodyWithCharset($inbox, $id, $partNum, $structure);

        if ($subtype == "HTML") $result["html"] = $body;
        else                    $result["plain"] = $body;

        return $result;
    }

    // ADJUNTO
    $body = imap_fetchbody($inbox, $id, $partNum);
    switch ($structure->encoding) {
        case 3:
            $body = base64_decode($body);
            break;
        case 4:
            $body = quoted_printable_decode($body);
            break;
    }

    if ($filename === "") $filename = $cid ?: ("parte_" . str_replace('.', '_', $partNum));
    $cleanName = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
    $fileFinal = $id . "_" . $cleanName;

    $dir = __DIR__ . "/adjuntos/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    file_put_contents($dir . $fileFinal, $body);

    if ($cid) {
        $ext  = strtolower(pathinfo($cleanName, PATHINFO_EXTENSION));
        $mime = "application/octet-stream";
        if (in_array($ext, ["jpg", "jpeg"])) $mime = "image/jpeg";
        elseif ($ext === "png")             $mime = "image/png";
        elseif ($ext === "gif")             $mime = "image/gif";

        $result["inline"][$cid] = "data:$mime;base64," . base64_encode($body);
    } else {
        $result["files"][] = [
            "filename" => $cleanName,
            "file"     => "../php/download.php?file=" . urlencode($fileFinal)
        ];
    }

    return $result;
}

// ======================================================
// CUERPO FINAL DEL EMAIL
// ======================================================
function getEmailBody($inbox, $id)
{
    $structure = imap_fetchstructure($inbox, $id);
    $data      = analyzeStructureRecursive($inbox, $id, $structure, "");

    $html  = $data["html"];
    $plain = $data["plain"];

    $mensaje = $html ?: nl2br(htmlspecialchars($plain ?? ""));

    // limpiar cosas de Word / outlook
    $mensaje = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $mensaje);
    $mensaje = preg_replace('#</?(html|head|body|meta)[^>]*>#i', '', $mensaje);

    // CIDs inline
    foreach ($data["inline"] as $cid => $img64) {
        $mensaje = str_replace("cid:$cid", $img64, $mensaje);
    }

    // Adjuntos descargables
    if (count($data["files"]) > 0) {
        $mensaje .= "<hr><h5>Adjuntos:</h5><ul>";
        foreach ($data["files"] as $f) {
            $filename = htmlspecialchars($f["filename"]);
            $href     = htmlspecialchars($f["file"]);
            $mensaje .= "<li>📎 $filename <a class='btn btn-sm btn-outline-primary' href='$href' target='_blank'>Descargar</a></li>";
        }
        $mensaje .= "</ul>";
    }

    return $mensaje;
}

// ======================================================
// ¿EMAIL RESPONDIDO? (por email + asunto)
// ======================================================
function emailRespondido($conn, $emailCliente, $asunto)
{
    $sql = "SELECT COUNT(*) 
            FROM respuestas_reclamos 
            WHERE email_cliente = :email 
              AND asunto = :asunto";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":email"  => $emailCliente,
        ":asunto" => $asunto
    ]);
    return $stmt->fetchColumn() > 0;
}

// ======================================================
// MARCAR Y CONSULTAR MAILS VISTOS
// ======================================================
function marcarVisto($conn, $mailId, $legajo)
{
    // 1) Verificar si ya existe
    $sqlCheck = "SELECT COUNT(*) FROM reclamos_vistos WHERE mail_uid = :id";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([":id" => $mailId]);
    $existe = $stmtCheck->fetchColumn();

    // 2) Insertar solo si no existe
    if ($existe == 0) {
        $sqlInsert = "INSERT INTO reclamos_vistos (mail_uid, trabajador_legajo)
                      VALUES (:id, :legajo)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->execute([
            ":id"     => $mailId,
            ":legajo" => $legajo
        ]);
    }
}

function mailVisto($conn, $mailId)
{
    $sql = "SELECT COUNT(*) FROM reclamos_vistos WHERE mail_uid = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => $mailId]);
    return $stmt->fetchColumn() > 0;
}

function quienVioPrimero($conn, $mailId)
{
    $sql = "SELECT trabajador_legajo FROM reclamos_vistos WHERE mail_uid = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => $mailId]);
    return $stmt->fetchColumn() ?: null;
}

// ======================================================
// OBTENER ADJUNTOS YA GUARDADOS EN /adjuntos/
// ======================================================
function obtenerAdjuntosGuardados($uid)
{
    $dir = __DIR__ . "/adjuntos/";
    if (!is_dir($dir)) return [];

    // Busca archivos del tipo: {uid}_archivo.ext
    $archivos = glob($dir . $uid . "_*");
    if (!$archivos) return [];

    $lista = [];

    foreach ($archivos as $path) {
        $nombre = basename($path); // ej: 12345_factura.pdf
        $lista[] = [
            "path"   => $path,
            "nombre" => preg_replace('/^\d+_/', '', $nombre) // saca el UID_ del nombre
        ];
    }

    return $lista;
}

// ======================================================
// REENVIAR CORREO (CON ADJUNTOS DEL ORIGINAL)
// ======================================================
if (isset($_POST["reenviar_enviar"])) {

    require "../PHPMailer/src/PHPMailer.php";
    require "../PHPMailer/src/SMTP.php";
    require "../PHPMailer/src/Exception.php";

    $destino   = $_POST["reenviar_a"];
    $contenido = base64_decode($_POST["reenviar_contenido"]);
    $asunto    = "Fwd: " . $_POST["reenviar_asunto"];

    // UID DEL MAIL ACTUAL → así encontramos los adjuntos
    $uidReenvio = imap_uid($inbox, intval($_GET["ver"] ?? 0));

    // Traer adjuntos del mail original
    $adjuntosReenvio = [];
    if ($uidReenvio) {
        $adjuntosReenvio = obtenerAdjuntosGuardados($uidReenvio);
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isHTML(true);

    try {
        $mail->isSMTP();
        $mail->Host       = "hd87.wcaup.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sdiaz@exgadetsa.com.ar';
        $mail->Password   = 'z2rdeEQRCN';
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;

        $mail->CharSet  = "UTF-8";
        $mail->Encoding = "quoted-printable";

        // REMITENTE
        $mail->setFrom("sistemas@exgadetsa.com.ar", "Exgadet Reclamos");
        $mail->addReplyTo("sistemas@exgadetsa.com.ar");

        // DESTINATARIO DEL REENVÍO
        $mail->addAddress($destino);

        $mail->Subject = $asunto;
        $mail->Body    = $contenido;

        // ===========================
        //    AGREGAR ADJUNTOS
        // ===========================
        foreach ($adjuntosReenvio as $adj) {
            $mail->addAttachment($adj["path"], $adj["nombre"]);
        }

        $mail->send();

        echo "<script>alert('📤 Correo reenviado con adjuntos');window.location='reclamos.php?ver=" . ($_GET['ver'] ?? '') . "';</script>";
    } catch (Exception $e) {
        echo "❌ Error reenviando: " . $mail->ErrorInfo . "<br>";
        echo "📌 Exception: " . $e->getMessage() . "<br>";
    }
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reclamos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
        }

        .sidebar {
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid #ddd;
        }

        .email-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .email-item:hover {
            background: #eef3f7;
        }

        .email-active {
            background: #dfe9f5 !important;
        }

        .mail-content {
            height: 100vh;
            overflow-y: auto;
            padding: 25px;
        }

        .card-mail {
            padding: 18px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        }

        .historial-box {
            background: white;
            padding: 15px;
            border-left: 4px solid #0d6efd;
            border-radius: 5px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <!-- ======================= NAV SUPERIOR ======================= -->
    <nav class="navbar navbar-light bg-white shadow-sm px-4" style="height:60px;">
        <span class="navbar-brand mb-0 h4">
            📬 Reclamos Exgadet
        </span>
        <div class="d-flex gap-2">
            <a href="../php/export_respuestas_excel.php" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Exportar Respuestas
            </a>

            <a href="../php/export_vistos_excel.php" class="btn btn-secondary">
                <i class="bi bi-eye-fill"></i> Exportar Vistos
            </a>

            <a href="estadisticas_reclamos.php" class="btn btn-warning">
                <i class="bi bi-bar-chart-fill"></i> Panel Estadístico
            </a>
        </div>
    </nav>
    <!-- =========================================================== -->
    <div class="container-fluid">
        <div class="row">

            <!-- ==================== SIDEBAR ==================== -->
            <div class="col-3 sidebar bg-white">

                <h4 class="p-3"><i class="bi bi-envelope-fill"></i> Reclamos</h4>

                <div class="p-3">
                    <a href="reclamos.php?filtro=todos&pagina=1"
                        class="btn btn-sm <?= ($filtro == 'todos' ? 'btn-primary' : 'btn-outline-primary') ?>">Todos</a>

                    <a href="reclamos.php?filtro=naturgy&pagina=1"
                        class="btn btn-sm <?= ($filtro == 'naturgy' ? 'btn-primary' : 'btn-outline-primary') ?>">Naturgy</a>

                    <a href="reclamos.php?filtro=resto&pagina=1"
                        class="btn btn-sm <?= ($filtro == 'resto' ? 'btn-primary' : 'btn-outline-primary') ?>">Resto</a>
                </div>

                <?php if (empty($emailsPagina)): ?>
                    <p class="p-3">No hay correos para este filtro.</p>

                <?php else: ?>
                    <?php foreach ($emailsPagina as $email_number):

                        // UID real, permanente
                        $uid = imap_uid($inbox, $email_number);

                        $ov = imap_fetch_overview($inbox, $email_number, 0)[0];

                        $rawDate = $ov->date ?? null;

                        if ($rawDate && strtotime($rawDate)) {
                            $fecha = date("d/m/Y H:i", strtotime($rawDate));
                        } else {
                            $fecha = "Sin fecha";
                        }

                        // Decodificar asunto y from
                        $subjectDecoded = decodeMimeHeader($ov->subject ?? "");
                        $fromDecoded    = decodeMimeHeader($ov->from ?? "");

                        preg_match('/<(.+?)>/', $ov->from, $match);
                        $mailFrom = $match[1] ?? $ov->from;
                        $mailFrom = trim($mailFrom);

                        $respondido = emailRespondido($conn, $mailFrom, $subjectDecoded);

                        $visto = mailVisto($conn, $uid);
                    ?>
                        <div class="email-item <?= (isset($_GET["ver"]) && $_GET["ver"] == $email_number) ? "email-active" : "" ?>">
                            <a href="reclamos.php?filtro=<?= $filtro ?>&pagina=<?= $pagina ?>&ver=<?= $email_number ?>"
                                style="text-decoration:none;color:black;">
                                <strong>
                                    <?= htmlspecialchars($subjectDecoded) ?>

                                    <?php if ($respondido): ?>
                                        <span class="badge bg-success ms-2">Respondido</span>
                                    <?php endif; ?>

                                    <?php if ($visto && !$respondido): ?>
                                        <span class="badge bg-secondary ms-2">Visto</span>
                                    <?php endif; ?>
                                </strong>
                                <br>
                                <span class="text-muted small"><?= $fecha ?></span><br>
                                <span class="text-muted small"><?= htmlspecialchars($fromDecoded) ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="p-3 text-center">
                    <?php if ($pagina > 1): ?>
                        <a href="reclamos.php?filtro=<?= $filtro ?>&pagina=<?= $pagina - 1 ?>"
                            class="btn btn-sm btn-outline-secondary">« Anterior</a>
                    <?php endif; ?>

                    <span class="mx-2">Página <?= $pagina ?> de <?= $totalPaginas ?></span>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="reclamos.php?filtro=<?= $filtro ?>&pagina=<?= $pagina + 1 ?>"
                            class="btn btn-sm btn-outline-secondary">Siguiente »</a>
                    <?php endif; ?>
                </div>

            </div>

            <!-- ==================== CUERPO DEL MAIL ==================== -->
            <div class="col-9 mail-content">

                <?php if (!isset($_GET["ver"])): ?>

                    <div class="text-center mt-5">
                        <i class="bi bi-envelope-paper fs-1 text-secondary"></i>
                        <h4 class="text-secondary mt-3">Seleccioná un correo</h4>
                    </div>

                <?php else: ?>

                    <?php
                    $id = intval($_GET["ver"]);
                    $uid = imap_uid($inbox, $id);
                    marcarVisto($conn, $uid, $legajo);

                    if (!in_array($id, $emails)) {
                        die("<h3 class='p-4 text-danger'>❌ El mensaje ya no existe.</h3>");
                    }

                    $overview        = imap_fetch_overview($inbox, $id, 0)[0];
                    $mensajeOriginal = getEmailBody($inbox, $id);

                    // Decodificar subject + from para la vista
                    $subjectDecoded = decodeMimeHeader($overview->subject ?? "");
                    $fromDecoded    = decodeMimeHeader($overview->from ?? "");

                    preg_match('/<(.+?)>/', $overview->from, $match);
                    $emailCliente = $match[1] ?? $overview->from;
                    $emailCliente = trim($emailCliente);

                    // Historial SOLO de este mail + asunto
                    $sqlHist = "SELECT * 
                            FROM respuestas_reclamos 
                            WHERE email_cliente = :email 
                              AND asunto = :asunto
                            ORDER BY fecha DESC";
                    $stmtHist = $conn->prepare($sqlHist);
                    $stmtHist->execute([
                        ":email"  => $emailCliente,
                        ":asunto" => $subjectDecoded
                    ]);
                    $historial = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

                    // Adjuntos del mail actual para mostrar en el modal
                    $adjuntosModal = obtenerAdjuntosGuardados($uid);
                    ?>

                    <div class="card-mail">

                        <h4><i class="bi bi-person-circle"></i> <?= htmlspecialchars(fixEncoding($emailCliente)) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($subjectDecoded) ?></p>

                        <hr>

                        <h5>Mensaje recibido</h5>
                        <div style="background:#f8f9fa; padding:15px; border-radius:5px;">
                            <?= $mensajeOriginal ?>
                        </div>

                        <hr>

                        <h4><i class="bi bi-clock-history"></i> Historial</h4>

                        <?php if (empty($historial)): ?>
                            <p class="text-muted">No hay respuestas anteriores para este mensaje.</p>
                        <?php else: ?>
                            <?php foreach ($historial as $h): ?>
                                <div class="historial-box">
                                    <strong><?= htmlspecialchars(fixEncoding($h["trabajador_nombre"])) ?>
                                        (Legajo <?= $h["trabajador_legajo"] ?>)</strong><br>
                                    <span class="text-muted small"><?= $h["fecha"] ?></span>
                                    <div class="mt-2"><?= nl2br(htmlspecialchars(fixEncoding($h["mensaje_enviado"]))) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <hr>

                        <!-- ================= BOTÓN REENVIAR ================= -->
                        <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalReenviar">
                            <i class="bi bi-forward-fill"></i> Reenviar correo
                        </button>

                        <!-- ================= MODAL REENVIAR ================= -->
                        <div class="modal fade" id="modalReenviar" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reenviar correo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <!-- DESTINO -->
                                            <label class="form-label">Enviar a:</label>
                                            <input type="email" name="reenviar_a" class="form-control" required>

                                            <!-- CONTENIDO DEL MENSAJE -->
                                            <label class="form-label mt-3">Mensaje a reenviar:</label>
                                            <textarea class="form-control" rows="6" disabled><?= htmlspecialchars(strip_tags($mensajeOriginal)) ?></textarea>

                                            <!-- LISTA DE ADJUNTOS -->
                                            <?php if (!empty($adjuntosModal)): ?>
                                                <div class="mt-3">
                                                    <label class="form-label">Adjuntos incluidos:</label>
                                                    <ul class="list-group">
                                                        <?php foreach ($adjuntosModal as $a): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                📎 <?= htmlspecialchars($a["nombre"]) ?>
                                                                <a href="<?= "../php/download.php?file=" . urlencode(basename($a["path"])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    Ver
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted mt-3">📁 No hay adjuntos para reenviar.</p>
                                            <?php endif; ?>

                                            <!-- DATOS OCULTOS -->
                                            <input type="hidden" name="reenviar_contenido" value='<?= base64_encode($mensajeOriginal) ?>'>
                                            <input type="hidden" name="reenviar_asunto" value="<?= htmlspecialchars($subjectDecoded) ?>">

                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" name="reenviar_enviar" class="btn btn-primary">
                                                <i class="bi bi-forward"></i> Enviar reenvío
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>Responder</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <textarea name="respuesta" rows="7" class="form-control mb-3" required></textarea>
                            <input type="file" name="adjunto" class="form-control mb-3">

                            <input type="hidden" name="email_cliente" value="<?= htmlspecialchars($emailCliente) ?>">
                            <input type="hidden" name="asunto" value="<?= htmlspecialchars($subjectDecoded) ?>">
                            <input type="hidden" name="id_mail" value="<?= $uid ?>">

                            <button type="submit" name="respuesta_enviar" class="btn btn-primary">
                                <i class="bi bi-send-fill"></i> Enviar respuesta
                            </button>
                        </form>

                    </div>

                <?php endif; ?>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
// Cerrar conexión IMAP si está abierta
if ($inbox && is_resource($inbox)) {
    imap_close($inbox);
}
?>