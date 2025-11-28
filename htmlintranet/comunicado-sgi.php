<?php
session_start();
require "../php/conexion.php";

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

// 🔒 Verificar sesión
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php?error=1");
  exit;
}

$legajo = $_SESSION["usuario"];

// 🔹 Obtener datos del usuario
$sql = "SELECT NOMBRE, APELLIDO, SECTOR, CARGO
        FROM USUARIOS_DATOS 
        WHERE LEGAJO = :legajo";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":legajo", $legajo);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("⚠️ No se encontró información del usuario.");
}

$nombreCompleto = trim($user["NOMBRE"] . " " . $user["APELLIDO"]);
$sectorUsuario  = trim($user["SECTOR"]);
$cargoUsuario   = trim($user["CARGO"]);

// 📁 Directorio base (SGI)
$baseDir = __DIR__ . "/../SGI";
if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);

// 📂 Carpeta: Sector-Cargo
$carpeta = $baseDir . "/" . $sectorUsuario . "-" . $cargoUsuario;
if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

// 📄 Listar archivos
$archivos = array_diff(scandir($carpeta), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Comunicados SGI</title>
  <link rel="icon" type="image/png" href="../favicon.png" sizes="32x32">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      background: #f5f5f5;
      color: #222;
    }

    main {
      padding: 120px 20px 40px;
      display: flex;
      justify-content: center;
    }

    .contenedor {
      width: 100%;
      max-width: 900px;
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      color: #2C88C8;
      margin-bottom: 15px;
    }

    /* 🔙 BOTÓN VOLVER */
    .btn-volver {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #2C88C8;
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s ease;
      text-decoration: none;
    }

    .btn-volver i {
      font-size: 16px;
    }

    .btn-volver:hover {
      background: #246da3;
    }

    .btn-volver:active {
      transform: scale(0.98);
    }

    .info {
      text-align: center;
      margin: 25px 0;
    }

    .info p {
      margin: 5px 0;
    }

    .carpeta h2 {
      color: #F9C031;
      border-bottom: 2px solid #eee;
      padding-bottom: 5px;
      margin-bottom: 15px;
    }

    /* 🔹 LISTA DE ARCHIVOS */
    .lista-archivos {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .item-archivo {
      margin: 8px 0;
      background: #f7f7f7;
      border-radius: 6px;
      padding: 12px 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: background 0.2s;
    }

    .item-archivo:hover {
      background: #eaf3ff;
    }

    .link-archivo {
      text-decoration: none;
      color: #007bff;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .link-archivo:hover {
      text-decoration: underline;
    }

    .icono {
      color: #2C88C8;
    }

    .no-archivos {
      text-align: center;
      color: #888;
      font-style: italic;
      margin-top: 20px;
    }

    /* 📱 responsive */
    @media (max-width: 768px) {
      main {
        padding: 100px 10px 30px;
      }

      .contenedor {
        padding: 20px;
      }

      .item-archivo {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
      }

      .btn-volver {
        font-size: 14px;
        padding: 8px 14px;
      }
    }

    /* 🌙 modo oscuro coherente con el nav */
    body.dark-mode {
      background: #1e1e1e;
      color: #f1f1f1;
    }

    body.dark-mode .contenedor {
      background: #2c2c2c;
      color: #f1f1f1;
    }

    body.dark-mode .item-archivo {
      background: #3a3a3a;
    }

    body.dark-mode .item-archivo:hover {
      background: #444;
    }

    body.dark-mode .link-archivo {
      color: #F9C031;
    }

    body.dark-mode .btn-volver {
      background: #F9C031;
      color: #000;
    }

    body.dark-mode .btn-volver:hover {
      background: #d9aa1c;
    }
  </style>
</head>

<body>

  <?php include './nav.php'; ?>

  <main>
    <div class="contenedor">

      <a href="./usuario.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver</a>

      <h1>📢 Comunicados SGI</h1>

      <div class="info">
        <p><strong>Empleado:</strong> <?php echo htmlspecialchars($nombreCompleto); ?></p>
        <p><strong>Sector:</strong> <?php echo htmlspecialchars($sectorUsuario); ?></p>
        <p><strong>Cargo:</strong> <?php echo htmlspecialchars($cargoUsuario); ?></p>
      </div>

      <div class="carpeta">
        <h2><i class="fa-solid fa-folder-open"></i> <?php echo htmlspecialchars($sectorUsuario . " / " . $cargoUsuario); ?></h2>

        <?php if (!empty($archivos)): ?>
          <ul class="lista-archivos">
            <?php foreach ($archivos as $archivo): ?>
              <li class="item-archivo">
                <a class="link-archivo" href="<?php echo "../SGI/" . urlencode($sectorUsuario) . "-" . urlencode($cargoUsuario) . "/" . urlencode($archivo); ?>" target="_blank">
                  <i class="fa-solid fa-file icono"></i>
                  <?php echo htmlspecialchars($archivo); ?>
                </a>
                <small><?php echo date("d/m/Y H:i", filemtime($carpeta . "/" . $archivo)); ?></small>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="no-archivos">No hay comunicados disponibles para tu cargo o sector.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

</body>

</html>