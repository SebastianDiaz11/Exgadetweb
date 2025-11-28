<?php
session_start();
require "conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php?error=1");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);
    $mensaje = trim($_POST["mensaje"] ?? "");
    $imagenRuta = null;

    if ($mensaje) {
        // Actualizar mensaje
        $sql = "UPDATE INCIDENCIAS SET MENSAJE = :mensaje WHERE ID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":mensaje", $mensaje);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        // Si subió nueva imagen
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $directorio = dirname(__DIR__) . "/imagenesintranet/incidencias/";
            if (!file_exists($directorio)) mkdir($directorio, 0777, true);

            $nombreArchivo = "incidencia_" . $id . "." . $ext;
            $rutaDestino = $directorio . $nombreArchivo;

            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                $imagenRuta = "../imagenesintranet/incidencias/" . $nombreArchivo;

                $sqlImg = "UPDATE INCIDENCIAS SET IMAGEN = :imagen WHERE ID = :id";
                $stmtImg = $conn->prepare($sqlImg);
                $stmtImg->bindParam(":imagen", $imagenRuta);
                $stmtImg->bindParam(":id", $id);
                $stmtImg->execute();
            }
        }

        header("Location: ../htmlintranet/analisis-incidencias.php?msg=edit_ok");
        exit;
    } else {
        header("Location: ../htmlintranet/analisis-incidencias.php?msg=edit_error");
        exit;
    }
}

