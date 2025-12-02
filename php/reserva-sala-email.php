<?php
// ======================================================
// 🔹 PHPMailer (sin Composer)
// ======================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// 🔹 Email RRHH
$mailRRHH = "rrhh@exgadetsa.com.ar";


// ======================================================
// 🔹 FUNCIÓN PARA ENVIAR CORREO
// ======================================================
function enviarMailReunion($mailDestino, $mailRRHH, $nombreCompleto, $dia, $hora, $hasta, $equipo, $motivo, $infusiones, $personas, $tecnologia)
{
    if (!$mailDestino) return;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'hd87.wcaup.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'soportesistemas@exgadetsa.com.ar';
        $mail->Password   = 'JSkN9TFl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet  = "UTF-8";
        $mail->Encoding = "base64";

        $mail->setFrom('soportesistemas@exgadetsa.com.ar', 'Exgadet - Salas');
        $mail->addAddress($mailDestino, $nombreCompleto);
        $mail->addAddress($mailRRHH, "Recursos Humanos");

        $mail->isHTML(true);
        $mail->Subject = "Nueva reserva de sala - $dia";
        $mail->Body = "
            <h2>Se registró una nueva reunión</h2>

            <p><strong>Reservada por:</strong> $nombreCompleto</p>
            <p><strong>Sala:</strong> $equipo</p>
            <p><strong>Día:</strong> $dia</p>
            <p><strong>Horario:</strong> $hora a $hasta</p>
            <p><strong>Motivo:</strong> $motivo</p>
            <p><strong>Infusiones:</strong> $infusiones</p>
            <p><strong>Personas:</strong> $personas</p>
            <p><strong>Elementos tecnologicos:</strong> $tecnologia</p>

            <br><p>Este es un aviso automático del sistema Exgadet Intranet.</p>
        ";

        $mail->send();

    } catch (Exception $e) {
        error_log("MAIL ERROR: " . $mail->ErrorInfo);
    }
}


// ======================================================
// 🔹 DATOS DEL USUARIO
// ======================================================
if (!$conn) die("⚠️ No se pudo establecer la conexión con la base de datos.");

$legajo = $_SESSION["usuario"] ?? "0000";

$sqlUser = "SELECT LEGAJO, NOMBRE, APELLIDO, SECTOR, CARGO AS AREA, EMAIL
            FROM USUARIOS_DATOS
            WHERE LEGAJO = :legajo";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bindParam(":legajo", $legajo);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$nombreCompleto = trim($user["NOMBRE"] . " " . $user["APELLIDO"]);
$sector         = $user["SECTOR"];
$area           = $user["AREA"];
$correoUsuario  = $user["EMAIL"];


// ======================================================
// 🔹 CREAR REUNIÓN (POST NORMAL)
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_reunion"])) {

    $dia        = $_POST["dia"] ?? null;
    $hora       = $_POST["hora"] ?? null;
    $hasta      = $_POST["hasta"] ?? null;
    $equipo     = $_POST["equipo"] ?? "";
    $motivo     = trim($_POST["motivo"] ?? "");
    $infusiones = $_POST["infusiones"] ?? "";
    $personas   = intval($_POST["personas"] ?? 0);
    $tecnologia = $_POST["tecnologia"] ?? "";

    if ($dia && $hora && $hasta && $equipo && $motivo && $infusiones && $personas > 0 && $tecnologia) {

        $check = $conn->prepare("
            SELECT COUNT(*) FROM reservas_sala
            WHERE DIA = :dia AND HORA = :hora AND EQUIPO = :equipo
        ");
        $check->execute([":dia" => $dia, ":hora" => $hora, ":equipo" => $equipo]);

        if ($check->fetchColumn() == 0) {

            $insert = $conn->prepare("
                INSERT INTO reservas_sala 
                (LEGAJO, NOMBRE, SECTOR, AREA, DIA, HORA, HASTA, EQUIPO, MOTIVO, INFUSIONES, PERSONAS, TECNOLOGIA, created_at)
                VALUES (:legajo, :nombre, :sector, :area, :dia, :hora, :hasta, :equipo, :motivo, :infusiones, :personas, :tecnologia, GETDATE())
            ");
            $insert->execute([
                ":legajo" => $legajo,
                ":nombre" => $nombreCompleto,
                ":sector" => $sector,
                ":area" => $area,
                ":dia" => $dia,
                ":hora" => $hora,
                ":hasta" => $hasta,
                ":equipo" => $equipo,
                ":motivo" => $motivo,
                ":infusiones" => $infusiones,
                ":personas" => $personas,
                ":tecnologia" => $tecnologia
            ]);

            // 📩 MAIL
            enviarMailReunion($correoUsuario, $mailRRHH, $nombreCompleto, $dia, $hora, $hasta, $equipo, $motivo, $infusiones, $personas, $tecnologia);

            $success = "✅ Reunión creada correctamente.";
        } else {
            $error = "⚠️ Esa sala ya está ocupada en ese horario.";
        }
    }
}


// ======================================================
// 🔹 AJAX: validar duplicado
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "check_reunion") {

    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM reservas_sala
        WHERE DIA = :dia AND HORA = :hora AND EQUIPO = :equipo
    ");
    $stmt->execute([
        ":dia" => $_POST["dia"],
        ":hora" => $_POST["hora"],
        ":equipo" => $_POST["equipo"]
    ]);

    echo json_encode(["exists" => $stmt->fetchColumn() > 0]);
    exit;
}


