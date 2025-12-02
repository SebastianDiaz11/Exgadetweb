<?php
// 🔹 Traer obras desde la BD
$sql = "SELECT * FROM CREAR_SERVICIO_OBRA_FLOTA WHERE CATEGORIA = 'Obra' ORDER BY FECHA_CREACION DESC";
$stmt = $conn->query($sql);
$obrasDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Carpeta de imágenes
$DIR = "../imagenes/obras/";

// 🧩 Función para listar imágenes principales (obra, obra2, etc.)
function listarPrincipales($DIR)
{
  $patterns = [$DIR . "obra*.{jpg,jpeg,png}"];
  $matches = [];
  foreach ($patterns as $p) {
    $found = glob($p, GLOB_BRACE);
    foreach ($found as $f) {
      $base = pathinfo($f, PATHINFO_FILENAME);
      if (!preg_match('/-\d+$/', $base)) {
        $matches[] = $f;
      }
    }
  }
  natsort($matches);
  return array_values($matches);
}

// 🧩 Función para listar imágenes secundarias (obra-1, obra2-1, etc.)
function listarSecundarias($DIR, $principal)
{
  $base = pathinfo($principal, PATHINFO_FILENAME);
  $found = glob($DIR . "{$base}-*.{jpg,jpeg,png}", GLOB_BRACE);
  natsort($found);
  return $found;
}

$principales = listarPrincipales($DIR);

// 🧩 Emparejar obras de la base con imágenes
$cardsDinamicas = [];
foreach ($obrasDB as $idx => $obra) {
  $principal = $principales[$idx] ?? "";
  $secundarias = $principal ? listarSecundarias($DIR, $principal) : [];
  $cardsDinamicas[] = [
    "titulo" => $obra["TITULO"],
    "cuerpo" => $obra["CUERPO"],
    "comitente" => $obra["COMITENTE"],
    "inicio" => $obra["FECHA_INICIO"],
    "final" => $obra["FECHA_FINAL"],
    "imgPrincipal" => $principal,
    "imgsSecundarias" => $secundarias,
  ];
}
?>