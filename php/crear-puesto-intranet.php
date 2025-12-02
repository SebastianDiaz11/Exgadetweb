<?php
$mensaje = "";
$editarPuesto = null;

// --- Crear ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"]) && $_POST["accion"] === "crear") {
  $titulo = trim($_POST["titulo"] ?? "");
  $direccion = trim($_POST["direccion"] ?? "");
  $linkedin = trim($_POST["linkedin"] ?? "");
  $acerca = $_POST["acerca"] ?? "";

  if ($titulo && $direccion && $linkedin && $acerca) {
    try {
      $sql = "INSERT INTO M950_PUESTOS (TITULO, DIRECCION, LINKEDIN, FECHA_PUBLICACION, ACERCA)
                    VALUES (:titulo, :direccion, :linkedin, GETDATE(), :acerca)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":titulo", $titulo);
      $stmt->bindParam(":direccion", $direccion);
      $stmt->bindParam(":linkedin", $linkedin);
      $stmt->bindParam(":acerca", $acerca);
      $stmt->execute();
      $_SESSION["mensaje_puesto"] = "✅ Puesto creado correctamente.";
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
    } catch (PDOException $e) {
      $_SESSION["mensaje_puesto"] = "❌ Error SQL: " . $e->getMessage();
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
    }
  }
}

// --- Eliminar ---
if (isset($_GET["eliminar"])) {
  $id = intval($_GET["eliminar"]);
  $stmt = $conn->prepare("DELETE FROM M950_PUESTOS WHERE ID = :id");
  $stmt->bindParam(":id", $id);
  $stmt->execute();
  $_SESSION["mensaje_puesto"] = "🗑️ Puesto eliminado correctamente.";
  header("Location: " . $_SERVER["PHP_SELF"]);
  exit;
}

// --- Editar ---
if (isset($_GET["editar"])) {
  $id = intval($_GET["editar"]);
  $stmt = $conn->prepare("SELECT * FROM M950_PUESTOS WHERE ID = :id");
  $stmt->bindParam(":id", $id);
  $stmt->execute();
  $editarPuesto = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- Actualizar ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"]) && $_POST["accion"] === "actualizar") {
  $id = intval($_POST["id"]);
  $titulo = trim($_POST["titulo"] ?? "");
  $direccion = trim($_POST["direccion"] ?? "");
  $linkedin = trim($_POST["linkedin"] ?? "");
  $acerca = $_POST["acerca"] ?? "";

  if ($titulo && $direccion && $linkedin && $acerca) {
    $sql = "UPDATE M950_PUESTOS
                   SET TITULO=:titulo, DIRECCION=:direccion, LINKEDIN=:linkedin, ACERCA=:acerca
                 WHERE ID=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":direccion", $direccion);
    $stmt->bindParam(":linkedin", $linkedin);
    $stmt->bindParam(":acerca", $acerca);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $_SESSION["mensaje_puesto"] = "✏️ Puesto actualizado correctamente.";
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
  }
}

if (isset($_SESSION["mensaje_puesto"])) {
  $mensaje = $_SESSION["mensaje_puesto"];
  unset($_SESSION["mensaje_puesto"]);
}

$stmt = $conn->query("
    SELECT ID, TITULO, DIRECCION, LINKEDIN, FECHA_PUBLICACION, PAUSADA
    FROM M950_PUESTOS
    ORDER BY FECHA_PUBLICACION DESC
");
$puestos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- PAUSAR / ACTIVAR ---
if (isset($_GET["toggle"])) {
  $id = intval($_GET["toggle"]);

  // Obtener estado actual
  $stmt = $conn->prepare("SELECT PAUSADA FROM M950_PUESTOS WHERE ID = :id");
  $stmt->bindParam(":id", $id);
  $stmt->execute();
  $puesto = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($puesto) {
    $nuevoEstado = $puesto["PAUSADA"] ? 0 : 1;

    $up = $conn->prepare("UPDATE M950_PUESTOS SET PAUSADA = :p WHERE ID = :id");
    $up->bindParam(":p", $nuevoEstado, PDO::PARAM_INT);
    $up->bindParam(":id", $id);
    $up->execute();

    $_SESSION["mensaje_puesto"] =
      $nuevoEstado ? "⏸️ Puesto pausado." : "▶️ Puesto activado.";

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
  }
}
?>