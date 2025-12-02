<?php
session_start();
require "../php/conexion.php"; // conexión PDO a SQL Server

require "../php/mail-proveedores-web.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formulario de Proveedores</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/proveedores.css">
</head>

<body>
  <?php include './nav.php'; ?>

  <main>
    <h1>🏢 Formulario de Proveedores</h1>

    <?php if ($msg): ?>
      <div class="msg <?= strpos($msg, '✅') !== false ? 'ok' : 'error' ?>">
        <?= $msg ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
      <label>Nombre y Apellido *</label>
      <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required>

      <label>Empresa</label>
      <input type="text" name="empresa" placeholder="Ej: Exgadet S.A.">

      <label>Email *</label>
      <input type="email" name="email" placeholder="Ej: correo@empresa.com" required>

      <label>Teléfono</label>
      <input type="tel" name="telefono" placeholder="Ej: +54 9 11 5555-5555">

      <label>Locación</label>
      <input type="text" name="locacion" placeholder="Ej: CABA">

      <label>Servicio</label>
      <select name="servicio">
        <option value="">Seleccionar</option>
        <option>Equipo</option>
        <option>Accesorio</option>
        <option>Otro</option>
      </select>

      <label>Adjuntar archivo (opcional)</label>
      <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

      <label>Motivo de contacto *</label>
      <textarea name="mensaje" placeholder="Contanos en qué podemos ayudarte..." required></textarea>

      <button type="submit">Enviar formulario</button>
    </form>
  </main>
</body>

</html>