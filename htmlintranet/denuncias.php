<?php
require "../php/conexion.php";
session_start();

// Si no hay sesión, redirigir al login
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php?error=1");
  exit();
}

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

// ===================================================
// 🔹 Evitar duplicados con token de sesión (Post/Redirect/Get)
// ===================================================
if (empty($_SESSION['token'])) {
  $_SESSION['token'] = bin2hex(random_bytes(16));
}

$mensaje = "";

// Si el usuario envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    $sugerencia = trim($_POST["sugerencia"]);

    if (!empty($sugerencia)) {
      $sql = "INSERT INTO DENUNCIAS (SUGERENCIA, FECHA)
              VALUES (:sugerencia, GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":sugerencia", $sugerencia);
      $stmt->execute();

      // ✅ Evita reenvío duplicado al refrescar
      $_SESSION['token'] = bin2hex(random_bytes(16));
      header("Location: denuncias.php?ok=1");
      exit();
    } else {
      $mensaje = "⚠️ Escribí una denuncia antes de enviar.";
    }
  }
}

if (isset($_GET['ok'])) {
  $mensaje = "✅ ¡Gracias por tu denuncia!";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Buzón de Sugerencias</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/denuncias.css">
</head>
<body>

<?php include './nav.php'; ?>

  <div class="contenedor">
    <a href="usuario.php" class="btn">Volver</a>
    <h2>💬 Buzón de Denuncias</h2>
    <p>Tu denuncia será completamente anónima y nos ayudará a mejorar.</p>

    <form method="POST" action="">
      <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
      <textarea name="sugerencia" placeholder="Escribí tu sugerencia aquí..."></textarea>
      <br>
      <button class="sugerencia" type="submit">Enviar denuncia</button>
    </form>

    <?php if (!empty($mensaje)): ?>
      <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'ok' : 'err' ?>">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>


