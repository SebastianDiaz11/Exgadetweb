<?php
session_start();
require_once "../php/conexion.php"; // conexión PDO a SQL Server

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

require_once "../php/mail-analisisincidencias-intranet.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Análisis de Incidencias</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/analisis-incidencias.css">
  <script>
    function toggleSector(id) {
      const div = document.getElementById("sector-" + id);
      div.style.display = (div.style.display === "none") ? "block" : "none";
    }
  </script>
</head>

<body>
  <?php include './nav.php'; ?>
  <main>
    <a href="usuario.php" class="btn-volver">⬅ Volver</a>
    <h1>📊 Análisis de Incidencias</h1>

    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

    <div class="sectores">
      <?php foreach ($sectores as $s): ?>
        <div class="sector-card" onclick="toggleSector('<?= htmlspecialchars($s['SECTOR']) ?>')">
          <h2><?= htmlspecialchars($s["SECTOR"]) ?></h2>
          <div class="badge"><?= $s["pendientes"] ?> pendientes</div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php foreach ($incidenciasPorSector as $sector => $lista): ?>
      <div class="detalle-sector" id="sector-<?= htmlspecialchars($sector) ?>" style="display:none;">
        <h3>📌 <?= htmlspecialchars($sector) ?></h3>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Tarea</th> <!-- 🆕 nueva columna -->
              <th>Prioridad</th> <!-- 🆕 nueva columna -->
              <th>Mensaje</th>
              <th>Imagen</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acción</th>
              <th>Editar/Eliminar</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lista as $inc): ?>
              <tr class="<?= strtolower($inc["ESTADO"]) ?>"
                data-id="<?= $inc["ID"] ?>"
                data-mensaje="<?= htmlspecialchars($inc["MENSAJE"]) ?>"
                data-imagen="<?= htmlspecialchars($inc["IMAGEN"]) ?>">
                <td><?= $inc["ID"] ?></td>
                <td><?= htmlspecialchars($inc["NOMBRE"]) ?></td>
                <td><?= htmlspecialchars($inc["TAREA"] ?? '-') ?></td> <!-- nueva -->
                <?php
                $prioridad = strtolower($inc["PRIORIDAD"]);
                $clasePrioridad = '';
                if ($prioridad === 'alta') $clasePrioridad = 'prioridad-alta';
                elseif ($prioridad === 'media') $clasePrioridad = 'prioridad-media';
                elseif ($prioridad === 'baja') $clasePrioridad = 'prioridad-baja';
                ?>
                <td class="<?= $clasePrioridad ?>"><?= htmlspecialchars($inc["PRIORIDAD"] ?? '-') ?></td>
                <td class="mensaje" title="<?= htmlspecialchars($inc["MENSAJE"]) ?>"><?= htmlspecialchars($inc["MENSAJE"]) ?></td>
                <td>
                  <?php if ($inc["IMAGEN"]): ?>
                    <a href="<?= htmlspecialchars($inc["IMAGEN"]) ?>" target="_blank">
                      <img src="<?= htmlspecialchars($inc["IMAGEN"]) ?>" alt="Evidencia">
                    </a>
                  <?php else: ?> - <?php endif; ?>
                </td>
                <td><?= date("d/m/Y H:i", strtotime($inc["FECHA_CREACION"])) ?></td>
                <td>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $inc["ID"] ?>">
                    <select name="estado">
                      <option value="pendiente" <?= $inc["ESTADO"] == "pendiente" ? "selected" : "" ?>>Pendiente</option>
                      <option value="asignado" <?= $inc["ESTADO"] == "asignado" ? "selected" : "" ?>>Asignado</option>
                      <option value="procesando" <?= $inc["ESTADO"] == "procesando" ? "selected" : "" ?>>Procesando</option>
                      <option value="resuelto" <?= $inc["ESTADO"] == "resuelto" ? "selected" : "" ?>>Resuelto</option>
                    </select>
                </td>
                <td><button type="submit">Actualizar</button></form>
                </td>
                <td class="acciones">
                  <button class="editar">Editar</button>
                  <button class="eliminar">Eliminar</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    <?php endforeach; ?>
  </main>

  <div id="modalIncidencia" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Editar Incidencia</h3>
        <span class="close" id="cerrarModal">&times;</span>
      </div>
      <div id="contenidoModal"></div>
    </div>
  </div>

  <script src="../js/menu_hambur-modo.js"></script>
  <script src="../js/analisis-incidencias.js"></script>
</body>

</html>