<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server o MySQL

if (!$conn) {
    die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

$mensaje = "";

// ======================================================
// 🔹 ELIMINAR REGISTRO
// ======================================================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM CREAR_SERVICIO_OBRA_FLOTA WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $mensaje = "🗑️ Registro eliminado correctamente.";
}

// ======================================================
// 🔹 EDITAR REGISTRO
// ======================================================
if (isset($_GET['editar'])) {
    $idEditar = intval($_GET['editar']);
    $sql = "SELECT * FROM CREAR_SERVICIO_OBRA_FLOTA WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $idEditar, PDO::PARAM_INT);
    $stmt->execute();
    $registroEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ======================================================
// 🔹 CREAR / ACTUALIZAR REGISTRO
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emoji = $_POST["emoji"] ?? "";
    $categoria = strtolower(trim($_POST["categoria"] ?? ""));
    $titulo = trim($_POST["titulo"] ?? "");
    $cuerpo = trim($_POST["cuerpo"] ?? "");
    $comitente = trim($_POST["comitente"] ?? "");
    $fecha_inicio = $_POST["fecha_inicio"] ?? null;
    $fecha_final = $_POST["fecha_final"] ?? null;
    $id_editar = $_POST["id_editar"] ?? "";

    $directorio = "../imagenes/obras/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // ======================================================
    // 🔸 INSERTAR O ACTUALIZAR EN BD
    // ======================================================
    if ($emoji && $categoria && $titulo && $cuerpo) {
        if ($id_editar) {
            // 🔹 Si se está editando
            $sql = "UPDATE CREAR_SERVICIO_OBRA_FLOTA 
                    SET EMOJI = :emoji, CATEGORIA = :categoria, TITULO = :titulo, 
                        CUERPO = :cuerpo, COMITENTE = :comitente, 
                        FECHA_INICIO = :fecha_inicio, FECHA_FINAL = :fecha_final
                    WHERE ID = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $id_editar);
            $stmt->bindParam(":emoji", $emoji);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":titulo", $titulo);
            $stmt->bindParam(":cuerpo", $cuerpo);
            $stmt->bindParam(":comitente", $comitente);
            $stmt->bindParam(":fecha_inicio", $fecha_inicio);
            $stmt->bindParam(":fecha_final", $fecha_final);
            $stmt->execute();
            $registroID = $id_editar;
        } else {
            // 🔹 Insertar nuevo registro y obtener el ID generado
            $sql = "INSERT INTO CREAR_SERVICIO_OBRA_FLOTA 
                    (EMOJI, CATEGORIA, TITULO, CUERPO, COMITENTE, FECHA_INICIO, FECHA_FINAL)
                    OUTPUT INSERTED.ID
                    VALUES (:emoji, :categoria, :titulo, :cuerpo, :comitente, :fecha_inicio, :fecha_final)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":emoji", $emoji);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":titulo", $titulo);
            $stmt->bindParam(":cuerpo", $cuerpo);
            $stmt->bindParam(":comitente", $comitente);
            $stmt->bindParam(":fecha_inicio", $fecha_inicio);
            $stmt->bindParam(":fecha_final", $fecha_final);
            $stmt->execute();
            $registroID = $stmt->fetchColumn(); // ID generado
        }

        // ======================================================
        // 🔸 SUBIR IMAGEN PRINCIPAL (usando ID)
        // ======================================================
        if (!empty($_FILES["imagen_principal"]["name"])) {
            $ext = strtolower(pathinfo($_FILES["imagen_principal"]["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $nombrePrincipal = "{$categoria}{$registroID}.{$ext}";
                $rutaPrincipal = $directorio . $nombrePrincipal;
                move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $rutaPrincipal);
            }
        }

        // ======================================================
        // 🔸 SUBIR IMÁGENES SECUNDARIAS (usando ID)
        // ======================================================
        if (!empty($_FILES["imagenes_secundarias"]["name"][0])) {
            $contador = 1;
            $baseNombre = "{$categoria}{$registroID}";
            foreach ($_FILES["imagenes_secundarias"]["name"] as $index => $nombre) {
                $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $nuevoNombre = "{$baseNombre}-{$contador}.{$ext}";
                    $rutaFinal = $directorio . $nuevoNombre;
                    move_uploaded_file($_FILES["imagenes_secundarias"]["tmp_name"][$index], $rutaFinal);
                    $contador++;
                }
            }
        }

        $mensaje = $id_editar
            ? "✏️ Registro actualizado correctamente."
            : "✅ Registro creado correctamente.";
    } else {
        $mensaje = "⚠️ Completá todos los campos antes de guardar.";
    }
}

// ======================================================
// 🔹 LEER REGISTROS EXISTENTES
// ======================================================
$sql = "SELECT * FROM CREAR_SERVICIO_OBRA_FLOTA ORDER BY FECHA_CREACION DESC";
$stmt = $conn->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Servicio / Obra / Flota</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/crear-servicio-obra-flota.css">
    <style>
        /* ==========================================================
   📱 RESPONSIVE – TABLET (≤ 992px)
   ========================================================== */

@media (max-width: 992px) {

    .contenedor {
        max-width: 700px;
        padding: 20px;
    }

    table th,
    table td {
        padding: 8px;
    }
}

/* ==========================================================
   📱 RESPONSIVE – CELULAR GRANDE (≤ 768px)
   ========================================================== */

@media (max-width: 768px) {

    .contenedor {
        max-width: 90%;
        margin: 20px auto;
    }

    h2 {
        font-size: 1.4rem;
    }

    /* Hacer la tabla scrolleable horizontal */
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    td i {
        font-size: 18px;
    }

    .acciones a {
        padding: 4px 8px;
        font-size: 0.9rem;
    }
}

/* ==========================================================
   📱 CELULAR MEDIANO (≤ 576px)
   ========================================================== */

@media (max-width: 576px) {

    .contenedor {
        padding: 15px;
    }

    h2 {
        font-size: 1.2rem;
    }

    label {
        font-size: 0.9rem;
    }

    button,
    .btn-volver {
        font-size: 0.9rem;
        padding: 8px;
    }

    textarea,
    input,
    select {
        font-size: 0.9rem;
        padding: 8px;
    }
}

/* ==========================================================
   📱 CELULAR CHICO (≤ 420px)
   ========================================================== */

@media (max-width: 420px) {

    .contenedor {
        padding: 12px;
        border-radius: 10px;
    }

    h2 {
        font-size: 1.1rem;
    }

    .acciones a {
        font-size: 0.8rem;
        padding: 4px 6px;
    }

    td,
    th {
        font-size: 0.85rem;
    }

    td i {
        font-size: 16px;
    }
}
    </style>
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

