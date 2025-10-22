<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <link rel="stylesheet" href="./estilos/style.css">
   
    <Title>Controlar almacen</Title>
</head>
<body>
<h2>Controlar almacen</h2>
<main>
    <nav>    
        <div class="imagen_logo">
            <img src="./imagenes/logo.png" alt="img_logo" class="imagen_logo">
        </div>
        <div class="logo_text"><a href="./vista_gerente.hmtl">Plumber-Pro</a></div>
        <div class="imagen_notificacion">
            <img src="./imagenes/notificacion.png" alt="img_notificacion" class="imagen_notificacion">
        </div>
    </nav>

    <div class="container">
       
        <table>
            <tr>
                <th>Nombre Material</th>
                <th>Tipo de instrumental asociado</th>
                <th>Costo Unitario</th>
                <th>Cantidad</th>
            </tr>
            <?php
                    $rep_materiales = [
                        ['id_material' => 'material 1', 'tipoinstrumental' => 'Tipo', 'costoUnitario' => '[$$$]', 'Cantidad' => '#'],
                        ['id_material' => 'material 2', 'tipoinstrumental' => 'Tipo', 'costoUnitario' => '[$$$]', 'Cantidad' => '#'],
                        ['id_material' => 'material 3', 'tipoinstrumental' => 'Tipo', 'costoUnitario' => '[$$$]', 'Cantidad' => '#'],
                        ['id_material' => 'material 4', 'tipoinstrumental' => 'Tipo', 'costoUnitario' => '[$$$]', 'Cantidad' => '#'],
                        ['id_material' => 'material 5', 'tipoinstrumental' => 'Tipo', 'costoUnitario' => '[$$$]', 'Cantidad' => '#'],
                        ['id_material' => 'material 6', 'tipoinstrumental' => 'Tipo', 'costoUnitario' => '[$$$]', 'Cantidad' => '#'],
                    ];

                    foreach ($rep_materiales as $mats) {
                        echo "<tr>";
                        echo "<td>{$mats['id_material']}</td>";
                        echo "<td>{$mats['tipoinstrumental']}</td>";
                        echo "<td>{$mats['costoUnitario']}</td>";
                        echo "<td>{$mats['Cantidad']}</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
            
        </div>
    
    </main>
    <div id="bajoLaTabla">
        <div id="costo_total_rep_mats">
                    <h3>Costo total acumulado:   [$] </h3>            
        </div>
        <button type="submit">Ver carrito de compra</button>
    </div>
    </body>
</html>