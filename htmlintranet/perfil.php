<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php?error=1");
    exit;
}

require "../php/actualizar-foto-perfil.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/perfil.css">
</head>

<body>
    <?php include './nav.php'; ?>

    <div class="perfil-container">

        <!-- Foto actual -->
        <img src="<?php echo $fotoPerfil; ?>?t=<?php echo time(); ?>" alt="Foto de perfil" class="foto-actual" id="preview">

        <!-- Mensajes -->
        <?php echo $mensaje; ?>

        <!-- Formulario -->
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="foto" id="foto" accept="image/*" required>
            <button type="submit" class="actualizar">Actualizar foto</button>
        </form>

        <a href="usuario.php" class="volver">⬅ Volver</a>
    </div>

    <script>
        // Preview de la foto seleccionada
        const inputFoto = document.getElementById("foto");
        const preview = document.getElementById("preview");

        inputFoto.addEventListener("change", () => {
            const file = inputFoto.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        });
    </script>
</body>

</html>