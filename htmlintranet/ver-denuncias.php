<?php
require "../php/conexion.php"; // conexión PDO a SQL Server
session_start();

// Si no hay sesión, redirigir al login
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php?error=1");
  exit();
}

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

// 🔹 Eliminar sugerencia si se envía el parámetro "eliminar"
if (isset($_GET["eliminar"])) {
  $id = intval($_GET["eliminar"]);
  $sqlDel = "DELETE FROM DENUNCIAS WHERE ID = :id";
  $stmtDel = $conn->prepare($sqlDel);
  $stmtDel->bindParam(":id", $id);
  $stmtDel->execute();
  header("Location: ver-denuncias.php");
  exit;
}

// 🔹 Obtener sugerencias
$sql = "SELECT ID, SUGERENCIA, FECHA 
        FROM DENUNCIAS
        ORDER BY FECHA DESC";
$stmt = $conn->query($sql);
$sugerencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ver Sugerencias</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/ver-denuncias.css">
</head>
<body>

<?php include './nav.php'; ?>

  <div class="contenedor">
    <h2>📋 Denuncias Recibidas</h2>

    <div class="acciones">
      <a href="usuario.php" class="btn">Volver</a>
    </div>

    <?php if (empty($sugerencias)): ?>
      <p style="text-align:center; color:#666;">No hay sugerencias enviadas todavía.</p>
    <?php else: ?>
      <?php foreach ($sugerencias as $s): ?>
        <div class="sugerencia">
          <form method="get" onsubmit="return confirmarEliminacion();">
            <input type="hidden" name="eliminar" value="<?= $s['ID'] ?>">
            <button type="submit" class="btn-eliminar">🗑️ Eliminar</button>
          </form>
          <p><?= htmlspecialchars($s["SUGERENCIA"]) ?></p>
          <p class="fecha">📅 <?= date("d/m/Y H:i", strtotime($s["FECHA"])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script>
    function confirmarEliminacion() {
      return confirm("¿Seguro que querés eliminar esta denuncia?");
    }
  </script>
</body>
</html>

