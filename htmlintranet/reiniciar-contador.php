<?php
session_start();

// Si no hay sesión, redirigir al login
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php?error=1");
    exit();
}

require "../php/conexion.php";

// =============================
// SI EL USUARIO APRETÓ EL BOTÓN
// =============================
$mensaje = "";
$reiniciado = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar"])) {

    $hoy = date("Y-m-d");

    // Traer ID del contador
    $sql = "SELECT TOP 1 id FROM contador_accidentes";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $id = $data["id"];

        $update = $conn->prepare("
            UPDATE contador_accidentes
            SET dias = 0,
                fecha_ultima_actualizacion = :hoy
            WHERE id = :id
        ");

        $update->execute([
            ":hoy" => $hoy,
            ":id" => $id
        ]);

        $mensaje = "El contador fue reiniciado correctamente.";
        $reiniciado = true;

    } else {
        $mensaje = "No se encontró la tabla del contador.";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reiniciar contador de días sin incidencias</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/reiniciar-contador.css">
</head>

<body>

<div class="box">
    <i class="fa-solid fa-triangle-exclamation icono"></i>
    <h2>Reiniciar contador de días sin incidencias</h2>

    <?php if ($reiniciado): ?>
        <p><?= $mensaje ?></p>
        <a class="btn-volver" href="usuario.php">
            <i class="fa-solid fa-arrow-left"></i> Volver al panel
        </a>

    <?php else: ?>
        <p>⚠️ Esta acción reiniciará el contador a <strong>0 días</strong>.  
        <br>¿Estás seguro de continuar?</p>

        <form method="POST">
            <button type="submit" name="confirmar" class="btn-confirmar">
                <i class="fa-solid fa-rotate-left"></i> Confirmar reinicio
            </button>

            <a href="usuario.php" class="btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

