<?php
    $mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $legajo = trim($_POST["legajo"]);
    $dni = trim($_POST["dni"]);

    if ($legajo && $dni) {
        $sql = "SELECT DNI, NOMBRE, APELLIDO, LEGAJO, SECTOR, CARGO, FECHA_INGRESO, CUMPLEANIOS, EMAIL, FBAJA001
                FROM USUARIOS_DATOS
                WHERE LEGAJO = :legajo AND DNI = :dni";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":legajo", $legajo);
        $stmt->bindParam(":dni", $dni);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            // ❌ Si FBAJA001 NO ES NULL → usuario dado de baja
            if (!empty($usuario["FBAJA001"])) {
                $mensaje = "❌ Este usuario ya no pertenece a la empresa (dado de baja).";
            } else {

                // ✅ Usuario válido → iniciar sesión
                $_SESSION["usuario"] = $usuario["LEGAJO"];
                $_SESSION["nombre"] = $usuario["NOMBRE"];
                $_SESSION["apellido"] = $usuario["APELLIDO"];
                $_SESSION["sector"] = $usuario["SECTOR"];
                $_SESSION["cargo"] = $usuario["CARGO"];
                $_SESSION["email"] = $usuario["EMAIL"];
                $_SESSION["fecha_ingreso"] = $usuario["FECHA_INGRESO"];
                $_SESSION["cumpleanios"] = $usuario["CUMPLEANIOS"];

                header("Location: ../htmlintranet/usuario.php");
                exit;
            }
        } else {
            $mensaje = "❌ Legajo o número de documento incorrecto";
        }
    } else {
        $mensaje = "⚠️ Completa ambos campos";
    }
}
?>