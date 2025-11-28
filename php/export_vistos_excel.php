<?php
require "../php/conexion.php";

// Obtener datos de mails vistos + info del trabajador
$sql = "
    SELECT 
        rv.mail_uid,
        rv.fecha_visto,
        rv.trabajador_legajo,
        u.NOMBRE,
        u.APELLIDO
    FROM reclamos_vistos rv
    LEFT JOIN USUARIOS_DATOS u
        ON rv.trabajador_legajo = u.LEGAJO
    ORDER BY rv.fecha_visto DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$vistos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre del archivo
$filename = "mails_vistos_" . date("Ymd_His") . ".xls";

// Headers Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");

echo "<table border='1'>";
echo "<tr>
        <th>ID Mail</th>
        <th>Fecha Visto</th>
        <th>Legajo</th>
        <th>Nombre</th>
        <th>Apellido</th>
      </tr>";

foreach ($vistos as $v) {
    echo "<tr>
            <td>{$v['mail_uid']}</td>
            <td>{$v['fecha_visto']}</td>
            <td>{$v['trabajador_legajo']}</td>
            <td>{$v['NOMBRE']}</td>
            <td>{$v['APELLIDO']}</td>
          </tr>";
}

echo "</table>";
exit;
?>
