<?php
// 1) Traer servicios desde la BD
$sql = "SELECT ID, EMOJI, CATEGORIA, TITULO, CUERPO, COMITENTE, FECHA_INICIO, FECHA_FINAL, FECHA_CREACION
        FROM CREAR_SERVICIO_OBRA_FLOTA
        WHERE CATEGORIA = 'Servicio'
        ORDER BY FECHA_CREACION DESC, ID DESC";
$stmt = $conn->query($sql);
$serviciosDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Escanear imágenes en /imagenes/obras/ (servicio y servicios)
$DIR = "../imagenes/obras/";

// Helper: listado de imágenes principales (sin "-n")
function listarPrincipales($DIR) {
  $patterns = [
    $DIR . "servicio*.{jpg,jpeg,png}",
    $DIR . "servicios*.{jpg,jpeg,png}",
  ];
  $matches = [];
  foreach ($patterns as $p) {
    $found = glob($p, GLOB_BRACE);
    foreach ($found as $f) {
      $base = pathinfo($f, PATHINFO_FILENAME);
      // filtrar las que NO tienen "-n" al final
      if (!preg_match('/-\d+$/', $base)) {
        $matches[] = $f;
      }
    }
  }
  // orden natural por nombre (servicio, servicio2, servicios3, etc.)
  natsort($matches);
  return array_values($matches);
}

// Helper: secundarias para una principal (base + -n)
function listarSecundarias($DIR, $principalPath) {
  $base = pathinfo($principalPath, PATHINFO_FILENAME); // ej: servicio2
  $secPatterns = [
    $DIR . $base . "-*.{jpg,jpeg,png}"
  ];
  $sec = [];
  foreach ($secPatterns as $p) {
    $found = glob($p, GLOB_BRACE);
    if ($found) {
      natsort($found);
      foreach ($found as $f) $sec[] = $f;
    }
  }
  return $sec;
}

$principales = listarPrincipales($DIR);

// 3) Emparejar N servicios con N imágenes principales (si hay menos imágenes, se usa un placeholder)
$cardsDinamicas = [];
$placeholder = $DIR . "default.jpg"; // opcional (crealo si querés)
foreach ($serviciosDB as $idx => $svc) {
  $principal = $principales[$idx] ?? (file_exists($placeholder) ? $placeholder : "");
  $secundarias = $principal ? listarSecundarias($DIR, $principal) : [];

  $cardsDinamicas[] = [
    "emoji" => $svc["EMOJI"], // en Servicio se permite emoji
    "titulo" => $svc["TITULO"],
    "cuerpo" => $svc["CUERPO"],
    "comitente" => $svc["COMITENTE"],
    "imgPrincipal" => $principal,
    "imgsSecundarias" => $secundarias,
  ];
}
?>