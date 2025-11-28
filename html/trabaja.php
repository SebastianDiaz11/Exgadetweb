<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre      = htmlspecialchars($_POST["nombre"]);
  $apellido    = htmlspecialchars($_POST["apellido"]);
  $localidad   = htmlspecialchars($_POST["localidad"]);
  $edad        = htmlspecialchars($_POST["edad"]);
  $area        = htmlspecialchars($_POST["area"]);
  $descripcion = htmlspecialchars($_POST["descripcion"]);

  $cv = $_FILES["cv"];
  $mensaje = "";

  if ($cv["error"] === UPLOAD_ERR_OK) {
    $baseDir = dirname(__DIR__) . "/CV/";
    $areaFolder = $baseDir . $area . "/";

    if (!file_exists($areaFolder)) {
      mkdir($areaFolder, 0777, true);
    }

    $nombreArchivo = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $cv["name"]);
    $rutaDestino = $areaFolder . $nombreArchivo;

    $extensionesPermitidas = ["pdf", "doc", "docx"];
    $extension = strtolower(pathinfo($cv["name"], PATHINFO_EXTENSION));

    if (in_array($extension, $extensionesPermitidas)) {
      if (move_uploaded_file($cv["tmp_name"], $rutaDestino)) {
        // ✅ Guardar los datos del formulario en JSON
        $info = [
          "nombre"      => $nombre,
          "apellido"    => $apellido,
          "localidad"   => $localidad,
          "edad"        => $edad,
          "area"        => $area,
          "descripcion" => $descripcion
        ];
        $jsonFile = $areaFolder . pathinfo($nombreArchivo, PATHINFO_FILENAME) . ".json";
        file_put_contents($jsonFile, json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $mensaje = "¡Postulación enviada correctamente! ✅ Tu CV fue almacenado en la carpeta del área " . htmlspecialchars($area) . ".";
      } else {
        $mensaje = "Error al subir el archivo ❌.";
      }
    } else {
      $mensaje = "Formato no permitido ❌. Solo PDF, DOC o DOCX.";
    }
  } else {
    $mensaje = "Por favor, suba un archivo de CV.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trabajá con nosotros - Exgadet S.A.</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="../css/trabaja.css">
</head>

<body>

  <?php include '../html/nav.php'; ?>

  <section class="trabaja-header">
    <h1>¡Sumate a nuestro equipo!</h1>
    <p>Completá el formulario y subí tu CV para postularte.</p>
  </section>

  <div class="form-container">
    <form method="POST" enctype="multipart/form-data" action="trabaja.php">
      <!-- Campos -->
      <div>
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required>
      </div>
      <div>
        <label for="apellido">Apellido *</label>
        <input type="text" id="apellido" name="apellido" required>
      </div>
      <div>
        <label for="localidad">Localidad *</label>
        <input type="text" id="localidad" name="localidad" required>
      </div>
      <div>
        <label for="edad">Edad *</label>
        <input type="number" id="edad" name="edad" required min="18">
      </div>
      <div>
        <label for="area">Área de interés *</label>
        <select id="area" name="area" required>
          <option value="" disabled selected>Seleccioná un área</option>
          <option value="Supervisor">Supervisor</option>
          <option value="Responsable">Responsable</option>
          <option value="Delegado">Delegado</option>
          <option value="PCF">PCF</option>
          <option value="Analista">Analista</option>
          <option value="Seguridad e Higiene">Seguridad e higiene</option>
          <option value="Mecanico">Mecanico</option>
          <option value="Reclamista">Reclamista</option>
          <option value="Pañolerp">Pañolero</option>
          <option value="Coordinador">Coordinador</option>
          <option value="Ayudante de inspector">Ayudante de inspector</option>
          <option value="Reclamista intervencion">Reclamista intervencion</option>
          <option value="Censista">Censista</option>
          <option value="Asistente">Asistente</option>
          <option value="Oficial">Oficial</option>
          <option value="Fusionista">Fusionista</option>
          <option value="Personal de maestranza">Personal de maestranza</option>
          <option value="Tunelero">Tunelero</option>
          <option value="Inspector">Inspector</option>
          <option value="Sereno">Sereno</option>
          <option value="Croquista">Croquista</option>
          <option value="Maquinista">Maquinista</option>
          <option value="Representante tecnico">Representante tecnico</option>
          <option value="Relevador de fuga">Relevador de fuga</option>
          <option value="Veredista">Veredista</option>
          <option value="Zanjero">Zanjero</option>
          <option value="Mantenimiento">Mantenimiento</option>
          <option value="Oficial especializado">Oficial especializado</option>
          <option value="Tecnico en higiene y seguridad">Tecnico en higiene y seguridad</option>
          <option value="Ayudante">Ayudante</option>
          <option value="Conversionista">Conversionista</option>
          <option value="Mensajeria">Mensajeria</option>
          <option value="Auditor">Auditor</option>
          <option value="Medio oficial">Medio oficial</option>
          <option value="Otros">Otros</option>
        </select>
      </div>
      <div>
        <label for="descripcion">Breve descripción (opcional)</label>
        <textarea id="descripcion" name="descripcion" placeholder="Contanos algo sobre vos..."></textarea>
      </div>
      <div>
        <label for="cv">Subí tu CV (PDF, DOC, DOCX) *</label>
        <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
      </div>
      <button type="submit">Enviar postulación</button>
    </form>

    <?php if (isset($mensaje)): ?>
      <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php endif; ?>
  </div>

  <?php include './footer.php'; ?>

  <script src="../js/menu_hambur-modo.js"></script>
</body>

</html>