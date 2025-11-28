<?php
session_start();
require "../php/conexion.php"; // conexión SQL Server con $conn

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

header('Content-Type: text/html; charset=UTF-8');

// 🧩 Si llega una solicitud POST de eliminación, procesar y salir
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"]) && $_POST["accion"] === "eliminar") {
  header('Content-Type: application/json; charset=UTF-8');
  $id = $_POST["id"] ?? null;

  if (!$id || !is_numeric($id)) {
    echo json_encode(["success" => false, "mensaje" => "ID inválido."]);
    exit;
  }

  try {
    $sql = "DELETE FROM dbo.bot WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      echo json_encode(["success" => true, "mensaje" => "✅ Consulta eliminada correctamente."]);
    } else {
      echo json_encode(["success" => false, "mensaje" => "⚠️ No se encontró el registro con ese ID."]);
    }
  } catch (PDOException $e) {
    echo json_encode(["success" => false, "mensaje" => "❌ Error en la base de datos: " . $e->getMessage()]);
  }
  exit;
}

// 🧠 Si no es eliminación, mostrar la tabla normalmente
if (!isset($_SESSION["usuario"])) {
  die("⚠️ Debe iniciar sesión para acceder a esta página.");
}

try {
  $legajo = $_SESSION["usuario"];

  // 🔹 Obtener SECTOR y CARGO del usuario logeado
  $sqlUser = "SELECT SECTOR, CARGO 
                FROM dbo.USUARIOS_DATOS 
                WHERE LEGAJO = :legajo";
  $stmtUser = $conn->prepare($sqlUser);
  $stmtUser->bindParam(":legajo", $legajo);
  $stmtUser->execute();
  $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    die("⚠️ No se encontró el sector o cargo del usuario logeado.");
  }

  $sectorUsuario = trim($usuario["SECTOR"]);
  $cargoUsuario  = trim($usuario["CARGO"]);

  // 🔹 Obtener los primeros 20 datos del bot filtrados por sector o cargo
  $sql = "SELECT TOP 20 id, nombre, dni, sector, consulta, fecha
            FROM dbo.bot
            WHERE sector = :sector OR sector = :cargo
            ORDER BY fecha DESC";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":sector", $sectorUsuario);
  $stmt->bindParam(":cargo", $cargoUsuario);
  $stmt->execute();
  $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("❌ Error de base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas del Bot | Exgadet S.A.</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --color-primario: #F9C031;
      --color-fondo: #f8f9fa;
      --color-texto: #222;
      --color-tabla: #ffffff;
      --color-hover: #ffeaa7;
      --color-borde: #ccc;
    }

    body.dark-mode {
      --color-fondo: #121212;
      --color-texto: #f1f1f1;
      --color-tabla: #1f1f1f;
      --color-hover: #2a2a2a;
      --color-borde: #333;
    }

    h1 {
      text-align: center;
      color: var(--color-primario);
      margin-bottom: 10px;
    }

    /* 🔹 NUEVO: botón Volver */
    .volver-btn {
      display: block;
      margin: 15px auto 25px auto;
      background: var(--color-primario);
      color: #222;
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
      text-decoration: none;
      text-align: center;
      width: fit-content;
    }

    .volver-btn:hover {
      background: #e5b928;
    }

    .info {
      text-align: center;
      color: var(--color-texto);
      margin-bottom: 25px;
    }

    input[type="text"] {
      display: block;
      margin: 0 auto 20px auto;
      padding: 10px 15px;
      width: 320px;
      border-radius: 8px;
      border: 1px solid var(--color-borde);
      background: var(--color-tabla);
      color: var(--color-texto);
      font-size: 15px;
      transition: all 0.2s ease-in-out;
    }

    input[type="text"]:focus {
      outline: none;
      border-color: var(--color-primario);
      box-shadow: 0 0 5px #f9c03188;
    }

    table {
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      border-collapse: collapse;
      background: var(--color-tabla);
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
      word-break: break-word;
    }

    th {
      background: var(--color-primario);
      color: #222;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: rgba(249, 192, 49, 0.1);
    }

    tr:hover {
      background-color: var(--color-hover);
    }

    td.consulta {
      max-width: 280px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      cursor: pointer;
    }

    .btn-borrar {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 6px 10px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .btn-borrar:hover {
      background: #bb2d3b;
    }

    .no-datos {
      text-align: center;
      padding: 20px;
      background: #fff3cd;
      border-radius: 10px;
      margin: 30px auto;
      max-width: 700px;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
      background-color: var(--color-tabla);
      margin: 5% auto;
      padding: 25px;
      border-radius: 12px;
      width: 60%;
      max-width: 650px;
      max-height: 80vh;
      overflow-y: auto;
      overflow-x: hidden;
      color: var(--color-texto);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .close {
      float: right;
      font-size: 22px;
      cursor: pointer;
    }

    .close:hover {
      color: red;
    }

    .popup-data p {
      margin: 8px 0;
      line-height: 1.5;
      word-wrap: break-word;
    }
  </style>
</head>

<body>
  <?php include './nav.php'; ?>

  <h1>📋 Consultas del Bot</h1>

  <!-- 🔹 BOTÓN VOLVER -->
  <a href="usuario.php" class="volver-btn">Volver</a>

  <div class="info">
    Mostrando registros del <b><?= htmlspecialchars($sectorUsuario) ?></b> o cargo <b><?= htmlspecialchars($cargoUsuario) ?></b>
  </div>

  <input type="text" id="filtroDni" placeholder="🔍 Buscar por DNI...">

  <?php if (empty($datos)): ?>
    <div class="no-datos">No hay registros disponibles para tu sector o cargo.</div>
  <?php else: ?>
    <table id="tablaBot">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>DNI</th>
          <th>Sector</th>
          <th>Consulta</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($datos as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d["id"]) ?></td>
            <td><?= htmlspecialchars($d["nombre"]) ?></td>
            <td><?= htmlspecialchars($d["dni"]) ?></td>
            <td><?= htmlspecialchars($d["sector"]) ?></td>
            <td class="consulta"
              data-id="<?= $d["id"] ?>"
              data-nombre="<?= htmlspecialchars($d["nombre"]) ?>"
              data-dni="<?= htmlspecialchars($d["dni"]) ?>"
              data-sector="<?= htmlspecialchars($d["sector"]) ?>"
              data-consulta="<?= htmlspecialchars($d["consulta"]) ?>"
              data-fecha="<?= htmlspecialchars($d["fecha"]) ?>">
              <?= htmlspecialchars($d["consulta"]) ?>
            </td>
            <td><?= date("d/m/Y H:i", strtotime($d["fecha"])) ?></td>
            <td><button class="btn-borrar" data-id="<?= $d["id"] ?>">Eliminar</button></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>

</html>