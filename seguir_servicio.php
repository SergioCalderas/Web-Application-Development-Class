<?php
session_start();

$servername = "localhost";
$username = "grupog";
$password = "2024#pfdf#8";
$dbname = "proyectoequg";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function obtenerServiciosPorTecnico($id_tecnico) {
    global $conn;
    $sql = "SELECT id_servicio FROM Servicios WHERE id_tecnico = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tecnico);
    $stmt->execute();
    $result = $stmt->get_result();
    $servicios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $servicios[] = $row;
        }
    }
    $stmt->close();
    return $servicios;
}

function obtenerDetallesServicio($id_servicio) {
    global $conn;
    $sql = "SELECT direccion, codigo_postal, tipo_servicio, fecha, hora, estado_servicio FROM Servicios WHERE id_servicio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_servicio);
    $stmt->execute();
    $result = $stmt->get_result();
    $servicio = $result->fetch_assoc();
    $stmt->close();
    return $servicio;
}

function terminarServicio($id_servicio) {
    global $conn;
    $sql = "UPDATE Servicios SET estado_servicio = 'Terminado' WHERE id_servicio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_servicio);
    $stmt->execute();
    $stmt->close();
}

function guardarImagenServicio($id_tecnico, $id_servicio, $ruta_imagen) {
    global $conn;
    $sql = "INSERT INTO Historial (id_tecnico, id_servicio, ruta_imagen) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $id_tecnico, $id_servicio, $ruta_imagen);
    $stmt->execute();
    $stmt->close();
}

$errores = [];
$exito = false;
$servicio_seleccionado = [];
$mensaje_terminado = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["id_servicio"])) {
        $id_servicio = $_POST["id_servicio"];
        $servicio_seleccionado = obtenerDetallesServicio($id_servicio);
    } elseif (isset($_POST["terminar_servicio"])) {
        $id_servicio = $_POST["terminar_servicio"];
        terminarServicio($id_servicio);
        $mensaje_terminado = "Servicio No. $id_servicio terminado.";

        // Procesar la subida de la imagen
        if (isset($_FILES['imagen_servicio']) && $_FILES['imagen_servicio']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = $_FILES['imagen_servicio']['name'];
            $ruta_temporal = $_FILES['imagen_servicio']['tmp_name'];
            $ruta_destino = './ImagenesServicios/' . $nombre_archivo;

            if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
                // Guardar la ruta de la imagen en la base de datos
                guardarImagenServicio($_SESSION['user_id'], $id_servicio, $ruta_destino);
                $exito = true;
            } else {
                $errores[] = "Error al mover el archivo.";
            }
        } else {
            $errores[] = "No se ha seleccionado ningún archivo o ha ocurrido un error en la subida.";
        }
    }
}

$id_tecnico = $_SESSION['user_id']; // Obtener el id_tecnico desde la sesión (debes tener lógica de autenticación para esto)

$servicios_tecnico = obtenerServiciosPorTecnico($id_tecnico);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <link rel="stylesheet" href="./estilos/estilo_seguir_servicio.css">
    <title>Seguir Servicios</title>
</head>
<body>
    <nav>
        <div class="nav-left">
            <div class="imagen_logo">
                <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
            </div>
            <div class="logo_text"><a href="./vista_tecnico.php"><b>Plumber-Pro</b></a></div>
        </div>
        <div class="imagen_notificacion">
            <img src="./imagenes/notificacion.png" alt="Notificaciones" class="imagen_notificacion">
        </div>
    </nav>
    <main>
    <h2>Seguir Servicios</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="id_servicio">Seleccionar Id de Servicio:</label>
        <select id="id_servicio" name="id_servicio">
            <option value="">Seleccionar...</option>
            <?php foreach ($servicios_tecnico as $servicio): ?>
                <option value="<?php echo htmlspecialchars($servicio['id_servicio']); ?>"><?php echo htmlspecialchars($servicio['id_servicio']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Mostrar Detalles</button>
    </form>

    <?php if (!empty($servicio_seleccionado)): ?>
        <h2>Detalles del Servicio Seleccionado</h2>
        <form method="post" class="seguir_servivio_form">
            <div>
                <label for="id_servicio_seleccionado">ID Servicio:</label>
                <input type="text" id="id_servicio_seleccionado" name="id_servicio_seleccionado" value="<?php echo htmlspecialchars($_POST['id_servicio']); ?>" readonly>
            </div>
            <div>
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($servicio_seleccionado['direccion'] ?? ''); ?>" readonly>
            </div>
            <div>
                <label for="codigo_postal">Código Postal:</label>
                <input type="text" id="codigo_postal" name="codigo_postal" value="<?php echo htmlspecialchars($servicio_seleccionado['codigo_postal'] ?? ''); ?>" readonly>
            </div>
            <div>
                <label for="tipo_servicio">Tipo Servicio:</label>
                <input type="text" id="tipo_servicio" name="tipo_servicio" value="<?php echo htmlspecialchars($servicio_seleccionado['tipo_servicio'] ?? ''); ?>" readonly>
            </div>
            <div>
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($servicio_seleccionado['fecha'] ?? ''); ?>" readonly>
            </div>
            <div>
                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" value="<?php echo htmlspecialchars($servicio_seleccionado['hora'] ?? ''); ?>" readonly>
            </div>
            <div>
                <label for="estado">Estado del Servicio:</label>
                <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($servicio_seleccionado['estado_servicio'] ?? ''); ?>" readonly>
            </div>
        </form>

        <h2>Seleccione un Servicio para Terminar</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="terminar_otro_servicio">Seleccionar Id de Servicio:</label>
            <select id="terminar_otro_servicio" name="terminar_servicio">
                <option value="">Seleccionar...</option>
                <?php foreach ($servicios_tecnico as $servicio): ?>
                    <option value="<?php echo htmlspecialchars($servicio['id_servicio']); ?>"><?php echo htmlspecialchars($servicio['id_servicio']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Terminar Servicio</button>

            <label for="imagen_servicio">Subir Imagen:</label>
            <input type="file" id="imagen_servicio" name="imagen_servicio" accept="image/*">
        </form>
    <?php endif; ?>

    <?php if (!empty($mensaje_terminado)): ?>
        <p><?php echo htmlspecialchars($mensaje_terminado); ?></p>
    <?php endif; ?>
    </main>
</body>
</html>