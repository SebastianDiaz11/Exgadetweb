<?php
require "../php/conexion.php";

// 🔥 NO imprimir NADA antes de estos headers
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=respuestas_reclamos.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Evitar BOM oculto
ob_clean();

// Consulta
$sql = "SELECT id, trabajador_legajo, trabajador_nombre, email_cliente, asunto, mensaje_enviado, fecha
        FROM respuestas_reclamos
        ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tabla HTML válida
echo "<html><meta charset='UTF-8'><body>";
echo "<table border='1' cellspacing='0' cellpadding='4'>";

echo "<tr>
        <th>ID</th>
        <th>Legajo Trabajador</th>
        <th>Nombre Trabajador</th>
        <th>Email Cliente</th>
        <th>Asunto</th>
        <th>Respuesta</th>
        <th>Fecha</th>
      </tr>";

foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['trabajador_legajo']}</td>";
    echo "<td>".htmlspecialchars($row['trabajador_nombre'])."</td>";
    echo "<td>{$row['email_cliente']}</td>";
    echo "<td>".htmlspecialchars($row['asunto'])."</td>";
    echo "<td>".htmlspecialchars($row['mensaje_enviado'])."</td>";
    echo "<td>{$row['fecha']}</td>";
    echo "</tr>";
}

echo "</table>";
echo "</body></html>";
exit;


