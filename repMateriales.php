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
                
                $conn = pg_connect("host=localhost dbname=id22375966_webplumberpro user=tu_usuario password=tu_contraseña");

               
                $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
                $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

                if (isset($_POST['limpiar_fechas'])) {
                    $fecha_inicio = $fecha_fin = null;
                }

                $query = "SELECT id_servicio, tipo_servicio, fecha FROM Servicios";
                if ($fecha_inicio && $fecha_fin) {
                    $query .= " WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                }
                $result = pg_query($conn, $query);

                if ($result) {
                    while ($row = pg_fetch_assoc($result)) {
                        $precio = calcular_precio($row['tipo_servicio']);
                        echo "<tr>";
                        echo "<td>{$row['id_servicio']}</td>";
                        echo "<td>{$row['tipo_servicio']}</td>";
                        echo "<td>{$row['fecha']}</td>";
                        echo "<td>{$precio}</td>";
                        echo "</tr>";
                    }
                }

           
                function calcular_precio($tipo_servicio) {
                    switch ($tipo_servicio) {
                        case 'Tipo 1':
                            return '$100';
                        case 'Tipo 2':
                            return '$150';
                        case 'Tipo 3':
                            return '$200';
                        default:
                            return 'N/A';
                    }
                }

            
                pg_close($conn);
                ?>
            </table>
        </div>
        <div id="bajoLaTabla">
            <div id="costo_total_rep_mats">
                <h3>Costo total de la orden:  [$$$]</h3>
            </div>
            <button type="submit">Enviar orden</button>
            <button type="button">Regresar al almacén</button>
        </div>
    </main>
</body>
</html>
