<?php
session_start();

// Mensaje de bienvenida con el nombre del cliente
if (isset($_SESSION['user_name'])) {
    $mensaje_bienvenida = "Bienvenido Cliente " . $_SESSION['user_name'] . "!";
} else {
    $mensaje_bienvenida = "Bienvenido Cliente!";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <title>Inicio Cliente</title>
</head>
<body>
    <nav>
        <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
        <div class="logo_text"><a href="./vista_cliente.php"><b>Plumber-Pro</b></a></div>
        <img src="./imagenes/notificacion.png" alt="Notificaciones" class="imagen_notificacion">
    </nav>
    <main>
        <h2><?php echo $mensaje_bienvenida; ?></h2>
        <div class="botones_Cliente">
            <form name="botonSolicitarServicio" action="./solicitar_servicio.php">
                <input type="submit" name="enviarDatos" value="Solicitar Servicios">
            </form>
            <form name="botonPagarServicio" action="./pagar_servicio.php">
                <input type="submit" name="enviarDatos" value="Pagar Servicios">
            </form>
        </div>
    </main>
</body>
</html>
