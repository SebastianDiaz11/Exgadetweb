<?php
// ==============================
// CONTADOR DÍAS SIN INCIDENCIAS
// ==============================
$sqlCont = "SELECT id, dias, fecha_ultima_actualizacion 
            FROM contador_accidentes";
$stmtCont = $conn->prepare($sqlCont);
$stmtCont->execute();
$cont = $stmtCont->fetch(PDO::FETCH_ASSOC);

$hoy = date("Y-m-d");

if ($cont) {
  $diasActuales = $cont["dias"];
  $ultima = $cont["fecha_ultima_actualizacion"];

  if ($hoy > $ultima) {
    $diff = (strtotime($hoy) - strtotime($ultima)) / 86400;
    $nuevoValor = $diasActuales + $diff;

    $update = $conn->prepare("UPDATE contador_accidentes
                              SET dias = :dias, fecha_ultima_actualizacion = :hoy
                              WHERE id = :id");
    $update->execute([
      ":dias" => $nuevoValor,
      ":hoy" => $hoy,
      ":id" => $cont["id"]
    ]);
    $diasActuales = $nuevoValor;
  }
} else {
  // Si la tabla está sin inicializar
  $conn->query("INSERT INTO contador_accidentes (dias, fecha_ultima_actualizacion)
                VALUES (0, '$hoy')");
  $diasActuales = 0;
}
?>