// ======================================================
// 🔹 AJAX: consultar reuniones por día  👈 (FALTABA)
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "get_dia") {

    $fecha = $_POST["fecha"] ?? "";

    $stmt = $conn->prepare("
        SELECT * 
        FROM reservas_sala
        WHERE DIA = :dia
        ORDER BY HORA ASC
    ");
    $stmt->execute([":dia" => $fecha]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Opcional: agregar color según sala
    foreach ($rows as &$r) {
        $r["color"] = ($r["EQUIPO"] === "SUM - Comedor") ? "#2ecc71" : "#9b59b6";
    }

    echo json_encode($rows);
    exit;
}


// ======================================================
// 🔹 AJAX: editar / eliminar
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"])) {

    if ($_POST["accion"] === "editar") {
        $stmt = $conn->prepare("
            UPDATE reservas_sala SET {$_POST['campo']} = :valor
            WHERE id = :id AND LEGAJO = :legajo
        ");
        $stmt->execute([
            ":valor" => $_POST["valor"],
            ":id" => $_POST["id"],
            ":legajo" => $legajo
        ]);
        echo json_encode(["ok" => true]);
        exit;
    }

    if ($_POST["accion"] === "eliminar") {
        $stmt = $conn->prepare("DELETE FROM reservas_sala WHERE id = :id AND LEGAJO = :legajo");
        $stmt->execute([":id" => $_POST["id"], ":legajo" => $legajo]);
        echo json_encode(["ok" => true]);
        exit;
    }
}


// ======================================================
// 🔹 AJAX: Crear reunión
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "crear") {

    $dia        = $_POST["dia"];
    $hora       = $_POST["hora"];
    $hasta      = $_POST["hasta"];
    $equipo     = $_POST["equipo"];
    $motivo     = $_POST["motivo"];
    $infusiones = $_POST["infusiones"];
    $personas   = intval($_POST["personas"]);
    $tecnologia = $_POST["tecnologia"];

    $insert = $conn->prepare("
        INSERT INTO reservas_sala
        (LEGAJO, NOMBRE, SECTOR, AREA, DIA, HORA, HASTA, EQUIPO, MOTIVO, INFUSIONES, PERSONAS, TECNOLOGIA, created_at)
        VALUES (:legajo, :nombre, :sector, :area, :dia, :hora, :hasta, :equipo, :motivo, :infusiones, :personas, :tecnologia, GETDATE())
    ");

    $insert->execute([
        ":legajo" => $legajo,
        ":nombre" => $nombreCompleto,
        ":sector" => $sector,
        ":area" => $area,
        ":dia" => $dia,
        ":hora" => $hora,
        ":hasta" => $hasta,
        ":equipo" => $equipo,
        ":motivo" => $motivo,
        ":infusiones" => $infusiones,
        ":personas" => $personas,
        ":tecnologia" => $tecnologia
    ]);

    // 📩 MAIL
    enviarMailReunion($correoUsuario, $mailRRHH, $nombreCompleto, $dia, $hora, $hasta, $equipo, $motivo, $infusiones, $personas, $tecnologia);

    echo json_encode(["ok" => true]);
    exit;
}


// ======================================================
// 🔹 CONSULTAS
// ======================================================
$fechaHoy = date("Y-m-d");

$stmt = $conn->prepare("SELECT * FROM reservas_sala WHERE DIA = :dia ORDER BY HORA ASC");
$stmt->execute([":dia" => $fechaHoy]);
$reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtMis = $conn->prepare("SELECT * FROM reservas_sala WHERE LEGAJO = :legajo ORDER BY DIA DESC, HORA ASC");
$stmtMis->execute([":legajo" => $legajo]);
$misReuniones = $stmtMis->fetchAll(PDO::FETCH_ASSOC);


// ======================================================
// 🔹 CALENDARIO
// ======================================================
$colores = [
    "SUM - Comedor" => "#2ecc71",
    "Sala de reuniones 2" => "#9b59b6"
];

$mes = isset($_GET["mes"]) ? intval($_GET["mes"]) : date("n");
$anio = isset($_GET["anio"]) ? intval($_GET["anio"]) : date("Y");

$meses = [
    1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",
    7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre"
];

$primerDia   = mktime(0,0,0,$mes,1,$anio);
$diasMes     = date("t",$primerDia);
$inicioSemana= date("N",$primerDia);
$nombreMes   = $meses[$mes] . " " . $anio;

$mesAnt = $mes - 1;
$anioAnt = $anio;
$mesSig = $mes + 1;
$anioSig = $anio;

if ($mesAnt < 1) { $mesAnt = 12; $anioAnt--; }
if ($mesSig > 12) { $mesSig = 1; $anioSig++; }
?>