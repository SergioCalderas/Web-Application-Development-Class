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

function obtenerServiciosTerminadosPorCliente($id_cliente) {
    global $conn;
    $sql = "SELECT id_servicio FROM Servicios WHERE id_cliente = ? AND estado_servicio = 'Terminado'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
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

function obtenerPrecioServicio($id_servicio) {
    global $conn;
    $sql = "SELECT costo FROM Servicios WHERE id_servicio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_servicio);
    $stmt->execute();
    $result = $stmt->get_result();
    $precio = 0;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $precio = $row['costo'];
    }
    $stmt->close();
    return $precio;
}

function procesarPago($id_cliente) {
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener datos del formulario
        $tipo_tarjeta = $_POST["tipo_tarjeta"];
        $nombre_tarjeta = $_POST["nombre_tarjeta"];
        $id_servicio = $_POST["servicio"];
        $numero_tarjeta = $_POST["numero_tarjeta"];
        $direccion_facturacion = $_POST["direccion_facturacion"];

        // Obtener el costo del servicio
        $costo_servicio = obtenerPrecioServicio($id_servicio);

        // Insertar en la tabla pagos
        $sql_insert_pago = "INSERT INTO Pagos (tipo_tarjeta, nombre_tarjeta, precio, numero_tarjeta, direccion_facturacion, id_cliente) 
                            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert_pago = $conn->prepare($sql_insert_pago);
        $stmt_insert_pago->bind_param("ssdssi", $tipo_tarjeta, $nombre_tarjeta, $costo_servicio, $numero_tarjeta, $direccion_facturacion, $id_cliente);

        // Ejecutar la inserción
        if ($stmt_insert_pago->execute()) {
            // Actualizar estado_servicio en la tabla servicios
            $sql_update_servicio = "UPDATE Servicios SET estado_servicio = 'Pagado' WHERE id_servicio = ?";
            $stmt_update_servicio = $conn->prepare($sql_update_servicio);
            $stmt_update_servicio->bind_param("i", $id_servicio);
            $stmt_update_servicio->execute();
            $stmt_update_servicio->close();

            echo "<p>Pago registrado correctamente.</p>";
        } else {
            echo "<p>Error al registrar el pago: " . $conn->error . "</p>";
        }

        $stmt_insert_pago->close();
    }
}

$id_cliente = $_SESSION['user_id']; // Adjust this according to how client ID is stored in your session

$servicios_terminados = obtenerServiciosTerminadosPorCliente($id_cliente);

procesarPago($id_cliente);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estiloCliente.css">
    <title>Pagar Servicio</title>
    
    <?php
        function validarDatos($nombre_tarjeta, $numero_tarjeta, $fecha_vencimiento, $cvv, $direccion_facturacion) {
            $errores = [];

            // Validar nombre en la tarjeta permitiendo acentos y caracteres especiales
            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u", $nombre_tarjeta)) {
                $errores[] = "El nombre en la tarjeta solo debe contener letras, espacios y caracteres acentuados.";
            }

            // Validar número de la tarjeta
            if (!preg_match("/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/", $numero_tarjeta)) {
                $errores[] = "El número de la tarjeta debe tener 4 grupos de 4 dígitos separados por espacios.";
            }

            // Validar fecha de vencimiento
            $fecha_actual = date('Y-m');
            if ($fecha_vencimiento < $fecha_actual) {
                $errores[] = "La fecha de vencimiento no puede ser anterior a la fecha actual.";
            }

            // Validar CVV
            if (!preg_match("/^\d{3}$/", $cvv)) {
                $errores[] = "El CVV debe ser un número de 3 dígitos.";
            }

            // Validar dirección de facturación
            if (!preg_match("/^[a-zA-Z0-9\s]+$/", $direccion_facturacion)) {
                $errores[] = "La dirección de facturación solo debe contener letras, números y espacios.";
            }

            return $errores;
        }

        $errores = [];
        $exito = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre_tarjeta = $_POST["nombre_tarjeta"];
            $numero_tarjeta = $_POST["numero_tarjeta"];
            $fecha_vencimiento = $_POST["fecha_vencimiento"];
            $cvv = $_POST["cvv"];
            $direccion_facturacion = $_POST["direccion_facturacion"];

            $errores = validarDatos($nombre_tarjeta, $numero_tarjeta, $fecha_vencimiento, $cvv, $direccion_facturacion);

            if (empty($errores)) {
                // Aquí se realizaría el proceso de pago
                $exito = true;
            }
        }
    ?>
</head>
<body>
    <nav>
        <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
        <div class="logo_text"><a href="./vista_cliente.php"><b>Plumber-Pro</b></a></div>
        <img src="./imagenes/notificacion.png" alt="Notificaciones" class="imagen_notificacion">
    </nav>
    <main>
        <div class="contenedor_todo">
            <h1>Pagar Servicio</h1>

            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if ($exito) {
                        echo "<p>Datos válidos. Pago realizado con éxito.</p>";
                    } else {
                        foreach ($errores as $error) {
                            echo "<p>Error: $error</p>";
                        }
                    }
                }
            ?>

            <div class="registrar">
                <form action="" method="post">
                    <div class="tipo_servicio">
                        <label for="servicio">* Seleccione un servicio:</label>
                        <select id="servicio" name="servicio">
                            <?php foreach ($servicios_terminados as $servicio): ?>
                                <option value="<?php echo htmlspecialchars($servicio['id_servicio']); ?>">
                                    <?php echo htmlspecialchars($servicio['id_servicio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tipo_servicio">
                        <label for="tipo_tarjeta">* Tipo Tarjeta:</label>
                        <select id="tipo_tarjeta" name="tipo_tarjeta">
                            <option value="TarjetaCredito">Tarjeta de Crédito</option>
                            <option value="TarjetaDebito">Tarjeta de Débito</option>
                        </select>
                    </div>
                    <div>
                        <label for="nombre_tarjeta">* Nombre en la tarjeta:</label>
                        <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" required>
                    </div>
                    <div>
                        <label for="numero_tarjeta">* Número en la tarjeta:</label>
                        <input type="text" id="numero_tarjeta" name="numero_tarjeta" required maxlength="19">
                    </div>
                    <div>
                        <label for="fecha_vencimiento">* Fecha vencimiento:</label>
                        <input type="month" id="fecha_vencimiento" name="fecha_vencimiento" required>
                    </div>
                    <div>
                        <label for="cvv">* CVV:</label>
                        <input type="number" name="cvv" id="cvv" min="000" max="999" required>
                    </div>
                    <div>
                        <label for="direccion_facturacion">* Dirección facturación:</label>
                        <input type="text" id="direccion_facturacion" name="direccion_facturacion" required>
                    </div>
                    <button type="submit">Pagar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>