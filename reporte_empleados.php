<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <link rel="stylesheet" href="./estilos/style.css">
    <title>Reporte de Empleados</title>
</head>
<body>
<h2>Reporte de Empleados</h2>
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
    <table>
        <tr>
            <th>Nombre Técnico</th>
            <th>ID Servicio</th>
            <th>Hora Inicio</th>
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

        // Consulta para obtener los servicios y técnicos relacionados
        $query = "
            SELECT s.id_servicio, s.id_tecnico, s.hora, t.nombre AS nombre_tecnico
            FROM Servicios s
            JOIN Tecnicos t ON s.id_tecnico = t.id_tecnico
        ";

        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['nombre_tecnico']}</td>";
                echo "<td>{$row['id_servicio']}</td>";
                echo "<td>{$row['hora']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No se encontraron resultados</td></tr>";
        }

        // Cerrar la conexión
        $conn->close();
        ?>
    </table>
</div>
</main>
</body>
</html>
