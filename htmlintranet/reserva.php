<?php
session_start();
require "../php/conexion.php"; // Conexión PDO a SQL Server

require "../php/reserva-sala-email.php"; 
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salas de reunión</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/reserva.css">
</head>

<body>

  <?php include './nav.php'; ?>

  <main>
    <!-- Calendario -->
    <aside class="sidebar">

      <h3>Calendario</h3>

      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <a href="?mes=<?= $mesAnt ?>&anio=<?= $anioAnt ?>" style="color:#F9C031;text-decoration:none;">«</a>
        <strong><?= $nombreMes ?></strong>
        <a href="?mes=<?= $mesSig ?>&anio=<?= $anioSig ?>" style="color:#F9C031;text-decoration:none;">»</a>
      </div>

      <table class="calendar">
        <thead>
          <tr>
            <th>L</th>
            <th>M</th>
            <th>M</th>
            <th>J</th>
            <th>V</th>
            <th>S</th>
            <th>D</th>
          </tr>
        </thead>

        <tbody>
          <?php
          $contador = 1;

          echo "<tr>";

          for ($i = 1; $i < $inicioSemana; $i++) {
            echo "<td></td>";
          }

          for ($i = $inicioSemana; $i <= 7; $i++) {
            if ($contador <= $diasMes) {
              $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $contador);
              $clase = ($fecha == date("Y-m-d")) ? "hoy" : "";
              echo "<td class='$clase' onclick='mostrarReunionesDia(\"$fecha\")'>$contador</td>";
              $contador++;
            }
          }

          echo "</tr>";

          while ($contador <= $diasMes) {
            echo "<tr>";
            for ($i = 1; $i <= 7; $i++) {
              if ($contador <= $diasMes) {
                $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $contador);
                $clase = ($fecha == date("Y-m-d")) ? "hoy" : "";
                echo "<td class='$clase' onclick='mostrarReunionesDia(\"$fecha\")'>$contador</td>";
                $contador++;
              } else {
                echo "<td></td>";
              }
            }
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>

      <div class="filtros">
        <h3>Colores de salas</h3>
        <label><span class="color-box" style="background:#2ecc71;"></span> 1 - SUM - Comedor</label>
        <label><span class="color-box" style="background:#9b59b6;"></span> 2 - Sala de reuniones 2</label>
      </div>

    </aside>

    <!-- Reuniones -->
    <section class="reuniones">
      <div class="reuniones-header">
        <h2>Reuniones del día</h2>
        <button class="btn-nueva" onclick="abrirModal()">+ Nueva reunión</button>
        <a href="./usuario.php"><button class="btn-volver">Volver</button></a>
      </div>

      <div id="contenedor-reuniones">
        <?php foreach ($reuniones as $r): ?>
          <div class="bloque" style="border-left-color:<?= $colores[$r['EQUIPO']] ?? '#555' ?>; position:relative;">

            <div class="acciones-bloque">
              <i class="fa-solid fa-copy icono copiar-bloque"
                 data-info="
Sala: <?= htmlspecialchars($r['EQUIPO']) ?>

Día: <?= htmlspecialchars($r['DIA']) ?>

Hora: <?= date('H:i', strtotime($r['HORA'])) ?> a <?= date('H:i', strtotime($r['HASTA'])) ?>

Motivo: <?= htmlspecialchars($r['MOTIVO']) ?>

Infusiones: <?= htmlspecialchars($r['INFUSIONES']) ?>

Personas: <?= htmlspecialchars($r['PERSONAS']) ?>

Elementos tecnologicos: <?= htmlspecialchars($r['TECNOLOGIA']) ?>

Reservado por: <?= htmlspecialchars($r['NOMBRE']) ?>"
                 title="Copiar"></i>

              <i class="fa-solid fa-eye icono ver-bloque"
                 data-info="
Sala: <?= htmlspecialchars($r['EQUIPO']) ?>

Día: <?= htmlspecialchars($r['DIA']) ?>

Hora: <?= date('H:i', strtotime($r['HORA'])) ?> a <?= date('H:i', strtotime($r['HASTA'])) ?>

Motivo: <?= htmlspecialchars($r['MOTIVO']) ?>

Infusiones: <?= htmlspecialchars($r['INFUSIONES']) ?>

Personas: <?= htmlspecialchars($r['PERSONAS']) ?>

Elementos tecnologicos: <?= htmlspecialchars($r['TECNOLOGIA']) ?>

