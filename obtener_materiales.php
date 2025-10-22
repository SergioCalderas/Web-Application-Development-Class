<?php
$servername = "localhost";
$username = "grupog";
$password = "2024#pfdf#8";
$dbname = "proyectoequg";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_servicio = intval($_POST['id_servicio']);

    $sql = "SELECT tipo_servicio FROM Servicios WHERE id_servicio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_servicio);
    $stmt->execute();
    $result = $stmt->get_result();
    $tipo_servicio = $result->fetch_assoc()['tipo_servicio'];
    $stmt->close();

    $materiales = [];
    if ($tipo_servicio === 'LimpiezaTinaco') {
        $materiales = ['Filtro de tinaco', '1 litro de solución sanitizante antibacterial', '1 Cepillo con extensor'];
    } elseif ($tipo_servicio === 'FugaGas') {
        $materiales = ['3 metros de tubo de cobre de 1/2 pulgada', '5 codos de 1/2 pulgada', '2 metros de soldadura', '1 tubo de gas butano de 1/2 litro'];
    } elseif ($tipo_servicio === 'CambioFiltro') {
        $materiales = ['1 kit de mangueras de agua caliente, fria y gas', '1 rollo de cinta teflón', '2 valvulas de presión inversa de 1/2 pulgada'];
    }

    foreach ($materiales as $material) {
        echo "<p>$material</p>";
    }
}

$conn->close();
?>
