<?php
// 🔹 Traer flota desde la BD
$sql = "SELECT * FROM CREAR_SERVICIO_OBRA_FLOTA WHERE CATEGORIA = 'Flota' ORDER BY FECHA_CREACION DESC";
$stmt = $conn->query($sql);
$flotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Carpeta de imágenes
$DIR = "../imagenes/obras/";

// 🔹 Función para listar imágenes principales y secundarias
function listarImagenes($DIR, $categoria)
{
  $imagenes = glob($DIR . "{$categoria}*.{jpg,jpeg,png}", GLOB_BRACE);
  natsort($imagenes);

  $principales = [];
  $secundarias = [];

  foreach ($imagenes as $img) {
    $base = pathinfo($img, PATHINFO_FILENAME);
    if (preg_match('/-\d+$/', $base)) {
      $secundarias[] = $img;
    } else {
      $principales[] = $img;
    }
  }
  return ["principales" => $principales, "secundarias" => $secundarias];
}

// 🔹 Buscar imágenes de flota
$imgs = listarImagenes($DIR, "flota");
$principales = $imgs["principales"];
$secundarias = $imgs["secundarias"];
?>