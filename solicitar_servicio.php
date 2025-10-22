<?php
session_start();

function validarDatos($direccion, $hora, $codigo_postal, $fecha) {
    $errores = [];

    // Validar dirección
    if (!preg_match("/^[a-zA-Z0-9\s]+$/", $direccion)) {
        $errores[] = "La dirección solo debe contener letras, números y espacios.";
    }

    // Validar código postal
    if (!preg_match("/^\d{5}$/", $codigo_postal)) {
        $errores[] = "El código postal debe ser un número de 5 dígitos.";
    }

    // Validar hora
    $hora_minima = "08:00";
    $hora_maxima = "16:00";
    if ($hora < $hora_minima || $hora > $hora_maxima) {
        $errores[] = "La hora debe estar dentro del rango de las 8:00 AM a las 4:00 PM.";
    }

    // Validar fecha
    $fecha_actual = date('Y-m-d');
    if ($fecha < $fecha_actual) {
        $errores[] = "La fecha no debe ser anterior al día de hoy.";
    }

    return $errores;
}

$errores = [];
$exito = false;
$delegacion = "";
$seccion = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $direccion = $_POST["direccion"];
    $hora = $_POST["hora"];
    $codigo_postal = $_POST["codigo_postal"];
    $fecha = $_POST["fecha"];
    $servicio = $_POST["servicio"];
    $id_cliente = $_POST["id_cliente"]; // Obtener el id_cliente del formulario

    $errores = validarDatos($direccion, $hora, $codigo_postal, $fecha);

    if (empty($errores)) {
        // Obtener la delegación según el código postal
        $delegacion = obtenerDelegacion($codigo_postal);

        if ($delegacion !== false) {

            // Asignar sección según la delegación
            $seccion = asignarSeccion($delegacion);
            echo $seccion;

            if ($seccion !== false) {
                // Obtener el precio del servicio
                $precio = obtenerPrecioServicio($servicio);

                if ($precio !== false) {
                    // Buscar técnico disponible en la sección asignada
                    $id_tecnico = buscarTecnicoPorSeccion($seccion);

                    if ($id_tecnico !== false) {
                        // Insertar el servicio en la base de datos
                        if (insertarServicio($direccion, $codigo_postal, $servicio, $hora, $fecha, $precio, $id_cliente, $id_tecnico)) {
                            $exito = true;
                        } else {
                            $errores[] = "Error al insertar el servicio en la base de datos.";
                        }
                    } else {
                        $errores[] = "No hay técnicos disponibles para la sección asignada.";
                    }
                } else {
                    $errores[] = "No se encontró el precio para el servicio seleccionado.";
                }
            } else {
                $errores[] = "No se pudo asignar la sección para la delegación obtenida.";
            }
        } else {
            $errores[] = "No se encontró la delegación para el código postal proporcionado.";
        }
    }
}

function obtenerDelegacion($codigo_postal) {
    $archivo = "./CodigosPostales.txt";
    $handle = fopen($archivo, "r");

    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // Asegurarse de que la línea esté en UTF-8
            $line = utf8_encode($line);
            $data = explode("|", $line);
            if ($data[0] == $codigo_postal) {
                fclose($handle);
                return $data[3]; // Devuelve el nombre de la delegación
            }
        }

        fclose($handle);
        return false; // No se encontró la delegación
    } else {
        return false; // Error al abrir el archivo
    }
}

function asignarSeccion($delegacion) {
    switch ($delegacion) {
        case "Gustavo A. Madero":
        case "Venustiano Carranza":
        case "Iztacalco":
        case "Iztapalapa":
            return 1;
        case "Milpa Alta":
        case "Tláhuac":
        case "Xochimilco":
            return 2;
        case "Coyoacán":
        case "Tlalpan":
        case "Magdalena Contreras":
            return 3;
        case "Azcapotzalco":
        case "Cuauhtémoc":
        case "Miguel Hidalgo":
        case "Benito Juárez":
        case "Álvaro Obregón":
        case "Cuajimalpa":
            return 4;
        default:
            return false; // No se encontró la delegación en ninguna sección
    }
}

