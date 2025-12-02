<?php
$mensaje = "";

// ======================================================
// 🔹 ELIMINAR REGISTRO
// ======================================================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM CREAR_SERVICIO_OBRA_FLOTA WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $mensaje = "🗑️ Registro eliminado correctamente.";
}

// ======================================================
// 🔹 EDITAR REGISTRO
// ======================================================
if (isset($_GET['editar'])) {
    $idEditar = intval($_GET['editar']);
    $sql = "SELECT * FROM CREAR_SERVICIO_OBRA_FLOTA WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $idEditar, PDO::PARAM_INT);
    $stmt->execute();
    $registroEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ======================================================
// 🔹 CREAR / ACTUALIZAR REGISTRO
// ======================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emoji = $_POST["emoji"] ?? "";
    $categoria = strtolower(trim($_POST["categoria"] ?? ""));
    $titulo = trim($_POST["titulo"] ?? "");
    $cuerpo = trim($_POST["cuerpo"] ?? "");
    $comitente = trim($_POST["comitente"] ?? "");
    $fecha_inicio = $_POST["fecha_inicio"] ?? null;
    $fecha_final = $_POST["fecha_final"] ?? null;
    $id_editar = $_POST["id_editar"] ?? "";

    $directorio = "../imagenes/obras/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // ======================================================
    // 🔸 INSERTAR O ACTUALIZAR EN BD
    // ======================================================
    if ($emoji && $categoria && $titulo && $cuerpo) {
        if ($id_editar) {
            // 🔹 Si se está editando
            $sql = "UPDATE CREAR_SERVICIO_OBRA_FLOTA 
                    SET EMOJI = :emoji, CATEGORIA = :categoria, TITULO = :titulo, 
                        CUERPO = :cuerpo, COMITENTE = :comitente, 
                        FECHA_INICIO = :fecha_inicio, FECHA_FINAL = :fecha_final
                    WHERE ID = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $id_editar);
            $stmt->bindParam(":emoji", $emoji);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":titulo", $titulo);
            $stmt->bindParam(":cuerpo", $cuerpo);
            $stmt->bindParam(":comitente", $comitente);
            $stmt->bindParam(":fecha_inicio", $fecha_inicio);
            $stmt->bindParam(":fecha_final", $fecha_final);
            $stmt->execute();
            $registroID = $id_editar;
        } else {
            // 🔹 Insertar nuevo registro y obtener el ID generado
            $sql = "INSERT INTO CREAR_SERVICIO_OBRA_FLOTA 
                    (EMOJI, CATEGORIA, TITULO, CUERPO, COMITENTE, FECHA_INICIO, FECHA_FINAL)
                    OUTPUT INSERTED.ID
                    VALUES (:emoji, :categoria, :titulo, :cuerpo, :comitente, :fecha_inicio, :fecha_final)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":emoji", $emoji);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":titulo", $titulo);
            $stmt->bindParam(":cuerpo", $cuerpo);
            $stmt->bindParam(":comitente", $comitente);
            $stmt->bindParam(":fecha_inicio", $fecha_inicio);
            $stmt->bindParam(":fecha_final", $fecha_final);
            $stmt->execute();
            $registroID = $stmt->fetchColumn(); // ID generado
        }

        // ======================================================
        // 🔸 SUBIR IMAGEN PRINCIPAL (usando ID)
        // ======================================================
        if (!empty($_FILES["imagen_principal"]["name"])) {
            $ext = strtolower(pathinfo($_FILES["imagen_principal"]["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $nombrePrincipal = "{$categoria}{$registroID}.{$ext}";
                $rutaPrincipal = $directorio . $nombrePrincipal;
                move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $rutaPrincipal);
            }
        }

        // ======================================================
        // 🔸 SUBIR IMÁGENES SECUNDARIAS (usando ID)
        // ======================================================
        if (!empty($_FILES["imagenes_secundarias"]["name"][0])) {
            $contador = 1;
            $baseNombre = "{$categoria}{$registroID}";
            foreach ($_FILES["imagenes_secundarias"]["name"] as $index => $nombre) {
                $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $nuevoNombre = "{$baseNombre}-{$contador}.{$ext}";
                    $rutaFinal = $directorio . $nuevoNombre;
                    move_uploaded_file($_FILES["imagenes_secundarias"]["tmp_name"][$index], $rutaFinal);
                    $contador++;
                }
            }
        }

        $mensaje = $id_editar
            ? "✏️ Registro actualizado correctamente."
            : "✅ Registro creado correctamente.";
    } else {
        $mensaje = "⚠️ Completá todos los campos antes de guardar.";
    }
}

// ======================================================
// 🔹 LEER REGISTROS EXISTENTES
// ======================================================
$sql = "SELECT * FROM CREAR_SERVICIO_OBRA_FLOTA ORDER BY FECHA_CREACION DESC";
$stmt = $conn->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>