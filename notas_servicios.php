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
    $sql = "SELECT id_servicio, direccion, codigo_postal, tipo_servicio, fecha, hora, estado_servicio 
            FROM Servicios 
            WHERE id_tecnico = ? 
              AND (estado_servicio = 'Terminado' OR estado_servicio = 'Pagado')
            ORDER BY fecha DESC";
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

function contarServiciosPorEstado($id_tecnico, $estado) {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM Servicios WHERE id_tecnico = ? AND estado_servicio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_tecnico, $estado);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();
    return $count;
}

$id_tecnico = $_SESSION['user_id']; // Obtener el id_tecnico desde la sesión

$servicios = obtenerServiciosPorTecnico($id_tecnico);
$servicios_activos = contarServiciosPorEstado($id_tecnico, 'Activo');
$servicios_terminados = contarServiciosPorEstado($id_tecnico, 'Terminado');
$servicios_pagados = contarServiciosPorEstado($id_tecnico, 'Pagado');

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <link rel="stylesheet" href="./estilos/estilo_notas_servicios.css">
    <title>Registro de Notas</title>
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
    <main class="container">
        <div class="left-section">
            <h2>Notas anteriores</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Servicio</th>
                            <th>Dirección</th>
                            <th>Código Postal</th>
                            <th>Tipo Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $servicio): ?>
                        <tr class="fila-servicio" data-id="<?php echo htmlspecialchars($servicio['id_servicio']); ?>">
                            <td><?php echo htmlspecialchars($servicio['id_servicio']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['direccion']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['codigo_postal']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['tipo_servicio']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['hora']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['estado_servicio']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="right-section">
            <h2>Notas del día <?php echo date('Y-m-d'); ?></h2>
            <div class="right-content">
                <div class="services-summary">
                    <h3>Recibo de Servicios</h3>
                    <p>Servicios activos: <span id="active-services"><?php echo $servicios_activos; ?></span></p>
                    <p>Servicios terminados: <span id="completed-services"><?php echo $servicios_terminados; ?></span></p>
                    <p>Servicios pagados: <span id="paid-services"><?php echo $servicios_pagados; ?></span></p>
                </div>
                <div class="refactions-voucher">
                    <h3>Vale de refacciones</h3>
                    <p id="materiales">Seleccione un servicio para ver los materiales utilizados.</p>
                </div>
            </div>
        </div>
    </main>
    <script>
        function seleccionarFila(id_servicio) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "obtener_materiales.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("materiales").innerHTML = xhr.responseText;
                }
            };
            xhr.send("id_servicio=" + id_servicio);
        }

        document.addEventListener("DOMContentLoaded", function() {
            var filas = document.querySelectorAll(".fila-servicio");
            filas.forEach(function(fila) {
                fila.addEventListener("click", function() {
                    // Eliminar la clase 'selected' de todas las filas
                    filas.forEach(f => f.classList.remove('selected'));
                    // Agregar la clase 'selected' a la fila actual
                    fila.classList.add('selected');
                    seleccionarFila(fila.getAttribute("data-id"));
                });
            });
        });
    </script>
    <style>
        .selected {
            background-color: #f0f0f0;
        }
    </style>
</body>
</html>
