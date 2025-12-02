<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php?error=1");
    exit();
}

require "../php/conexion.php";

$empleados = [
    //ALMACENES
    ["", "", "ALMACENES", "", "", ""],
    ["8087", "MAURO SEBASTIAN RODOLFO", "ALMACENES", "JEFE", "1120275055", "smauro@exgadetsa.com.ar"],
    ["8803", "RAMA PABLO SEBASTIAN", "ALMACENES", "COORDINADOR", "1136352642", "prama@exgadetsa.com.ar"],
    ["5821", "SINGH ALEXIS FABIAN", "ALMACENES", "COORDINADOR", "1136494292", "asingh@exgadetsa.com.ar"],
    ["2362", "ARENA LEONARDO DAVID", "ALMACENES", "PAÑOLERO", "1162674209", ""],
    ["360", "CASTELLI GUSTAVO MARTIN", "ALMACENES", "PAÑOLERO", "", ""],
    ["1798", "GUERRERO CRISTIAN ARIEL", "ALMACENES", "PAÑOLERO", "", ""],
    ["2409", "GONZALEZ FACUNDO DANIEL", "ALMACENES", "PAÑOLERO", "", ""],
    ["1552", "LOPEZ JUAN JOSE", "ALMACENES", "PAÑOLERO", "", "jjlopez@exgadetsa.com.ar"],
    ["7225", "UCEDO BRIAN ENRIQUE", "ALMACENES", "PAÑOLERO", "", ""],
    ["6060", "PELENDIR ABEL ARIEL", "ALMACENES", "PAÑOLERO", "", ""],
    ["4901", "CASTIÑEIRA GONAZALO HUGO", "ALMACENES", "PAÑOLERO", "", ""],

    //COMPRAS
    ["", "", "COMPRAS", "", "", ""],
    ["46", "BONCOR MARIANO ANDRES", "COMPRAS", "JEFE", "1126086975", "mboncor@exgadetsa.com.ar"],
    ["7678", "DIAZ CAROLINA SALOME", "COMPRAS", "ANALISTA", "1138738430", "csdiaz@exgadetsa.com.ar"],
    ["5013", "HERRERA NICOLAS EMANUEL", "COMPRAS", "ANALISTA", "1162676676", "nherrera@exgadetsa.com.ar"],
    ["764", "TICONA NADIA MELINA", "COMPRAS", "ANALISTA", "1136184072", "nticona@exgadetsa.com.ar"],
    ["3491", "ANTELO FLORENCIA AGUSTINA", "COMPRAS", "ANALISTA", "1141419887", "fantelo@exgadetsa.com.ar"],

    //GERENCIA
    ["6390", "SIMONI RICARDO NESTOR", "GERENCIA", "PRESIDENTE", "", "rsimoni@exgadetsa.com.ar"],
    ["732", "SIMONI MATIAS", "GERENCIA", "VICEPRESIDENTE", "", "msimoni@exgadetsa.com.ar"],

    //GESTION DE FLOTA
    ["", "", "GESTIÓN DE FLOTA", "", "", ""],
    ["7903", "VALDEZ CRISTIAN JOSE", "GESTIÓN DE FLOTA", "JEFE", "1165839670", "cvaldez@exgadetsa.com.ar"],
    ["6892", "LOPEZ AMAYA GONZALO MANUEL", "GESTIÓN DE FLOTA", "ANALISTA", "1169091368", "gmlopez@exgadetsa.com.ar"],
    ["4916", "MONGE JULIAN ANDRES", "GESTIÓN DE FLOTA", "ASISTENTE", "1165840584", "jmonge@exgadetsa.com.ar"],
    ["3689", "ZERDA YANET DALILA", "GESTIÓN DE FLOTA", "ASISTENTE", "1169931759", "yzerda@exgadetsa.com.ar"],
    ["2961", "LOPEZ ARZAMENDIA CHRISTIAN", "GESTIÓN DE FLOTA", "MECANICO", "", ""],
    ["395", "GIMENEZ SANTIAGO ANDRES", "GESTIÓN DE FLOTA", "MECANICO", "", ""],
    ["5986", "MATA ADOLFO RAFAEL", "GESTIÓN DE FLOTA", "MECANICO", "", ""],

    //MANTENIMIENTO
    ["", "", "MANTENIMIENTO", "", "", ""],
    ["3994", "VALENCIA JUAN CARLOS", "MANTENIMIENTO", "SUPERVISOR", "1167664232", "jvalencia@exgadetsa.com.ar"],
    ["5814", "Almada Alvarez Ariel Alberto", "MANTENIMIENTO", "AYUDANTE", "", ""],
    ["1913", "Bustamante Raul Antonio", "MANTENIMIENTO", "AYUDANTE", "", ""],
    ["5120", "Pucheta Luis", "MANTENIMIENTO", "MAQUINISTA", "", ""],
    ["3883", "Tules Liliana Beatriz", "MANTENIMIENTO", "PERSONAL DE MAESTRANZA", "", ""],

    //PROYECTOS Y OBRA
    ["", "", "PROYECTOS Y OBRA", "", "", ""],
    ["7969", "ANTONUCCI LUIS ELIAS", "PROYECTOS Y OBRA", "JEFE", "1138609441", "eantonucci@exgadetsa.com.ar"],
    //RESPONSABLE DEL SERCTOR
    ["8273", "MIERES NATALIA SOLEDAD", "PROYECTOS Y OBRA", "RESPONSABLE DE OFICINA TÉCNICA", "1122813332", "nmieres@exgadetsa.com.ar"],
    ["5519", "FLORES APAZA ALVARO OMAR", "PROYECTOS Y OBRA", "ANALISTA", "1136688687", "aflores@exgadetsa.com.ar"],
    ["5019", "BLANCO MALENA", "PROYECTOS Y OBRA", "ANALISTA", "1159492810", "mblanco@exgadetsa.com.ar"],
    ["7713", "FERNANDEZ DAIANA BELEN", "PROYECTOS Y OBRA", "CADISTA", "1162676652", "dbfernandez@exgadetsa.com.ar"],
    ["3360", "LEDESMA LUCIANA BELEN", "PROYECTOS Y OBRA", "ANALISTA", "1139272100", "lledesma@exgadetsa.com.ar"],
    ["6502", "NATALE AGOSTINA", "PROYECTOS Y OBRA", "ANALISTA", "1162674226", "anatale@exgadetsa.com.ar"],
    //RESPONSABLE DEL SECTOR
    ["1613", "JULIANO NILO SEBASTIAN", "PROYECTOS Y OBRA", "RESPONSABLE DE OBRA", "1126087010", "njuliano@exgadetsa.com.ar"],
    ["9904", "RUSSO AGUSTIN ANDRES", "PROYECTOS Y OBRA", "SUPERVISOR", "1162676457", "arusso@exgadetsa.com.ar"],
    ["2867", "BALADO JUAN MANUEL", "PROYECTOS Y OBRA", "SUPERVISOR", "1162674230", ""],
    ["1757", "BARRERA MACCHIONE MATEO", "PROYECTOS Y OBRA", "SUPERVISOR", "1161812012", "mbarrera@exgadetsa.com.ar"],
    ["5178", "SMALDONE FERNANDO ADRIAN", "PROYECTOS Y OBRA", "CROQUISTA", "1135752431", "fsmaldone@exgadetsa.com.ar"],

    //RESPONSABLE COMERCIAL
    ["", "", "RESPONSABLE COMERCIAL", "", "", ""],
    ["2376", "PALAZZI JUAN CARLOS", "RESPONSABLE COMERCIAL", "RESPONSABLE COMERCIAL", "1151836279", "jcpalazzi@exgadetsa.com.ar"],

    //RRHH
    ["", "", "RECURSOS HUMANOS", "", "", ""],
    ["4496", "CAMPESI ROMINA ANALIA", "RECURSOS HUMANOS", "JEFE", "1126086943", "rcampesi@exgadetsa.com.ar"],
    ["9361", "DUARTE IZIAR ITATI", "RECURSOS HUMANOS", "COORDINADOR", "1151845418", "iduarte@exgadetsa.com.ar"],
    ["4578", "CRISTALDO CANDELARIA", "RECURSOS HUMANOS", "ANALISTA", "1136878115", "ccristaldo@exgadetsa.com.ar"],
    ["9038", "FORTUNATO EVELYN ALDANA", "RECURSOS HUMANOS", "ANALISTA", "1128377150", "efortunato@exgadetsa.com.ar"],
    ["212", "GHEDIN MARIA BELEN", "RECURSOS HUMANOS", "ANALISTA", "1167667798", "bghedin@exgadetsa.com.ar"],
    ["2788", "PADIN AGUSTINA", "RECURSOS HUMANOS", "ANALISTA", "1139196132", "apadin@exgadetsa.com.ar"],
    ["9119", "TOMÉ RIVERO VALERIA", "RECURSOS HUMANOS", "ANALISTA", "1166335132", "vrivero@exgadetsa.com.ar"],

    //SISTEMA DE GESTION INTEGRADO
    ["", "", "SGI", "", "", ""],
    ["7374", "FONSFRIA DANIELA ALEJANDRA", "SGI", "JEFE", "1161777315", "dfonsfria@exgadetsa.com.ar"],
    ["38", "CESPEDES MELINA ANAHI", "SGI", "TECNICA EN HIGIENE Y SEGURIDAD", "1157131186", "mcespedes@exgadetsa.com.ar"],
    ["1026", "VIGNAUD MILAGROS GIULIANA", "SGI", "ANALISTA", "1131778519", "mvignaud@exgadetsa.com.ar"],
    ["7749", "ALVAREZ PABLO RUBEN", "SGI", "AUDITOR", "1162676738", "palvarez@exgadetsa.com.ar"],
    ["414", "ROZENMUTER LEONARDO", "SGI", "AUDITOR", "1162676595", "lrozenmuter@exgadetsa.com.ar"],

    //SISTEMAS
    ["", "", "SISTEMAS", "", "", ""],
    ["6710", "LESIW MAXIMILIANO HORACIO", "SISTEMAS", "JEFE", "1122610599", "mlesiw@exgadetsa.com.ar"],
    ["9015", "DIAZ SEBASTIAN ANGEL", "SISTEMAS", "DESARROLLADOR FULL STACK", "1169444825", "sdiaz@exgadetsa.com.ar"],
    ["6269", "PIÑEYRO CABALLERO BRIAN", "SISTEMAS", "DESARROLLADOR FULL STACK", "1161813998", "bpineyro@exgadetsa.com.ar"],
    ["8466", "VERGARA JORGE DANIEL", "SISTEMAS", "SOPORTE TÉCNICO IT", "1149372272", "dvergara@exgadetsa.com.ar"],

    //ADMINISTRACION Y FINANZAS
    ["", "", "ADMINISTRACIÓN Y FINANZAS", "", "", ""],
    ["2015", "LOPEZ HERNAN LEANDRO", "ADMINISTRACIÓN Y FINANZAS", "JEFE", "1162676616", "hlopez@exgadetsa.com.ar"],
    ["3881", "CEJAS FLAVIA YANINA ", "ADMINISTRACIÓN Y FINANZAS", "ANALISTA CONTROL FINANCIERO", "1131978040", "fcejas@exgadetsa.com.ar"],
    ["740", "MAROTTA CAROLINA CECILIA", "ADMINISTRACIÓN Y FINANZAS", "ANALISTA", "", "cmarotta@exgadetsa.com.ar"],
    ["6763", "SCHNEIDER NAYLA MICAELA", "ADMINISTRACIÓN Y FINANZAS", "ANALISTA", "", "nschneider@exgadetsa.com.ar"],

    //SISTEMA DE GESTION INTEGRADO
    ["", "", "SGI", "", "", ""],
    ["7374", "FONSFRIA DANIELA ALEJANDRA", "SGI", "JEFE", "1161777315", "dfonsfria@exgadetsa.com.ar"],
    ["38", "CESPEDES MELINA ANAHI", "SGI", "TECNICA EN HIGIENE Y SEGURIDAD", "1157131186", "mcespedes@exgadetsa.com.ar"],
    ["1026", "VIGNAUD MILAGROS GIULIANA", "SGI", "ANALISTA", "1131778519", "mvignaud@exgadetsa.com.ar"],
    ["7749", "ALVAREZ PABLO RUBEN", "SGI", "AUDITOR", "1162676738", "palvarez@exgadetsa.com.ar"],
    ["414", "ROZENMUTER LEONARDO", "SGI", "AUDITOR", "1162676595", "lrozenmuter@exgadetsa.com.ar"],

    //OPERACIONES
    ["", "", "OPERACIONES", "", "", ""],
    ["1231", "FERNANDEZ JUAN CARLOS", "OPERACIONES", "JEFE", "1162674442", "jfernandez@exgadetsa.com.ar"],
    ["3349", "MAROTTA REVELLO HUGO NESTOR", "OPERACIONES", "SUPERVISOR", "1161618897", "hmarotta@exgadetsa.com.ar"],
    ["9320", "PISERA CESAR PABLO", "OPERACIONES", "SUPERVISOR", "1135603135", "cpisera@exgadetsa.com.ar"],
    ["737", "RODRIGUEZ VICTOR RODOLFO", "OPERACIONES", "SUPERVISOR", "1159796601", "vrodriguez@exgadetsa.com.ar"],
    ["444", "MARTINELLI MARIANO FABIAN", "OPERACIONES", "RESPONSABLE TÉCNICO", "1132034527", "mmartinelli@exgadetsa.com.ar"],
    ["3464", "VARAONA CANDELA ALDANA", "OPERACIONES", "ASISTENTE GESTION DOCUMENTAL TECNICA", "1165842653", "cvaraona@exgadetsa.com.ar"],

    //ACOMETIDAS
    ["", "", "ACOMETIDAS", "", "", ""],
    ["2781", "RIBEIRO ROBERTO EZEQUIEL", "ACOMETIDAS", "JEFE", "1149948154", "eribeiro@exgadetsa.com.ar"],
    ["6979", "ROJAS TOMAS AUGUSTO", "ACOMETIDAS", "SUPERVISOR", "1136887759", "trojas@exgadetsa.com.ar"],
    ["5663", "MONTENEGRO DIEGO NAHUEL", "ACOMETIDAS", "SUPERVISOR", "1126087005", "dmontenegro@exgadetsa.com.ar"],

    //Contrato de Operaciones Masivas
    ["", "", "Contrato de Operaciones Masivas", "", "", ""],
    ["6264", "BARNEAU JUAN JOSE", "Contrato de Operaciones Masivas", "JEFE", "1162676659", "jbarneau@exgadetsa.com.ar"],
    ["3838", "SOSA DANIEL MARCELO", "Contrato de Operaciones Masivas", "RESPONSABLE OPERACIONES MASIVAS", "1159886370", "dsosa@exgadetsa.com.ar"],
    ["56", "RODRIGUEZ TOMAS AYRTON", "Contrato de Operaciones Masivas", "SUPERVISOR", "1162674373", "trodriguez@exgadetsa.com.ar"],
    ["5510", "RUIZ GUILLERMO ANTONIO", "Contrato de Operaciones Masivas", "SUPERVISOR", "1138587821", "gruiz@exgadetsa.com.ar"],

    //PLANIFICACION DE RECORRIDOS
    ["", "", "PLANIFICACIÓN DE RECORRIDOS", "", "", ""],
    ["4021", "VILLANUEVA HERNAN DARIO", "PLANIFICACIÓN DE RECORRIDOS", "SUPERVISOR", "1152269164", "hvillanueva@exgadetsa.com.ar"],
    ["5775", "HANGLIN AGUIRRE LUDMILA", "PLANIFICACIÓN DE RECORRIDOS", "ASISTENTE", "1162676371", "lhanglin@exgadetsa.com.ar"],
    ["3427", "SILVA MARIANA PAOLA", "PLANIFICACIÓN DE RECORRIDOS", "ASISTENTE", "1165842514", "msilva@exgadetsa.com.ar"],
    ["1370", "RODRIGUEZ JORGE OMAR", "PLANIFICACIÓN DE RECORRIDOS", "ANALISTA", "1162676466", "jrodriguez@exgadetsa.com.ar"],
    ["7933", "LOPEZ SERGIO DANIEL", "PLANIFICACIÓN DE RECORRIDOS", "ANALISTA", "1151608808", "slopez@exgadetsa.com.ar"],
    ["2413", "SCILLAMA BRUNO EZEQUIEL", "PLANIFICACIÓN DE RECORRIDOS", "ASISTENTE", "1139510617", "bscillama@exgadetsa.com.ar"],

    //MESA DE AYUDA
    ["", "", "MESA DE AYUDA", "", "", ""],
    ["5269", "MONTANARI MATIAS ARIEL", "MESA DE AYUDA", "SUPERVISOR", "1131334484", "mmontanari@exgadetsa.com.ar"],
    ["3505", "GARRO LILIANA SILVINA", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1136888024", "sgarro@exgadetsa.com.ar"],
    ["3893", "GUZMAN AYLEN YAZMIN", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1136888158", "aguzman@exgadetsa.com.ar"],
    ["6969", "VILLALBA SONIA GRACIELA", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1153042539", "svillalba@exgadetsa.com.ar"],
    ["2868", "BAREIRO LEILA VICTORIA", "MESA DE AYUDA", "CONTROL", "1162676563", "lbareiro@exgadetsa.com.ar"],
    ["8204", "CUPOLO BARBARA", "MESA DE AYUDA", "GESTION DE TRAMITE", "1165840562", "bcupolo@exgadetsa.com.ar"],
    ["4848", "PUGLIESE FEDERICO DAMIAN", "MESA DE AYUDA", "GESTION DE TRAMITE", "1159563342", "fpugliese@exgadetsa.com.ar"],
    ["6931", "QUINTANA SILVA DAMIAN WASHINGTON", "MESA DE AYUDA", "CONTROL", "", "dquintana@exgadetsa.com.ar"],
    ["7224", "SEGOVIA WANDA ANAHI", "MESA DE AYUDA", "CONTROL", "1138719167", "wsegovia@exgadetsa.com.ar"],
    ["7150", "SERRA MELANIE AGOSTINA", "MESA DE AYUDA", "GESTION DE TRAMITE", "1128598338", "mserra@exgadetsa.com.ar"],
    ["2326", "VILLANUEVA RUBEN DARIO", "MESA DE AYUDA", "GESTIÓN DE TRÁMITE", "1141878956", "rvillanueva@exgadetsa.com.ar"],
    ["2262", "GARCIA JUAN CRUZ", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1123228958", "jgarcia@exgadetsa.com.ar"],
    ["9369", "DEGO NICOLE", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1139532112", "ndego@exgadetsa.com.ar"],
    ["9480", "DOMINGIGUEZ FABIAN IGNACIO", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1138440913", "fdominguez@exgadetsa.com.ar"],
    ["9062", "RAMIREZ ROBERTO OSCAR", "MESA DE AYUDA", "ATENCION AL CLIENTE", "1135569283", "rramirez@exgadetsa.com.ar"],
];

/* ============================================
   FUNCIONES AUXILIARES
   ============================================ */

function foto($legajo)
{
    if (!ctype_digit($legajo)) return "../imagenesintranet/usuario.jpg";
    $path = "../imagenesintranet/usuarios/" . $legajo . ".jpg";
    return file_exists($path) ? $path : "../imagenesintranet/usuario.jpg";
}

function nivelCargo($cargo)
{
    $c = mb_strtoupper(trim($cargo), "UTF-8");
    if (strpos($c, "RESPONSABLE") !== false) return "responsable";
    if (strpos($c, "SUPERVISOR") !== false) return "supervisor";
    if (strpos($c, "COORDINADOR") !== false) return "coordinador";
    return "base";
}

function esSectorCabecera($emp)
{
    return empty(trim($emp[0])) &&
        empty(trim($emp[1])) &&
        !empty(trim($emp[2])) &&
        empty(trim($emp[3])) &&
        empty(trim($emp[4])) &&
        empty(trim($emp[5]));
}

/* ============================================
   SECTORES CABECERA
   ============================================ */
$sectoresCabecera = [];
foreach ($empleados as $e) {
    if (esSectorCabecera($e)) {
        $sectoresCabecera[] = trim($e[2]);
    }
}

/* ============================================
   AGRUPAR POR SECTOR
   ============================================ */
$sectores = [];
foreach ($empleados as $e) {
    $sec = trim($e[2]) ?: "SIN SECTOR";
    if (!isset($sectores[$sec])) $sectores[$sec] = [];
    $sectores[$sec][] = $e;
}

/* ============================================
   SUBSECTORES QUE VAN DENTRO DE OPERACIONES
   ============================================ */
$subsectoresOperaciones = [
    "OPERACIONES PROPIO",             // 🔥 nuevo botón propio
    "ACOMETIDAS",
    "Contrato de Operaciones Masivas",
    "PLANIFICACIÓN DE RECORRIDOS",
    "MESA DE AYUDA"
];

/* ============================================
   ESTRUCTURA DEL ORGANIGRAMA
   ============================================ */
$estructura = [];
$jefeOperaciones = null;

/* ============================================
   ARMAR ORGANIGRAMA COMPLETO
   ============================================ */

foreach ($sectores as $sector => $lista) {

    /* ---------------------------------------
   1) SI ES OPERACIONES → JEFE PRINCIPAL
   --------------------------------------- */
    if ($sector === "OPERACIONES") {

        // Buscar jefe de OPERACIONES
        $jefe = null;
        foreach ($lista as $emp) {
            if (mb_strtoupper($emp[3]) === "JEFE") {
                $jefe = $emp;
                break;
            }
        }

        if (!$jefe) continue;

        $jefeOperaciones = $jefe[0];

        // Crear nodo base de OPERACIONES
        $estructura[$jefeOperaciones] = [
            "sector"       => "OPERACIONES",
            "jefe"         => $jefe,
            "responsables" => [],   // acá van los subsectores
            "empleados"    => []
        ];

        // ==============================
        // CREAR SUBSECTOR: OPERACIONES PROPIO
        // ==============================

        // 1) Empleados propios (directos del sector)
        $empleadosPropios = [];
        foreach ($lista as $emp) {
            if ($emp[0] === $jefe[0]) continue;     // no incluir al jefe
            if (trim($emp[1]) === "") continue;     // evitar cabecera vacía
            $empleadosPropios[] = $emp;
        }

        // 2) Crear subsector propio como si fuera un responsable más
        $estructura[$jefeOperaciones]["responsables"]["OPERACIONES_PROPIO"] = [
            "sector"    => "OPERACIONES PROPIO",
            "datos"     => $jefe,   // se muestra la card con la foto del jefe
            "empleados" => $empleadosPropios
        ];

        continue;
    }

    /* ---------------------------------------
       2) SUBSECTORES QUE VAN BAJO OPERACIONES
       --------------------------------------- */
    if (in_array($sector, $subsectoresOperaciones)) {

        if (!$jefeOperaciones) continue;

        // Buscar jefe o líder del subsector
        $jefeSub = null;
        foreach ($lista as $emp) {
            if (
                mb_strtoupper($emp[3]) === "JEFE" ||
                mb_strtoupper($emp[3]) === "SUPERVISOR" ||
                mb_strtoupper($emp[3]) === "RESPONSABLE"
            ) {

                $jefeSub = $emp;
                break;
            }
        }

        if (!$jefeSub) continue;

        // Crear subsector dentro de OPERACIONES
        $estructura[$jefeOperaciones]["responsables"][$jefeSub[0]] = [
            "sector"    => $sector,
            "datos"     => $jefeSub,
            "empleados" => []
        ];

        // Cargar empleados del subsector
        foreach ($lista as $emp) {
            if ($emp[0] === $jefeSub[0]) continue;
            if (trim($emp[1]) === "") continue;
            $estructura[$jefeOperaciones]["responsables"][$jefeSub[0]]["empleados"][] = $emp;
        }

        continue;
    }

    /* ---------------------------------------
       3) ESTRUCTURA ESPECIAL PROYECTOS Y OBRA
       --------------------------------------- */
    if ($sector === "PROYECTOS Y OBRA") {

        $responsablesOficina = [
            "8273" => [5519, 5019, 7713, 3360, 6502],
            "1613" => [9904, 2867, 1757, 5178]
        ];

        $jefe = null;
        foreach ($lista as $emp) {
            if (mb_strtoupper($emp[3]) === "JEFE") {
                $jefe = $emp;
                break;
            }
        }

        if (!$jefe) continue;

        $estructura[$jefe[0]] = [
            "sector"       => $sector,
            "jefe"         => $jefe,
            "responsables" => [],
            "empleados"    => []
        ];

        $asignados = [];

        foreach ($responsablesOficina as $legResp => $legajosEmpleado) {

            $respData = null;
            foreach ($lista as $emp) {
                if ($emp[0] == $legResp) $respData = $emp;
            }
            if (!$respData) continue;

            $estructura[$jefe[0]]["responsables"][$legResp] = [
                "datos"     => $respData,
                "empleados" => []
            ];

            foreach ($lista as $emp) {
                if (in_array((int)$emp[0], $legajosEmpleado)) {
                    $estructura[$jefe[0]]["responsables"][$legResp]["empleados"][] = $emp;
                    $asignados[$emp[0]] = true;
                }
            }
        }

        foreach ($lista as $emp) {
            if ($emp[0] === $jefe[0]) continue;
            if (isset($asignados[$emp[0]])) continue;
            if (trim($emp[1]) === "") continue;
            $estructura[$jefe[0]]["empleados"][] = $emp;
        }

        continue;
    }

    /* ---------------------------------------
       4) SECTORES NORMALES
       --------------------------------------- */

    $jefe = null;
    foreach ($lista as $emp) {
        if (mb_strtoupper($emp[3]) === "JEFE") {
            $jefe = $emp;
            break;
        }
    }

    if (!$jefe) {
        foreach ($lista as $emp) {
            if (
                strpos(mb_strtoupper($emp[3]), "RESPONSABLE") !== false &&
                trim($emp[1]) !== ""
            ) {
                $jefe = $emp;
                break;
            }
        }
    }

    if (!$jefe) {
        foreach ($lista as $emp) {
            if (trim($emp[1]) !== "") {
                $jefe = $emp;
                break;
            }
        }
    }

    if (!$jefe) continue;

    $estructura[$jefe[0]] = [
        "sector"       => $sector,
        "jefe"         => $jefe,
        "responsables" => [],
        "empleados"    => []
    ];

    foreach ($lista as $emp) {
        if ($emp[0] === $jefe[0]) continue;
        if (trim($emp[1]) === "") continue;
        $estructura[$jefe[0]]["empleados"][] = $emp;
    }
}

/* ============================================
   FUNCION AUXILIAR NOMBRES
   ============================================ */
function separarNombre($nombreCompleto)
{
    $p = explode(" ", trim($nombreCompleto));
    $a = array_shift($p);
    return [$a, implode(" ", $p)];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Organigrama Institucional</title>

    <style>
        body {
            background: #f2f4f7;
            font-family: 'Segoe UI', sans-serif;
        }

        h1 {
            text-align: center;
            color: #003f6b;
        }

        .nivel {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 15px;
        }

        .persona {
            background: white;
            padding: 10px;
            border-radius: 12px;
            width: 250px;
            min-height: 150px;
            box-shadow: 0 2px 6px #0001;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .persona img {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            border: 3px solid #f9c031;
            object-fit: cover;
        }

        .persona.jefe-area {
            width: 150px !important;
            min-height: 40px;
        }

        .persona.jefe-area img {
            width: 55px;
            height: 55px;
        }

        .texto-corto {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .empleados {
            display: none;
            margin-bottom: 40px;
        }

        .jefe-desplegable,
        .resp-desplegable {
            cursor: pointer;
        }

        .info {
            width: 100%;
            text-align: left;
            margin-top: 8px;
        }

        .info div {
            display: flex;
            gap: 6px;
        }

        /* Scroll para las tarjetas del carrusel superior */
        .nivel-scroll {
            display: flex;
            gap: 18px;
            margin-bottom: 15px;
            padding: 10px;
            overflow-x: auto;
            white-space: nowrap;
            scroll-behavior: smooth;
        }

        .nivel-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .nivel-scroll::-webkit-scrollbar-thumb {
            background: #b7c1cc;
            border-radius: 10px;
        }

        .nivel-scroll::-webkit-scrollbar-track {
            background: #e4e7eb;
        }

        /* Subsectores centrados */
        .subsectores {
            display: flex;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <?php include "./nav.php"; ?>

    <?php $rutaPDF = "../documentos/Organigrama.pdf"; ?>

    <div style="text-align:center; margin:20px 0;">
        <a href="<?= $rutaPDF ?>" download
            style="background:#003f6b;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:bold;">
            📄 Descargar Organigrama PDF
        </a>
    </div>

    <h1>👥 Organigrama Institucional</h1>

    <!-- PRESIDENTE --> 
    <div class="nivel">
        <?php foreach ($empleados as $e): ?>
            <?php if ($e[3] === "PRESIDENTE"): ?>
                <div class="persona">
                    <img src="<?= foto($e[0]) ?>">
                    <div class="nombre texto-corto"><?= $e[1] ?></div>
                    <div class="cargo texto-corto"><?= $e[3] ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- VICEPRESIDENTE -->
    <div class="nivel">
        <?php foreach ($empleados as $e): ?>
            <?php if ($e[3] === "VICEPRESIDENTE"): ?>
                <div class="persona">
                    <img src="<?= foto($e[0]) ?>">
                    <div class="nombre texto-corto"><?= $e[1] ?></div>
                    <div class="cargo texto-corto"><?= $e[3] ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- CARRUSEL DE JEFES DE ÁREA (OPERACIONES ES EL ÚNICO DE ESTOS GRUPOS) -->
    <div class="nivel-scroll">
        <?php
        // 1) Mostrar primero OPERACIONES si existe
        if (isset($estructura[$jefeOperaciones])):
            $data = $estructura[$jefeOperaciones];
        ?>
            <div class="persona jefe-area jefe-desplegable" data-jefe="<?= $jefeOperaciones ?>">
                <div class="nombre texto-corto" style="font-weight:bold; text-align:center;">
                    OPERACIONES ▼
                </div>
            </div>
        <?php endif; ?>

        <?php
        // 2) Mostrar el resto de los sectores (EXCEPTO OPERACIONES)
        foreach ($estructura as $idJefe => $data):
            if ($idJefe == $jefeOperaciones) continue;
        ?>
            <div class="persona jefe-area jefe-desplegable" data-jefe="<?= $idJefe ?>">
                <div class="nombre texto-corto" style="font-weight:bold; text-align:center;">
                    <?= $data["sector"] ?> ▼
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <!-- EQUIPOS DE CADA JEFE -->
    <?php foreach ($estructura as $idJefe => $data): ?>

        <?php
        $j = $data["jefe"];
        $sector = $data["sector"];
        $responsables = $data["responsables"];
        $empleadosSector = $data["empleados"];

        // Empleados directos que no son subsector
        $empleadosDirectos = [];
        foreach ($empleadosSector as $emp) {
            if (isset($responsables[$emp[0]])) continue;
            $empleadosDirectos[] = $emp;
        }

        // Grupos de cargos
        $grupos = [
            "responsable" => [],
            "supervisor"  => [],
            "coordinador" => [],
            "base"        => []
        ];
        foreach ($empleadosDirectos as $emp) {
            $nivel = nivelCargo($emp[3]);
            $grupos[$nivel][] = $emp;
        }
        ?>

<div id="empleados-<?= $idJefe ?>" class="empleados empleados-jefe">

    <?php if ($j && $sector !== "OPERACIONES"): ?>
        <!-- Sectores normales: título + tarjeta del jefe --> 
        <h3 style="color:#003f6b;">
            👥 Equipo de <?= $j[1] ?> — Jefe de <?= $sector ?>
        </h3>

        <div class="nivel">
            <div class="persona" style="border:2px solid #003f6b;background:#eaf3ff;">
                <img src="<?= foto($j[0]) ?>">
                <div class="nombre texto-corto"><?= $j[1] ?></div>
                <div class="cargo texto-corto"><?= $j[3] ?></div>

                <div class="info">
                    <div>
                        📞 <?= $j[4] ? "<a href='https://wa.me/54{$j[4]}' target='_blank'>{$j[4]}</a>" : "—" ?>
                    </div>
                    <div>
                        ✉️ <?= $j[5] ? "<a href='mailto:{$j[5]}'>{$j[5]}</a>" : "—" ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
         <!-- OPERACIONES: solo título genérico, sin tarjeta del jefe -->
        <h3 style="color:#003f6b;">
            👥 Equipo de <?= $sector ?>
        </h3>
    <?php endif; ?>


            <!-- SUBSECTORES DE OPERACIONES --> 
            <?php if ($sector === "OPERACIONES" && !empty($responsables)): ?>

                <div class="subsectores">
                    <?php foreach ($responsables as $idResp => $infoResp): ?>
                        <?php
                        $r = $infoResp["datos"];
                        $sectorMostrar = ($idResp === "OPERACIONES_PROPIO")
                            ? "OPERACIONES PROPIO"
                            : $infoResp["sector"];
                        ?>
                        <div class="persona jefe-area resp-desplegable"
                            data-jefe="<?= $idJefe ?>" data-resp="<?= $idResp ?>">
                            <img src="<?= foto($r[0]) ?>">
                            <div class="nombre texto-corto"><?= $sectorMostrar ?></div>
                            <div class="cargo texto-corto"><?= $r[3] ?> ▼</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Equipos de subsectores --> 
                <?php foreach ($responsables as $idResp => $infoResp): ?>
                    <?php $emplResp = $infoResp["empleados"]; ?>

                    <div id="empleados-resp-<?= $idJefe ?>-<?= $idResp ?>" class="empleados empleados-resp">
                        <h3 style="color:#003f6b; margin-top:25px; text-align:center;">
                            <?= $infoResp["sector"] ?> — Equipo de <?= $infoResp["datos"][1] ?>
                        </h3>

                        <div class="nivel">
                            <?php foreach ($emplResp as $e): ?>
                                <div class="persona">
                                    <img src="<?= foto($e[0]) ?>">
                                    <div class="nombre texto-corto"><?= $e[1] ?></div>
                                    <div class="cargo texto-corto"><?= $e[3] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>

            <!-- RESPONSABLES DE PROYECTOS Y OBRA --> 
            <?php if ($sector === "PROYECTOS Y OBRA" && !empty($responsables)): ?>

                <h3 style="color:#003f6b;text-align:center;margin-top:30px;">Responsables de Oficina Técnica</h3>

                <div class="subsectores">
                    <?php foreach ($responsables as $idResp => $infoResp): ?>
                        <?php $r = $infoResp["datos"]; ?>
                        <div class="persona jefe-area resp-desplegable"
                            data-jefe="<?= $idJefe ?>" data-resp="<?= $idResp ?>">
                            <img src="<?= foto($r[0]) ?>">
                            <div class="nombre texto-corto"><?= $r[1] ?></div>
                            <div class="cargo texto-corto"><?= $r[3] ?> ▼</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Equipos por responsable --> 
                <?php foreach ($responsables as $idResp => $infoResp): ?>
                    <?php $emplResp = $infoResp["empleados"]; ?>

                    <div id="empleados-resp-<?= $idJefe ?>-<?= $idResp ?>" class="empleados empleados-resp">

                        <h3 style="color:#003f6b; margin-top:25px;">
                            Equipo de <?= $infoResp["datos"][1] ?>
                        </h3>

                        <div class="nivel">
                            <?php foreach ($emplResp as $e): ?>
                                <div class="persona">
                                    <img src="<?= foto($e[0]) ?>">
                                    <div class="nombre texto-corto"><?= $e[1] ?></div>
                                    <div class="cargo texto-corto"><?= $e[3] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>

                <?php endforeach; ?>
            <?php endif; ?>

            <!-- EMPLEADOS DIRECTOS DEL JEFE -->
            <?php foreach ($grupos as $nivel => $lista): ?>
                <?php if (!empty($lista)): ?>
                    <div class="nivel">
                        <?php foreach ($lista as $e): ?>
                            <div class="persona">
                                <img src="<?= foto($e[0]) ?>">
                                <div class="nombre texto-corto"><?= $e[1] ?></div>
                                <div class="cargo texto-corto"><?= $e[3] ?></div>

                                <div class="info">
                                    <div>
                                        📞 <?= $e[4] ? "<a href='https://wa.me/54{$e[4]}' target='_blank'>{$e[4]}</a>" : "—" ?>
                                    </div>
                                    <div>
                                        ✉️ <?= $e[5] ? "<a href='mailto:{$e[5]}'>{$e[5]}</a>" : "—" ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>

    <?php endforeach; ?>

    <!-- ============================================
     SCRIPTS PARA ABRIR/CERRAR SECTORES Y SUBSECTORES
     ============================================ --> 
    <script>
        // Abrir/Cerrar Jefe Principal (sector entero)
        document.querySelectorAll(".jefe-desplegable").forEach(j => {
            j.addEventListener("click", () => {
                const id = j.dataset.jefe;
                const box = document.getElementById("empleados-" + id);

                // Cerrar todos los equipos de jefes
                document.querySelectorAll(".empleados-jefe").forEach(div => {
                    if (div !== box) div.style.display = "none";
                });

                // Cerrar todos los responsables también
                document.querySelectorAll(".empleados-resp").forEach(div => {
                    div.style.display = "none";
                });

                // Toggle
                box.style.display = (box.style.display === "block") ? "none" : "block";
            });
        });

        // Abrir/Cerrar Subsectores o Responsables (Operaciones / Proyectos)
        document.querySelectorAll(".resp-desplegable").forEach(r => {
            r.addEventListener("click", () => {
                const idJefe = r.dataset.jefe;
                const idResp = r.dataset.resp;
                const box = document.getElementById("empleados-resp-" + idJefe + "-" + idResp);

                // Cerrar otros responsables
                document.querySelectorAll(".empleados-resp").forEach(div => {
                    if (div !== box) div.style.display = "none";
                });

                // Toggle
                box.style.display = (box.style.display === "block") ? "none" : "block";
            });
        });
    </script>

</body>
</html>