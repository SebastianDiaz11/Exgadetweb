<?php
require "../php/conexion.php";
$legajo = $_SESSION["usuario"];

// ===============================
// ⚙️ Configuración inicial
// ===============================
date_default_timezone_set('America/Argentina/Buenos_Aires');
$hoy = new DateTime();

if (!$conn) {
  die("⚠️ No se pudo establecer la conexión con la base de datos.");
}

// 🔹 Datos del usuario actual desde la vista
$sql = "SELECT LEGAJO, NOMBRE, APELLIDO, SECTOR, CARGO, FECHA_INGRESO, CUMPLEANIOS
        FROM dbo.USUARIOS_DATOS
        WHERE LEGAJO = :legajo";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":legajo", $legajo);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$nombreCompleto = $usuario ? $usuario["NOMBRE"] . " " . $usuario["APELLIDO"] : "Usuario";
$sector         = $usuario ? $usuario["SECTOR"] : "Sin sector";
$cargo          = $usuario ? $usuario["CARGO"] : "Sin cargo";

// Imagen de perfil del usuario actual
$rutaUsuario = "../imagenesintranet/usuarios/{$legajo}.jpg";
$fotoPerfil = file_exists($rutaUsuario) ? $rutaUsuario : "../imagenesintranet/usuario.jpg";

/* ===============================
   FUNCIONES DE APOYO
   =============================== */
function calcularProximaOcurrencia(DateTime $hoy, $fechaStr) {
    $ts = strtotime($fechaStr);
    if ($ts === false) return null;
    $base = new DateTime(date("Y-m-d", $ts));
    $prox = new DateTime($hoy->format("Y") . "-" . $base->format("m-d"));
    if ($prox < $hoy) $prox->modify("+1 year");
    return $prox;
}

function agruparProximoDia(array $rows, string $campoFecha, DateTime $hoy): array {
    $minFecha = null;
    $grupo = [];
    foreach ($rows as $row) {
        if (empty($row[$campoFecha])) continue;
        $prox = calcularProximaOcurrencia($hoy, $row[$campoFecha]);
        if ($prox === null) continue;

        if ($minFecha === null || $prox < $minFecha) {
            $minFecha = $prox;
            $grupo = [];
            $row["PROX_FECHA"] = $prox;
            $grupo[] = $row;
        } elseif ($prox == $minFecha) {
            $row["PROX_FECHA"] = $prox;
            $grupo[] = $row;
        }
    }
    return $grupo;
}

/* ===============================
   DATOS: CUMPLEAÑOS Y ANIVERSARIOS
   =============================== */
$sqlCumple = "SELECT LEGAJO, NOMBRE, APELLIDO, SECTOR, CARGO, CUMPLEANIOS
              FROM dbo.USUARIOS_DATOS
              WHERE CUMPLEANIOS IS NOT NULL";
$stmtCumple = $conn->prepare($sqlCumple);
$stmtCumple->execute();
$cumples = $stmtCumple->fetchAll(PDO::FETCH_ASSOC);

$proximosCumples = agruparProximoDia($cumples, "CUMPLEANIOS", $hoy);

$sqlAniversario = "SELECT LEGAJO, NOMBRE, APELLIDO, SECTOR, CARGO, FECHA_INGRESO
                   FROM dbo.USUARIOS_DATOS
                   WHERE FECHA_INGRESO IS NOT NULL";
$stmtAniv = $conn->prepare($sqlAniversario);
$stmtAniv->execute();
$anivs = $stmtAniv->fetchAll(PDO::FETCH_ASSOC);

$proximosAnivs = agruparProximoDia($anivs, "FECHA_INGRESO", $hoy);

/* ===============================
   POPUP: ¿ES ANIVERSARIO HOY DEL USUARIO?
   =============================== */
$esSuAniversario = false;
if (!empty($usuario["FECHA_INGRESO"])) {
    $tsUser = strtotime($usuario["FECHA_INGRESO"]);
    if ($tsUser !== false && date("m-d", $tsUser) === $hoy->format("m-d")) {
        $esSuAniversario = true;
    }
}

/* ===============================
   NUEVOS INGRESOS DE HOY
   =============================== */
$nuevosIngresos = [];
$sqlIngresos = "SELECT LEGAJO, NOMBRE, APELLIDO, SECTOR, CARGO, FECHA_INGRESO 
                FROM dbo.USUARIOS_DATOS
                WHERE FECHA_INGRESO IS NOT NULL";
$stmtIngresos = $conn->prepare($sqlIngresos);
$stmtIngresos->execute();
$ingresos = $stmtIngresos->fetchAll(PDO::FETCH_ASSOC);

foreach ($ingresos as $ing) {
    $fechaIng = new DateTime($ing["FECHA_INGRESO"]);
    if ($fechaIng->format("Y-m-d") === $hoy->format("Y-m-d")) {
        $nuevosIngresos[] = $ing;
    }
}

/* ===============================
   POPUP: ¿ES EFECTIVO HOY? (Usuario)
   =============================== */
$esSuEfectivacion = false;

if (!empty($usuario["FECHA_INGRESO"])) {
    $fechaIng = new DateTime($usuario["FECHA_INGRESO"]);
    $fechaEfectiva = sumarDiasHabiles($fechaIng, 180);

    if ($fechaEfectiva->format("m-d") === $hoy->format("m-d")) {
        $esSuEfectivacion = true;
    }
}

