<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server o MySQL

if (!$conn) {
    die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

require "../php/crearservicioobrasflota-intranet.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Servicio / Obra / Flota</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/crear-servicio-obra-flota.css">
</head>

<body>
    <?php include './nav.php'; ?>

    <div class="contenedor">
        <a href="usuario.php" class="btn-volver">⬅ Volver</a>
        <h2><i class="fa-solid fa-wrench"></i> Crear Servicio / Obra / Flota</h2>

        <!-- 🔹 Formulario -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_editar" value="<?= $registroEditar['ID'] ?? '' ?>">

            <label for="emoji">Emoji</label>
            <select name="emoji" id="emoji" required>
                <option value="">Seleccioná un emoji</option>
                <option value="fa-solid fa-network-wired" <?= (isset($registroEditar) && $registroEditar['EMOJI'] == 'fa-solid fa-network-wired') ? 'selected' : '' ?>>🌐 Conectividad</option>
                <option value="fa-solid fa-screwdriver-wrench" <?= (isset($registroEditar) && $registroEditar['EMOJI'] == 'fa-solid fa-screwdriver-wrench') ? 'selected' : '' ?>>🛠️ Mantenimiento</option>
                <option value="fa-solid fa-house-chimney" <?= (isset($registroEditar) && $registroEditar['EMOJI'] == 'fa-solid fa-house-chimney') ? 'selected' : '' ?>>🏠 Construcción</option>
                <option value="fa-solid fa-fire-extinguisher" <?= (isset($registroEditar) && $registroEditar['EMOJI'] == 'fa-solid fa-fire-extinguisher') ? 'selected' : '' ?>>🔥 Seguridad</option>
                <option value="fa-solid fa-truck" <?= (isset($registroEditar) && $registroEditar['EMOJI'] == 'fa-solid fa-truck') ? 'selected' : '' ?>>🚚 Transporte</option>
                <option value="fa-solid fa-helmet-safety" <?= (isset($registroEditar) && $registroEditar['EMOJI'] == 'fa-solid fa-helmet-safety') ? 'selected' : '' ?>>👷 Seguridad laboral</option>
            </select>

            <label for="categoria">Categoría</label>
            <select name="categoria" id="categoria" required>
                <option value="">Seleccioná una categoría</option>
                <option value="Servicio" <?= (isset($registroEditar) && $registroEditar['CATEGORIA'] == 'Servicio') ? 'selected' : '' ?>>Servicio</option>
                <option value="Obra" <?= (isset($registroEditar) && $registroEditar['CATEGORIA'] == 'Obra') ? 'selected' : '' ?>>Obra</option>
                <option value="Flota" <?= (isset($registroEditar) && $registroEditar['CATEGORIA'] == 'Flota') ? 'selected' : '' ?>>Flota</option>
            </select>

            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" value="<?= $registroEditar['TITULO'] ?? '' ?>" placeholder="Ej: Nueva instalación eléctrica" required>

            <label for="cuerpo">Cuerpo</label>
            <textarea name="cuerpo" id="cuerpo" rows="4" placeholder="Descripción detallada..." required><?= $registroEditar['CUERPO'] ?? '' ?></textarea>

            <label for="comitente">Comitente</label>
            <input type="text" name="comitente" id="comitente" value="<?= $registroEditar['COMITENTE'] ?? '' ?>" placeholder="Ej: Metro Gas S.A.">

            <label for="fecha_inicio">Fecha de inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= $registroEditar['FECHA_INICIO'] ?? '' ?>">

            <label for="fecha_final">Fecha de finalización</label>
            <input type="date" name="fecha_final" id="fecha_final" value="<?= $registroEditar['FECHA_FINAL'] ?? '' ?>">

            <label for="imagen_principal">Imagen principal</label>
            <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*">

            <label for="imagenes_secundarias">Imágenes secundarias</label>
            <input type="file" name="imagenes_secundarias[]" id="imagenes_secundarias" accept="image/*" multiple>

            <button type="submit"><?= isset($registroEditar) ? 'Actualizar' : 'Guardar' ?></button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <!-- 🔹 Listado de registros -->
        <?php if ($registros): ?>
            <h3 style="margin-top:30px;color:#003f6b;">Registros creados</h3>
            <table>
                <tr>
                    <th>Emoji</th>
                    <th>Categoría</th>
                    <th>Título</th>
                    <th>Comitente</th>
                    <th>Fechas</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($registros as $r): ?>
                    <tr>
                        <td><i class="<?= htmlspecialchars($r['EMOJI']) ?>"></i></td>
                        <td><?= htmlspecialchars($r['CATEGORIA']) ?></td>
                        <td><?= htmlspecialchars($r['TITULO']) ?></td>
                        <td><?= htmlspecialchars($r['COMITENTE']) ?></td>
                        <td><?= htmlspecialchars($r['FECHA_INICIO']) ?> → <?= htmlspecialchars($r['FECHA_FINAL']) ?></td>
                        <td class="acciones">
                            <a href="?editar=<?= $r['ID'] ?>" class="editar">✏️</a>
                            <a href="?eliminar=<?= $r['ID'] ?>" class="eliminar" onclick="return confirm('¿Seguro que querés eliminar este registro?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;margin-top:20px;">No hay registros aún.</p>
        <?php endif; ?>
    </div>
</body>
</html>

