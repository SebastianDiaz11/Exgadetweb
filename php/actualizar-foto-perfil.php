<?php
$legajo = $_SESSION["usuario"];
$mensaje = "";

// Ruta de la foto actual o default
$archivo = "../imagenesintranet/usuarios/" . $legajo . ".jpg";
if (file_exists($archivo)) {
    $fotoPerfil = $archivo;
} else {
    $fotoPerfil = "../imagenesintranet/usuario.jpg";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["foto"])) {
    $dir = "../imagenesintranet/usuarios/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $archivo = $dir . $legajo . ".jpg";

    // Validar que sea imagen
    $tipo = mime_content_type($_FILES["foto"]["tmp_name"]);
    if (strpos($tipo, "image") !== false) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $archivo)) {
            $mensaje = "<p class='exito'>Foto actualizada correctamente ✅</p>";
            $fotoPerfil = $archivo; // actualizar preview
        } else {
            $mensaje = "<p class='error'>Error al subir la foto ❌</p>";
        }
    } else {
        $mensaje = "<p class='error'>El archivo debe ser una imagen ❌</p>";
    }
}
?>