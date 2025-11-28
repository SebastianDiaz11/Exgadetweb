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

// Validar que llegó un ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: ../htmlintranet/analisis-incidencias.php?msg=delete_error");
    exit;
}

$id = intval($_GET["id"]);

try {
    // Buscar primero la imagen asociada
    $sqlImg = "SELECT IMAGEN FROM INCIDENCIAS WHERE ID = :id";
    $stmt = $conn->prepare($sqlImg);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($incidencia) {
        // Si tiene imagen, eliminar el archivo físico
        if (!empty($incidencia["IMAGEN"])) {
            $rutaImagen = dirname(__DIR__) . "/" . ltrim($incidencia["IMAGEN"], "./");
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        // Eliminar de la base de datos
        $sqlDel = "DELETE FROM INCIDENCIAS WHERE ID = :id";
        $stmtDel = $conn->prepare($sqlDel);
        $stmtDel->bindParam(":id", $id);
        $stmtDel->execute();

        header("Location: ../htmlintranet/analisis-incidencias.php?msg=delete_ok");
        exit;
    } else {
        header("Location: ../htmlintranet/analisis-incidencias.php?msg=not_found");
        exit;
    }

} catch (Exception $e) {
    header("Location: ../htmlintranet/analisis-incidencias.php?msg=delete_error");
    exit;
}