/* ===============================
   EMPLEADOS EFECTIVOS (PRÓXIMO)
   =============================== */

// 🔹 Aseguramos zona horaria local
date_default_timezone_set('America/Argentina/Buenos_Aires');
$hoyEfectivo = new DateTime('today');

// 🔹 Función para sumar 180 días hábiles
function sumarDiasHabiles(DateTime $fecha, int $diasHabiles): DateTime {
    $f = clone $fecha;
    $sumados = 0;
    while ($sumados < $diasHabiles) {
        $f->modify('+1 day');
        $dia = (int)$f->format('N'); // 1=lunes ... 7=domingo
        if ($dia <= 5) $sumados++;
    }
    return $f;
}

// 🔹 Traer todos los ingresos válidos
$sqlEfectivos = "
    SELECT LEGAJO, NOMBRE, APELLIDO, SECTOR, CARGO, FECHA_INGRESO
    FROM dbo.USUARIOS_DATOS
    WHERE FECHA_INGRESO IS NOT NULL
";
$stmtEfectivos = $conn->prepare($sqlEfectivos);
$stmtEfectivos->execute();
$empleados = $stmtEfectivos->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Calcular la fecha efectiva (180 días hábiles desde ingreso)
foreach ($empleados as &$emp) {
    $fi = new DateTime(date('Y-m-d', strtotime($emp['FECHA_INGRESO'])));
    $emp['FECHA_EFECTIVA'] = sumarDiasHabiles($fi, 180);
}

// 🔹 Buscar el grupo con la próxima fecha efectiva
function agruparProximoEfectivo(array $rows, DateTime $hoyEfectivo): array {
    $minFecha = null;
    $grupo = [];
    foreach ($rows as $r) {
        if (empty($r['FECHA_EFECTIVA'])) continue;
        $fe = $r['FECHA_EFECTIVA'];

        // Evitamos errores por hora (comparar solo fecha)
        $fEfectiva = $fe->format('Y-m-d');
        $fHoy = $hoyEfectivo->format('Y-m-d');
        if ($fEfectiva < $fHoy) continue;

        if ($minFecha === null || $fEfectiva < $minFecha->format('Y-m-d')) {
            $minFecha = clone $fe;
            $grupo = [$r];
        } elseif ($fEfectiva === $minFecha->format('Y-m-d')) {
            $grupo[] = $r;
        }
    }
    return $grupo;
}

$proximosEfectivos = agruparProximoEfectivo($empleados, $hoyEfectivo);

// 🔹 Debug temporal (BORRAR luego)
if (empty($proximosEfectivos)) {
    // echo "<!-- ❌ No hay próximos efectivos - hoy {$hoyEfectivo->format('d/m/Y')} -->";
} else {
    // echo "<!-- ✅ Próximos efectivos encontrados: " . count($proximosEfectivos) . " -->";
}


/* ===============================
   POPUP: ¿ES CUMPLEAÑOS HOY DEL USUARIO?
   =============================== */
$esSuCumpleanios = false;
if (!empty($usuario["CUMPLEANIOS"])) {
    $tsUser = strtotime($usuario["CUMPLEANIOS"]);
    if ($tsUser !== false && date("m-d", $tsUser) === $hoy->format("m-d")) {
        $esSuCumpleanios = true;
    }
}

/* ===============================
   CUMPLEAÑOS DE HOY
   =============================== */
$cumplesHoy = [];
foreach ($cumples as $c) {
    if (empty($c["CUMPLEANIOS"])) continue;
    $ts = strtotime($c["CUMPLEANIOS"]);
    if ($ts !== false && date("m-d", $ts) === $hoy->format("m-d")) {
        $cumplesHoy[] = $c;
    }
}

/* ===============================
   ANIVERSARIOS DE HOY
   =============================== */
$anivsHoy = [];
foreach ($anivs as $a) {
    if (empty($a["FECHA_INGRESO"])) continue;
    $ts = strtotime($a["FECHA_INGRESO"]);
    if ($ts !== false && date("m-d", $ts) === $hoy->format("m-d")) {
        $anivsHoy[] = $a;
    }
}

/* ===============================
   NOTICIAS RRHH
   =============================== */
$sqlNoticias = "
    SELECT ID_NOTICIA, TITULO, CUERPO, FECHA_CREACION, IMAGEN
    FROM M900_NOTICIAS
    WHERE ACTIVA = 1
    ORDER BY FECHA_CREACION DESC
";
$stmtNoticias = $conn->prepare($sqlNoticias);
$stmtNoticias->execute();
$noticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   NOTICIAS SGI
   =============================== */
$sqlNoticiasSGI = "
    SELECT ID_NOTICIA, TITULO, CUERPO, FECHA_CREACION, IMAGEN
    FROM M901NOTICIAS_SGI
    ORDER BY FECHA_CREACION DESC
";
$stmtNoticiasSGI = $conn->prepare($sqlNoticiasSGI);
$stmtNoticiasSGI->execute();
$noticiasSGI = $stmtNoticiasSGI->fetchAll(PDO::FETCH_ASSOC);
?>
