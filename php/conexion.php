<?php
// --- CONFIGURACIÓN DE CONEXIÓN ---
//$servers = [
//    "192.168.1.222,1433", // IP privada
//    "209.13.190.105,1433" // IP pública
//];

//$database = "Sistema_Integral_Exgadet";
//$username = "sa";
//$password = "Exgadetsa01";

//$conn = null;
//$lastError = null;

// --- INTENTAR CONECTAR ---
//foreach ($servers as $serverName) {
//    try {
//        $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
//        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "✅ Conectado correctamente a $serverName<br>";
//        break; // ✅ conexión exitosa
//    } catch (PDOException $e) {
//        $lastError = $e->getMessage();
//    }
//}

// --- VERIFICAR CONEXIÓN ---
//if (!$conn) {
//    die("❌ No se pudo conectar a ningún servidor SQL. Error: " . $lastError);
//}
//?>



<?php
$serverName = "192.168.1.222,1433"; // IP y puerto de SQL Server
$database   = "Sistema_Integral_Exgadet";
$username   = "sa";
$password   = "Exgadetsa01";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conexión establecida con SQL Server.";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>