function obtenerPrecioServicio($servicio) {
    switch ($servicio) {
        case "CambioFiltro":
            return 1000;
        case "LimpiezaTinaco":
            return 750;
        case "FugaGas":
            return 1500;
        default:
            return false; // Servicio no encontrado
    }
}

function buscarTecnicoPorSeccion($seccion) {
    $servername = "localhost";
    $username = "grupog";
    $password = "2024#pfdf#8";
    $dbname = "proyectoequg";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT id_tecnico FROM Tecnicos WHERE seccion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $seccion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_tecnico = $row['id_tecnico'];
    } else {
        $id_tecnico = false; // No se encontró ningún técnico en esa sección
    }

    $stmt->close();
    $conn->close();

    return $id_tecnico;
}

function insertarServicio($direccion, $codigo_postal, $servicio, $hora, $fecha, $precio, $id_cliente, $id_tecnico) {
    $servername = "localhost";
    $username = "grupog";
    $password = "2024#pfdf#8";
    $dbname = "proyectoequg";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "INSERT INTO Servicios (direccion, codigo_postal, tipo_servicio, hora, fecha, costo, id_cliente, id_tecnico) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdii", $direccion, $codigo_postal, $servicio, $hora, $fecha, $precio, $id_cliente, $id_tecnico);
    $resultado = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $resultado;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estiloCliente.css">
    <title>Solicitar Servicio</title>

    <script>
        // Función para actualizar el precio basado en el tipo de servicio seleccionado
        function actualizarPrecio() {
            var servicio = document.getElementById("servicio").value;
            var precio = document.getElementById("precio");

            switch (servicio) {
                case "CambioFiltro":
                    precio.value = "1000";
                    break;
                case "LimpiezaTinaco":
                    precio.value = "750";
                    break;
                case "FugaGas":
                    precio.value = "1500";
                    break;
                default:
                    precio.value = "0";
                    break;
            }
        }
    </script>
</head>
<body>
    <nav>
        <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
        <div class="logo_text"><a href="./vista_cliente.php"><b>Plumber-Pro</b></a></div>
        <img src="./imagenes/notificacion.png" alt="Notificaciones" class="imagen_notificacion">
    </nav>
    <main>
        <div class="contenedor_todo">
            <h1>Solicitar Servicio</h1>

            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if ($exito) {
                        echo "<p>Datos válidos. Solicitud de servicio realizada con éxito.</p>";
                    } else {
                        foreach ($errores as $error) {
                            echo "<p>Error: $error</p>";
                        }
                    }
                }
            ?>

            <div class="registrar">
                <div class="registrar_tipo">Datos Servicio</div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div>
                        <label for="direccion">* Dirección:</label>
                        <input type="text" id="direccion" name="direccion" required>
                    </div>
                    <div>
                        <label for="hora">* Hora:</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>
                    <div>
                        <label for="codigo_postal">* Código Postal:</label>
                        <input type="text" id="codigo_postal" name="codigo_postal" required>
                    </div>
                    <div>
                        <label for="fecha">* Fecha:</label>
                        <input type="date" id="fecha" name="fecha" required>
                    </div>                    
                    <div class="tipo_servicio">
                        <label for="servicio">* Tipo Servicio:</label>
                        <select id="servicio" name="servicio" onchange="actualizarPrecio()">
                            <option value="CambioFiltro">Lavado de Tinacos</option>
                            <option value="LimpiezaTinaco">Reparar Fuga de Agua</option>
                            <option value="FugaGas">Instalación Calentador de Agua</option>
                        </select>
                    </div>
                    <div>
                        <label for="precio">Precio Total:</label>
                        <input type="text" id="precio" name="precio" disabled placeholder="$$$">
                    </div>
                    <input type="hidden" name="id_cliente" value="<?php echo $_SESSION['user_id']; ?>">
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>