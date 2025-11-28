<?php
require "conexion.php"; // misma carpeta que este archivo (../php/)
if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"], $_POST["conforme"])) {
  $id = intval($_POST["id"]);
  $conforme = trim($_POST["conforme"]);

  try {
    $sql = "UPDATE INCIDENCIAS SET CONFORME = :conforme WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":conforme", $conforme);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    echo "✅ Guardado correctamente";
  } catch (Exception $e) {
    echo "⚠️ Error al actualizar: " . $e->getMessage();
  }
}
?>

