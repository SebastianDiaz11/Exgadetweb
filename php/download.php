<?php
if (!isset($_GET['file'])) {
    die("Archivo inválido.");
}

$file = basename($_GET['file']); // Sanitizar

// Ruta CORRECTA donde reclamos.php guarda los adjuntos
$path = __DIR__ . "/../htmlintranet/adjuntos/" . $file;

if (!file_exists($path)) {
    die("Archivo no encontrado.");
}

// Forzar descarga
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Length: " . filesize($path));

// Enviar archivo
readfile($path);

// Eliminar después de descargar (Opción 2)
unlink($path);

exit;

