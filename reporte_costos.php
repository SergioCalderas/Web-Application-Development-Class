<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <link rel="stylesheet" href="./estilos/estilos_tablas.css">
    <title>Reporte costos</title>
    <?php
        function validarFechas($fecha_inicio, $fecha_fin) {
            $errores = [];

            // Validar fechas
            if (empty($fecha_inicio)) {
                $errores[] = "La fecha de inicio no puede estar vacía.";
            }

            if (empty($fecha_fin)) {
                $errores[] = "La fecha de fin no puede estar vacía.";
            }

            if (!empty($fecha_inicio) && !empty($fecha_fin) && $fecha_inicio > $fecha_fin) {
                $errores[] = "La fecha de inicio no puede ser mayor que la fecha de fin.";
            }

            return $errores;
        }

        $errores = [];
        $resultados = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fecha_inicio = $_POST["fecha_inicio"];
            $fecha_fin = $_POST["fecha_fin"];

            $errores = validarFechas($fecha_inicio, $fecha_fin);

            if (empty($errores)) {
                // Conectar a la base de datos
                $servername = "localhost";
                $username = "grupog";
                $password = "2024#pfdf#8";
                $dbname = "proyectoequg";

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Verificar conexión
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Preparar y ejecutar la consulta
                $sql = "SELECT nombre_cliente, fecha, tipo_servicio, id_servicio, costo FROM costos WHERE fecha BETWEEN ? AND ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
                $stmt->execute();
                $result = $stmt->get_result();

                // Obtener resultados
                while ($row = $result->fetch_assoc()) {
                    $resultados[] = $row;
                }

                $stmt->close();
                $conn->close();
            }
        }
    ?>
</head>
<body>
    <nav>    
        <div class="imagen_logo">
            <img  src="./imagenes/logo.png" alt="img_logo">
        </div>
        <div class="logo_text"><a href="./vista_gerente.html">Plumber-Pro</a></div>
        <div class="imagen_notificacion">
            <img  src="./imagenes/notificacion.png" alt="img_notificacion">
        </div>
    </nav>

    <main>
        <h2>Reporte de Costos</h2>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errores)) {
                foreach ($errores as $error) {
                    echo "<p style='color: red;'>$error</p>";
                }
            }
        ?>
        <table class="reporte_costos">
            <thead>
                <tr>
                    <th>Nombre Cliente</th>
                    <th>Fecha</th>
                    <th>Tipo Servicio</th>
                    <th>ID Servicio</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!empty($resultados)) {
                        foreach ($resultados as $fila) {
                            echo "<tr>
                                    <td>{$fila['nombre_cliente']}</td>
                                    <td>{$fila['fecha']}</td>
                                    <td>{$fila['tipo_servicio']}</td>
                                    <td>{$fila['id_servicio']}</td>
                                    <td>{$fila['costo']}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay datos disponibles para el período seleccionado.</td></tr>";
                    }
                ?>
            </tbody>
        </table>

        <form class="formulario_reporte" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h3>Periodo de tiempo del reporte</h3>

            <div>
                <label for="fecha_inicio">Fecha inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div>
                <label for="fecha_fin">Fecha fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>

            <button type="submit">Generar Reporte</button>
        </form>
    </main>
</body>
</html>
