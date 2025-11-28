<?php
session_start();
require_once "../php/conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

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

$msg = "";

// --- Actualizar estado de una incidencia ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"], $_POST["estado"])) {
  $id = intval($_POST["id"]);
  $estado = trim($_POST["estado"]);
  $legajo = $_SESSION["usuario"];

  // 🔹 Obtener nombre del usuario que hace el cambio
  $sqlU = "SELECT NOMBRE, APELLIDO FROM USUARIOS_DATOS WHERE LEGAJO = :legajo";
  $stmtU = $conn->prepare($sqlU);
  $stmtU->bindParam(":legajo", $legajo);
  $stmtU->execute();
  $u = $stmtU->fetch(PDO::FETCH_ASSOC);
  $usuarioAccion = $u ? $u["NOMBRE"] . " " . $u["APELLIDO"] : "Desconocido";

  // 🔹 Actualizar la incidencia
  if ($estado === "resuelto") {
    $sql = "UPDATE INCIDENCIAS 
            SET ESTADO = :estado, 
                TERMINADO_POR = :usuarioAccion,
                FECHA_TERMINADO = GETDATE()
            WHERE ID = :id";
  } else {
    $sql = "UPDATE INCIDENCIAS 
            SET ESTADO = :estado
            WHERE ID = :id";
  }

  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":estado", $estado);
  if ($estado === "resuelto") {
    $stmt->bindParam(":usuarioAccion", $usuarioAccion);
  }
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  // 🔹 Obtener datos de la incidencia y del usuario
  $sqlMail = "SELECT I.NOMBRE, I.LEGAJO, I.PRIORIDAD, U.EMAIL 
              FROM INCIDENCIAS I 
              JOIN USUARIOS_DATOS U 
                ON I.LEGAJO COLLATE SQL_Latin1_General_CP1_CI_AI = U.LEGAJO COLLATE SQL_Latin1_General_CP1_CI_AI
              WHERE I.ID = :id";
  $stmtMail = $conn->prepare($sqlMail);
  $stmtMail->bindParam(":id", $id);
  $stmtMail->execute();
  $usuario = $stmtMail->fetch(PDO::FETCH_ASSOC);

  if ($usuario && !empty($usuario["EMAIL"])) {
    $emailDestino = $usuario["EMAIL"];
    $nombreDestino = $usuario["NOMBRE"];
    $prioridad = ucfirst(strtolower($usuario["PRIORIDAD"] ?? "Desconocida"));

    // 🕒 Calcular duración estimada según prioridad
    switch (strtolower($usuario["PRIORIDAD"])) {
      case "alta":
        $duracion = "20 minutos";
        break;
      case "media":
        $duracion = "40 minutos";
        break;
      case "baja":
        $duracion = "60 minutos";
        break;
      default:
        $duracion = "no especificada";
        break;
    }

    // 🔹 Personalizar mensaje según el estado
    $asunto = "";
    $mensaje = "";
    $estadoMayus = ucfirst($estado);

    switch ($estado) {
      case "pendiente":
        $asunto = "🕓 Tu incidencia fue registrada como Pendiente";
        $mensaje = "
          <h2>Incidencia pendiente</h2>
          <p>Hola <strong>{$nombreDestino}</strong>,</p>
          <p>Tu incidencia (ID #{$id}) fue registrada y se encuentra <strong>pendiente</strong> de revisión.</p>
          <p>Pronto será asignada a un técnico.</p>
        ";
        break;

      case "asignado":
        $asunto = "👨‍🔧 Tu incidencia fue asignada a un técnico";
        $mensaje = "
          <h2>Incidencia asignada</h2>
          <p>Hola <strong>{$nombreDestino}</strong>,</p>
          <p>Tu incidencia (ID #{$id}) fue <strong>asignada</strong> a <strong>{$usuarioAccion}</strong> del equipo de sistemas.</p>
          <p>El tecnico ha comenzado con la resolucion de la incidencia.</p>
          <br>
          <p><strong>Ticket con prioridad {$prioridad}</strong> se estima un tiempo de resolucion no mayor a <strong>{$duracion}</strong>.</p>
        ";
        break;

      case "procesando":
        $asunto = "⚙️ Tu incidencia está siendo procesada";
        $mensaje = "
          <h2>Incidencia en proceso</h2>
          <p>Hola <strong>{$nombreDestino}</strong>,</p>
          <p>Tu incidencia (ID #{$id}) se encuentra actualmente <strong>atendido</strong>.</p>
          <p>Un técnico está trabajando en la resolución.</p>
        ";
        break;

      case "resuelto":
        $asunto = "✅ Tu incidencia fue resuelta";
        $mensaje = "
          <h2>Incidencia resuelta</h2>
          <p>Hola <strong>{$nombreDestino}</strong>,</p>
          <p>Tu incidencia (ID #{$id}) fue marcada como <strong>resuelta</strong> por <strong>{$usuarioAccion}</strong>.</p>
          <p>Si el problema fue resuelto, por favor cerrar la incidencia. De lo contrario marcar como 'No resuelto'</p>
        ";
        break;

      default:
        $asunto = "📢 Actualización del estado de tu incidencia";
        $mensaje = "
          <h2>Actualización del estado</h2>
          <p>Hola <strong>{$nombreDestino}</strong>,</p>
          <p>El estado de tu incidencia (ID #{$id}) cambió a <strong>{$estadoMayus}</strong>.</p>
        ";
    }

    // ✉️ Pie del mensaje
    $mensaje .= "
      <br>
      <p style='color:#666;font-size:12px;'>
        Por favor, no respondas este correo.<br>
        Equipo de Sistemas - Exgadet S.A.
      </p>
    ";

    // --- Enviar con PHPMailer ---
    try {
      $mail = new PHPMailer(true);
      $mail->isSMTP();
      $mail->Host       = 'hd87.wcaup.com';
      $mail->SMTPAuth   = true;
      $mail->Username   = 'soportesistemas@exgadetsa.com.ar';
      $mail->Password   = 'JSkN9TFl'; // ⚠️ proteger este valor en .env
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = 587;

      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';

      $mail->setFrom('soportesistemas@exgadetsa.com.ar', 'Soporte Sistemas Exgadet');
      $mail->addAddress($emailDestino, $nombreDestino);

      $mail->isHTML(true);
      $mail->Subject = $asunto;
      $mail->Body = $mensaje;

      $mail->send();
      $msg = "✅ Estado actualizado y correo enviado a {$nombreDestino}.";
    } catch (Exception $e) {
      $msg = "⚠️ Estado actualizado, pero no se pudo enviar el correo: {$mail->ErrorInfo}";
    }
  } else {
    $msg = "✅ Estado actualizado, pero el usuario no tiene email registrado.";
  }
}

// --- Consulta incidencias agrupadas por sector ---
$sql = "SELECT SECTOR, 
               COUNT(*) as total,
               SUM(CASE WHEN ESTADO <> 'resuelto' THEN 1 ELSE 0 END) as pendientes
        FROM INCIDENCIAS
        GROUP BY SECTOR";
$stmt = $conn->query($sql);
$sectores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Traer incidencias completas ---
$sql = "SELECT * FROM INCIDENCIAS 
        ORDER BY 
          CASE 
            WHEN ESTADO = 'resuelto' THEN 2  
            ELSE 1                           
          END,
          CASE 
            WHEN PRIORIDAD = 'Alta' THEN 1
            WHEN PRIORIDAD = 'Media' THEN 2
            WHEN PRIORIDAD = 'Baja' THEN 3
            ELSE 4
          END,
          FECHA_CREACION DESC";
$stmt = $conn->query($sql);
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Agrupar incidencias por sector ---
$incidenciasPorSector = [];
foreach ($incidencias as $inc) {
  $incidenciasPorSector[$inc["SECTOR"]][] = $inc;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Análisis de Incidencias</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/analisis-incidencias.css">
  <script>
    function toggleSector(id) {
      const div = document.getElementById("sector-" + id);
      div.style.display = (div.style.display === "none") ? "block" : "none";
    }
  </script>
</head>

<body>
  <?php include './nav.php'; ?>
  <main>
    <a href="usuario.php" class="btn-volver">⬅ Volver</a>
    <h1>📊 Análisis de Incidencias</h1>

    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

    <div class="sectores">
      <?php foreach ($sectores as $s): ?>
        <div class="sector-card" onclick="toggleSector('<?= htmlspecialchars($s['SECTOR']) ?>')">
          <h2><?= htmlspecialchars($s["SECTOR"]) ?></h2>
          <div class="badge"><?= $s["pendientes"] ?> pendientes</div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php foreach ($incidenciasPorSector as $sector => $lista): ?>
      <div class="detalle-sector" id="sector-<?= htmlspecialchars($sector) ?>" style="display:none;">
        <h3>📌 <?= htmlspecialchars($sector) ?></h3>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Tarea</th> <!-- 🆕 nueva columna -->
              <th>Prioridad</th> <!-- 🆕 nueva columna -->
              <th>Mensaje</th>
              <th>Imagen</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acción</th>
              <th>Editar/Eliminar</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lista as $inc): ?>
              <tr class="<?= strtolower($inc["ESTADO"]) ?>"
                data-id="<?= $inc["ID"] ?>"
                data-mensaje="<?= htmlspecialchars($inc["MENSAJE"]) ?>"
                data-imagen="<?= htmlspecialchars($inc["IMAGEN"]) ?>">
                <td><?= $inc["ID"] ?></td>
                <td><?= htmlspecialchars($inc["NOMBRE"]) ?></td>
                <td><?= htmlspecialchars($inc["TAREA"] ?? '-') ?></td> <!-- nueva -->
                <?php
                $prioridad = strtolower($inc["PRIORIDAD"]);
                $clasePrioridad = '';
                if ($prioridad === 'alta') $clasePrioridad = 'prioridad-alta';
                elseif ($prioridad === 'media') $clasePrioridad = 'prioridad-media';
                elseif ($prioridad === 'baja') $clasePrioridad = 'prioridad-baja';
                ?>
                <td class="<?= $clasePrioridad ?>"><?= htmlspecialchars($inc["PRIORIDAD"] ?? '-') ?></td>
                <td class="mensaje" title="<?= htmlspecialchars($inc["MENSAJE"]) ?>"><?= htmlspecialchars($inc["MENSAJE"]) ?></td>
                <td>
                  <?php if ($inc["IMAGEN"]): ?>
                    <a href="<?= htmlspecialchars($inc["IMAGEN"]) ?>" target="_blank">
                      <img src="<?= htmlspecialchars($inc["IMAGEN"]) ?>" alt="Evidencia">
                    </a>
                  <?php else: ?> - <?php endif; ?>
                </td>
                <td><?= date("d/m/Y H:i", strtotime($inc["FECHA_CREACION"])) ?></td>
                <td>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $inc["ID"] ?>">
                    <select name="estado">
                      <option value="pendiente" <?= $inc["ESTADO"] == "pendiente" ? "selected" : "" ?>>Pendiente</option>
                      <option value="asignado" <?= $inc["ESTADO"] == "asignado" ? "selected" : "" ?>>Asignado</option>
                      <option value="procesando" <?= $inc["ESTADO"] == "procesando" ? "selected" : "" ?>>Procesando</option>
                      <option value="resuelto" <?= $inc["ESTADO"] == "resuelto" ? "selected" : "" ?>>Resuelto</option>
                    </select>
                </td>
                <td><button type="submit">Actualizar</button></form>
                </td>
                <td class="acciones">
                  <button class="editar">Editar</button>
                  <button class="eliminar">Eliminar</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    <?php endforeach; ?>
  </main>

  <div id="modalIncidencia" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Editar Incidencia</h3>
        <span class="close" id="cerrarModal">&times;</span>
      </div>
      <div id="contenidoModal"></div>
    </div>
  </div>

  <script src="../js/menu_hambur-modo.js"></script>
  <script>
    const modal = document.getElementById("modalIncidencia");
    const contenido = document.getElementById("contenidoModal");
    const cerrar = document.getElementById("cerrarModal");

    document.querySelectorAll(".editar").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation();
        const fila = btn.closest("tr");
        const datos = fila.dataset;

        contenido.innerHTML = `
          <form method="POST" enctype="multipart/form-data" action="../php/editar-incidencia.php">
            <input type="hidden" name="id" value="${datos.id}">
            <label><b>Mensaje:</b></label>
            <textarea name="mensaje" style="width:100%;min-height:120px;">${datos.mensaje}</textarea>
            <label><b>Imagen (opcional):</b></label>
            ${datos.imagen ? `<p>Imagen actual:<br><img src="${datos.imagen}" style="max-width:100px;"></p>` : ""}
            <input type="file" name="imagen" accept="image/*">
            <button type="submit" style="margin-top:10px;background:#28a745;color:#fff;border:none;padding:10px 15px;border-radius:6px;cursor:pointer;">Guardar cambios</button>
          </form>
        `;
        modal.style.display = "flex";
      });
    });

    document.querySelectorAll(".eliminar").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation();
        const id = btn.closest("tr").dataset.id;
        if (confirm("¿Seguro que querés eliminar la incidencia " + id + "?")) {
          location.href = "../php/eliminar-incidencia.php?id=" + id;
        }
      });
    });

    cerrar.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => {
      if (e.target === modal) modal.style.display = "none";
    });
  </script>
</body>

</html>