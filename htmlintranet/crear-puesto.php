<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

require "../php/crear-puesto-intranet.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestión de Puestos</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/crear-puesto.css">

  <!-- TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/9f19457osvjg5grzkphydtj7vgtksyojd6kyeix45fji399n/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const darkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;
      tinymce.init({
        selector: "#acerca",
        menubar: false,
        plugins: "lists",
        toolbar: "bold italic underline | bullist numlist | undo redo",
        branding: false,
        height: 250,
        skin: darkMode ? "oxide-dark" : "oxide",
        content_css: darkMode ? "dark" : "default",
        setup: editor => editor.on("change", () => editor.save())
      });
    });
  </script>
</head>

<body>

  <?php include './nav.php'; ?>

  <div class="container">
    <a href="usuario.php" class="btn-volver">← Volver</a>

    <form method="POST">
      <h1><?= $editarPuesto ? "Editar Puesto" : "Crear Nuevo Puesto" ?></h1>
      <input type="hidden" name="accion" value="<?= $editarPuesto ? "actualizar" : "crear" ?>">
      <?php if ($editarPuesto): ?>
        <input type="hidden" name="id" value="<?= $editarPuesto["ID"] ?>">
      <?php endif; ?>

      <label>Título del puesto</label>
      <input type="text" name="titulo" value="<?= htmlspecialchars($editarPuesto["TITULO"] ?? "") ?>" required>

      <label>Dirección del trabajo</label>
      <input type="text" name="direccion" value="<?= htmlspecialchars($editarPuesto["DIRECCION"] ?? "") ?>" required>

      <label>Link de LinkedIn</label>
      <input type="text" name="linkedin" value="<?= htmlspecialchars($editarPuesto["LINKEDIN"] ?? "") ?>" required>

      <label>Acerca del empleo</label>
      <textarea name="acerca" id="acerca" required><?= htmlspecialchars($editarPuesto["ACERCA"] ?? "") ?></textarea>

      <button type="submit" class="btn"><?= $editarPuesto ? "Actualizar Puesto" : "Guardar Puesto" ?></button>
    </form>

    <?php if ($mensaje): ?>
      <div class="mensaje <?= strpos($mensaje, '✅') !== false || strpos($mensaje, '✏️') !== false || strpos($mensaje, '🗑️') !== false ? 'ok' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <h2 style="margin-top:40px;">🗂 Tus Puestos Activos</h2>

    <?php if ($puestos): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Dirección</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($puestos as $p): ?>
            <tr>

              <td><?= $p["ID"] ?></td>

              <td><?= htmlspecialchars($p["TITULO"]) ?></td>

              <td><?= htmlspecialchars($p["DIRECCION"]) ?></td>

              <td><?= date("d/m/Y", strtotime($p["FECHA_PUBLICACION"])) ?></td>

              <!-- ESTADO -->
              <td>
                <?= $p["PAUSADA"] ? "⏸️ Pausada" : "🟢 Activa" ?>
              </td>

              <!-- ACCIONES -->
              <td>
                <!-- EDITAR -->
                <a href="?editar=<?= $p["ID"] ?>" class="btn-accion editar">
                  Editar
                </a>

                <!-- PAUSAR / ACTIVAR -->
                <a href="?toggle=<?= $p["ID"] ?>"
                  class="btn-accion"
                  style="background: <?= $p["PAUSADA"] ? '#28a745' : '#6c757d' ?>;">
                  <?= $p["PAUSADA"] ? "Activar" : "Pausar" ?>
                </a>

                <!-- ELIMINAR -->
                <a href="?eliminar=<?= $p["ID"] ?>"
                  onclick="return confirm('¿Seguro que querés eliminar este puesto?')"
                  class="btn-accion eliminar">
                  Eliminar
                </a>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    <?php else: ?>
      <p style="text-align:center;margin-top:20px;">No hay puestos registrados.</p>
    <?php endif; ?>
  </div>

  <script src="../js/menu_hambur-modo.js"></script>
</body>

</html>