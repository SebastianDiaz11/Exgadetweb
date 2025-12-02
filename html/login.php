<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
    die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

require "../php/inicio-sesion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <img src="../imagenes/logo.png" alt="Logo de la empresa" class="logo">
            <h2>Iniciar sesión</h2>

            <form method="POST" action="">
                <div class="input-group">
                    <i class="fa-solid fa-id-card"></i>
                    <input type="text" name="legajo" placeholder="Legajo" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="dni" name="dni" placeholder="Número de documento" required>
                    <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                </div>

                <button type="submit" class="btn-login">Entrar</button>

                <?php if ($mensaje): ?>
                    <p class="mensaje"><?php echo $mensaje; ?></p>
                <?php endif; ?>
            </form>

            <button class="btn-volver" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </button>
        </div>
    </div>

    <script>
        // 👁️ Mostrar/Ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('dni');
            const icon = this;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>

</html>