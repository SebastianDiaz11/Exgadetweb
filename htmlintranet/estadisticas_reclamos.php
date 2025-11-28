<?php
session_start();
require "../php/conexion.php";

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

// Validar sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php?error=1");
    exit();
}

// =========================
// Filtros de fecha (entradas del usuario)
// =========================
$desde = $_GET["desde"] ?? date("Y-m-01");
$hasta = $_GET["hasta"] ?? date("Y-m-d");

// =========================
// Normalizar fechas — evitar errores SQL
// =========================
function safeDate($d) {
    try {
        return (new DateTime($d))->format("Y-m-d");
    } catch (Exception $e) {
        return date("Y-m-d"); // fallback seguro
    }
}

$desde = safeDate($desde);
$hasta = safeDate($hasta);

// =========================
// DATOS: VISTOS POR DÍA
// =========================
$sqlVistos = "
SELECT 
    LEFT(LTRIM(RTRIM(fecha_visto)), 10) AS dia,
    COUNT(*) AS total
FROM reclamos_vistos
WHERE 
    LTRIM(RTRIM(fecha_visto)) LIKE '[0-9][0-9][0-9][0-9]-%'
    AND LEFT(LTRIM(RTRIM(fecha_visto)), 10) >= :d
    AND LEFT(LTRIM(RTRIM(fecha_visto)), 10) < DATEADD(day, 1, :h)
GROUP BY LEFT(LTRIM(RTRIM(fecha_visto)), 10)
ORDER BY dia
";

$stmt = $conn->prepare($sqlVistos);
$stmt->execute([":d" => $desde, ":h" => $hasta]);
$vistos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================
// DATOS: RESPUESTAS POR DÍA
// =========================
$sqlResp = "
SELECT 
    LEFT(LTRIM(RTRIM(fecha)), 10) AS dia,
    COUNT(*) AS total
FROM respuestas_reclamos
WHERE 
    LTRIM(RTRIM(fecha)) LIKE '[0-9][0-9][0-9][0-9]-%'
    AND LEFT(LTRIM(RTRIM(fecha)), 10) >= :d
    AND LEFT(LTRIM(RTRIM(fecha)), 10) < DATEADD(day, 1, :h)
GROUP BY LEFT(LTRIM(RTRIM(fecha)), 10)
ORDER BY dia

";

$stmt2 = $conn->prepare($sqlResp);
$stmt2->execute([":d" => $desde, ":h" => $hasta]);
$respuestas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para Chart.js
function toChartArray($rows) {
    $labels = [];
    $values = [];
    foreach ($rows as $r) {
        $labels[] = $r["dia"];
        $values[] = $r["total"];
    }
    return [$labels, $values];
}

[$labV, $valV] = toChartArray($vistos);
[$labR, $valR] = toChartArray($respuestas);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Estadístico</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="p-4">

<h2 class="mb-4">
    📊 Panel Estadístico de Reclamos
</h2>

<!-- ============================
 FILTROS
============================= -->
<form method="GET" class="row mb-4">
    <div class="col-md-3">
        <label>Desde:</label>
        <input type="date" name="desde" class="form-control" value="<?= $desde ?>">
    </div>
    <div class="col-md-3">
        <label>Hasta:</label>
        <input type="date" name="hasta" class="form-control" value="<?= $hasta ?>">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100">Aplicar Filtros</button>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <a href="reclamos.php" class="btn btn-secondary w-100">Volver</a>
    </div>
</form>

<!-- ============================
 TARJETAS RESUMEN
============================= -->
<div class="row mb-4">

    <div class="col-md-4">
        <div class="p-3 bg-warning text-dark rounded shadow">
            <h4>👁️ Total Vistos</h4>
            <h2><?= array_sum($valV) ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 bg-success text-white rounded shadow">
            <h4>✉️ Total Respondidos</h4>
            <h2><?= array_sum($valR) ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 bg-info text-white rounded shadow">
            <h4>⚡ Eficiencia (% respondidos / vistos)</h4>
            <h2>
                <?= (array_sum($valV) > 0) ? round(array_sum($valR) * 100 / array_sum($valV), 1) : 0 ?>%
            </h2>
        </div>
    </div>

</div>

<!-- ============================
 GRAFICO LINEAL
============================= -->
<div class="card p-4 shadow">
    <h4>📈 Evolución diaria</h4>
    <canvas id="grafico"></canvas>
</div>

<script>
const ctx = document.getElementById('grafico');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labV) ?>,
        datasets: [
            {
                label: 'Vistos',
                data: <?= json_encode($valV) ?>,
                borderColor: 'orange',
                borderWidth: 2
            },
            {
                label: 'Respondidos',
                data: <?= json_encode($valR) ?>,
                borderColor: 'green',
                borderWidth: 2
            }
        ]
    }
});
</script>

</body>
</html>