Reservado por: <?= htmlspecialchars($r['NOMBRE']) ?>"
                 title="Ver detalles"></i>
            </div>

            <strong><?= htmlspecialchars($r['EQUIPO']) ?></strong> •
            <?= date("H:i", strtotime($r['HORA'])) ?> – <?= date("H:i", strtotime($r['HASTA'])) ?> hs<br>

            <?= htmlspecialchars($r['MOTIVO']) ?><br>
            <small><?= htmlspecialchars($r['NOMBRE']) ?></small>

            <br>Infusiones: <strong><?= htmlspecialchars($r['INFUSIONES']) ?></strong>
            <br>Personas: <strong><?= htmlspecialchars($r['PERSONAS']) ?></strong>
            <br>Elementos tecnologicos: <strong><?= htmlspecialchars($r['TECNOLOGIA']) ?></strong>

          </div>
        <?php endforeach; ?>

        <?php if (empty($reuniones)): ?>
          <p style="color:#999;">No hay reuniones programadas.</p>
        <?php endif; ?>
      </div>

    </section>

  </main>

  <section class="mis-reuniones">
    <h3>Mis reuniones</h3>
    <table>
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Desde</th>
          <th>Hasta</th>
          <th>Sala</th>
          <th>Motivo</th>
          <th>Infusiones</th>
          <th>Personas</th>
          <th>Elementos tecnologicos</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($misReuniones as $m): ?>
          <tr data-id="<?= $m['id'] ?>">

            <td contenteditable data-campo="DIA"><?= $m['DIA'] ?></td>
            <td contenteditable data-campo="HORA"><?= $m['HORA'] ?></td>
            <td contenteditable data-campo="HASTA"><?= htmlspecialchars($m['HASTA']) ?></td>
            <td contenteditable data-campo="EQUIPO"><?= htmlspecialchars($m['EQUIPO']) ?></td>
            <td contenteditable data-campo="MOTIVO"><?= htmlspecialchars($m['MOTIVO']) ?></td>
            <td contenteditable data-campo="INFUSIONES"><?= htmlspecialchars($m['INFUSIONES']) ?></td>
            <td contenteditable data-campo="PERSONAS"><?= htmlspecialchars($m['PERSONAS']) ?></td>
            <td contenteditable data-campo="TECNOLOGIA"><?= htmlspecialchars($m['TECNOLOGIA']) ?></td>
            

            <td>
              <span class="edit">💾 Guardar</span>
              <span class="delete">🗑 Borrar</span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </section>

  <!-- MODAL -->
  <div id="modal-overlay" onclick="cerrarModal()"></div>

  <div id="modal">
    <form id="formReunion" method="POST" class="form-reunion">
      <h3>🗓 Nueva reunión</h3>

      <div class="grid-datos">

        <div class="campo"><label>Legajo:</label><input type="text" value="<?= $legajo ?>" readonly></div>
        <div class="campo"><label>Nombre:</label><input type="text" value="<?= $nombreCompleto ?>" readonly></div>
        <div class="campo"><label>Sector:</label><input type="text" value="<?= $sector ?>" readonly></div>
        <div class="campo"><label>Área:</label><input type="text" value="<?= $area ?>" readonly></div>

        <div class="campo">
          <label>Sala:</label>
          <select name="equipo" required>
            <option value="">Seleccione una sala</option>
            <option value="SUM - Comedor">1 - SUM - Comedor</option>
            <option value="Sala de reuniones 2">2 - Sala de reuniones 2</option>
          </select>
        </div>

        <div class="campo">
          <label>Día:</label>
          <input type="date" name="dia" required>
        </div>

        <div class="campo">
          <label>Hora desde:</label>
          <input type="time" name="hora" required>
        </div>

        <div class="campo">
          <label>Hora hasta:</label>
          <input type="time" name="hasta" required>
        </div>

        <div class="campo full">
          <label>Motivo:</label>
          <textarea name="motivo" rows="3" required></textarea>
        </div>

        <div class="campo">
          <label>Infusiones:</label>
          <select name="infusiones" required>
            <option value="">Seleccione</option>
            <option value="SI">SI</option>
            <option value="NO">NO</option>
          </select>
        </div>

        <div class="campo">
          <label>Personas:</label>
          <input type="number" name="personas" min="1" required>
        </div>

        <div class="campo">
          <label>Elementos tecnologicos:</label>
          <select name="tecnologia" required>
            <option value="">Seleccione</option>
            <option value="SI">SI</option>
            <option value="NO">NO</option>
          </select>
        </div>

      </div>

      <div id="msgBox"></div>

      <div class="botones">
        <button type="submit" name="crear_reunion" class="guardar">Guardar reunión</button>
        <button type="button" class="cerrar" onclick="cerrarModal()">Cancelar</button>
      </div>

    </form>
  </div>

  <!-- Popup -->
  <div class="popup-overlay" id="popup-overlay"></div>

  <div class="popup-info" id="popup-info">
    <h3>Detalle de la reserva</h3>
    <pre id="popup-text"></pre>
    <button class="btn-cerrar" onclick="cerrarPopup()">Cerrar</button>
  </div>

  <script src="../js/reserva-sala1.js"></script>

</body>

</html>

