<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/style.css">
    <title>Pedido de Reposición de Materiales</title>
</head>
<body>
    <h2>Pedido de Reposición de Materiales</h2>
    <main>
    <nav>    
        <div class="imagen_logo">
            <img src="./imagenes/logo.png" alt="img_logo" class="imagen_logo">
        </div>
        <div class="logo_text"><a href="./vista_gerente.html">Plumber-Pro</a></div>
        <div class="imagen_notificacion">
            <img src="./imagenes/notificacion.png" alt="img_notificacion" class="imagen_notificacion">
        </div>
    </nav>
        <div class="container">
            <form method="post" action="">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin">
                <button type="submit" name="generar_reporte">Generar Reporte</button>
                <button type="submit" name="limpiar_fechas">Limpiar Fechas</button>
            </form>
            <table>
                <tr>
                    <th>ID Servicio</th>
                    <th>Tipo de Servicio</th>
                    <th>Fecha</th>
                    <th>Precio</th>
                </tr>
                <?php
                $servername = "localhost";
                $username = "grupog";
                $password = "2024#pfdf#8";
                $dbname = "proyectoequg";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Manejo del formulario
                $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
                $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

                if (isset($_POST['limpiar_fechas'])) {
                    $fecha_inicio = $fecha_fin = null;
                }

                $query = "SELECT id_servicio, tipo_servicio, fecha FROM Servicios WHERE estado_servicio = 'Terminado' OR estado_servicio = 'Pagado'";
                if ($fecha_inicio && $fecha_fin) {
                    $query .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                }

                $result = $conn->query($query);
                $costo_total = 0;  // Inicializar el costo total

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $precio = calcular_precio($row['tipo_servicio']);
                        // Quitar el símbolo de dólar y convertir a número
                        $costo_total += floatval(str_replace('$', '', $precio));
                        echo "<tr>";
                        echo "<td>{$row['id_servicio']}</td>";
                        echo "<td>{$row['tipo_servicio']}</td>";
                        echo "<td>{$row['fecha']}</td>";
                        echo "<td>{$precio}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No se encontraron resultados</td></tr>";
                }

                // Función para calcular el precio basado en el tipo de servicio
                function calcular_precio($tipo_servicio) {
                    switch ($tipo_servicio) {
                        case 'CambioFiltro':
                            return '$100';
                        case 'LimpiezaTinaco':
                            return '$150';
                        case 'FugaGas':
                            return '$200';
                        default:
                            return 'N/A';
                    }
                }

                // Cerrar la conexión
                $conn->close();
                ?>
            </table>
        </div>
        <div id="bajoLaTabla">
            <div id="costo_total_rep_mats">
                <h3>Costo total de la orden:  $<?php echo number_format($costo_total, 2); ?></h3>
            </div>
            <button type="button" onclick="location.href='./vista_gerente.html'">Menú principal</button>
        </div>
    </main>
</body>
</html>
