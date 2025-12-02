<?php
// --- Crear nueva noticia ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nueva'])) {
  $titulo = trim($_POST['titulo']);
  $cuerpo = trim($_POST['cuerpo']);

  if ($titulo && $cuerpo) {
    $sql = "INSERT INTO M900_NOTICIAS (TITULO, CUERPO, ACTIVA) 
                OUTPUT INSERTED.ID_NOTICIA 
                VALUES (:titulo, :cuerpo, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":cuerpo", $cuerpo);
    $stmt->execute();
    $idNoticia = $stmt->fetchColumn();

    // 📂 Imagen
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
      $directorio = dirname(__DIR__) . "/imagenesintranet/noticias/";
      if (!file_exists($directorio)) mkdir($directorio, 0777, true);

      $nombreArchivo = "noticia_" . $idNoticia . "." . $ext;
      $rutaDestino = $directorio . $nombreArchivo;

      if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $rutaRelativa = "../imagenesintranet/noticias/" . $nombreArchivo;
        $sqlImg = "UPDATE M900_NOTICIAS SET IMAGEN = :imagen WHERE ID_NOTICIA = :id";
        $stmtImg = $conn->prepare($sqlImg);
        $stmtImg->bindParam(":imagen", $rutaRelativa);
        $stmtImg->bindParam(":id", $idNoticia);
        $stmtImg->execute();
      }
    }

    header("Location: noticias-internas.php?msg=noticia_creada");
    exit;
  }
}

// --- Guardar cambios al editar ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
  $id     = intval($_POST['id']);
  $titulo = trim($_POST['titulo']);
  $cuerpo = trim($_POST['cuerpo']);

  $sql = "UPDATE M900_NOTICIAS 
            SET TITULO = :titulo, CUERPO = :cuerpo 
            WHERE ID_NOTICIA = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":titulo", $titulo);
  $stmt->bindParam(":cuerpo", $cuerpo);
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  // 📂 Nueva imagen si se carga
  if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
    $directorio = dirname(__DIR__) . "/imagenesintranet/noticias/";
    if (!file_exists($directorio)) mkdir($directorio, 0777, true);

    $nombreArchivo = "noticia_" . $id . "." . $ext;
    $rutaDestino = $directorio . $nombreArchivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
      $rutaRelativa = "../imagenesintranet/noticias/" . $nombreArchivo;
      $sqlImg = "UPDATE M900_NOTICIAS SET IMAGEN = :imagen WHERE ID_NOTICIA = :id";
      $stmtImg = $conn->prepare($sqlImg);
      $stmtImg->bindParam(":imagen", $rutaRelativa);
      $stmtImg->bindParam(":id", $id);
      $stmtImg->execute();
    }
  }

  header("Location: noticias-internas.php?msg=noticia_editada");
  exit;
}

// --- Eliminar noticia (marcar como inactiva) ---
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  $sql = "UPDATE M900_NOTICIAS SET ACTIVA = 0 WHERE ID_NOTICIA = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  header("Location: noticias-internas.php?msg=noticia_eliminada");
  exit;
}

// --- Consultar todas las noticias activas ---
$sql = "SELECT * FROM M900_NOTICIAS 
        WHERE ACTIVA = 1 
        ORDER BY FECHA_CREACION DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === SUBIR ORGANIGRAMA ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['subirOrganigrama'])) {

  if (isset($_FILES["organigrama"]) && $_FILES["organigrama"]["error"] === UPLOAD_ERR_OK) {

    $ext = strtolower(pathinfo($_FILES["organigrama"]["name"], PATHINFO_EXTENSION));

    if ($ext === "pdf") {

      $directorioOrg = dirname(__DIR__) . "/documentos/";
      if (!file_exists($directorioOrg)) {
        mkdir($directorioOrg, 0777, true);
      }

      $rutaDestinoOrg = $directorioOrg . "Organigrama.pdf";

      move_uploaded_file($_FILES["organigrama"]["tmp_name"], $rutaDestinoOrg);

      header("Location: noticias-internas.php?msg=organigrama_subido");
      exit;
    }
  }

  header("Location: noticias-internas.php?msg=organigrama_error");
  exit;
}
?>