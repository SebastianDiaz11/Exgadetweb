<?php
// 📁 subir_cv.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $puesto = trim($_POST["puesto"] ?? "");
    if (!$puesto) {
        echo "<script>alert('⚠️ Faltan datos del puesto.'); window.history.back();</script>";
        exit;
    }

    // 📂 Carpeta principal
    $baseDir = dirname(__DIR__) . "/CV/";
    if (!file_exists($baseDir)) mkdir($baseDir, 0777, true);

    // 📂 Carpeta del puesto
    $carpetaPuesto = $baseDir . preg_replace('/[^A-Za-z0-9 _-]/', '', $puesto) . "/";
    if (!file_exists($carpetaPuesto)) mkdir($carpetaPuesto, 0777, true);

    // 📎 Validar archivo
    if (!isset($_FILES["cv"]) || $_FILES["cv"]["error"] !== UPLOAD_ERR_OK) {
        echo "<script>alert('⚠️ Error al subir el archivo.'); window.history.back();</script>";
        exit;
    }

    $ext = strtolower(pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION));
    $permitidos = ["pdf", "doc", "docx"];
    if (!in_array($ext, $permitidos)) {
        echo "<script>alert('⚠️ Solo se permiten archivos PDF o Word.'); window.history.back();</script>";
        exit;
    }

    // 📄 Guardar con nombre único
    $nombreArchivo = "CV_" . date("Ymd_His") . "." . $ext;
    $rutaDestino = $carpetaPuesto . $nombreArchivo;

    if (move_uploaded_file($_FILES["cv"]["tmp_name"], $rutaDestino)) {
        echo "<script>alert('✅ CV enviado correctamente para el puesto {$puesto}.'); window.history.back();</script>";
    } else {
        echo "<script>alert('❌ No se pudo guardar el archivo.'); window.history.back();</script>";
    }
}
?>
