<?php
session_start();

// Mensaje de bienvenida con el nombre del técnico
if (isset($_SESSION['user_name'])) {
    $mensaje_bienvenida = "¡Bienvenido Técnico " . $_SESSION['user_name'] . "!";
} else {
    $mensaje_bienvenida = "¡Bienvenido Técnico!";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <title>Vista Técnico</title>
</head>
<body>
    <nav>
        <div class="imagen_logo">
            <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
        </div>
        <div class="logo_text"><a href="./vista_tecnico.php"><b>Plumber-Pro</b></a></div>
        <div class="imagen_notificacion">
            <img src="./imagenes/notificacion.png" alt="Notificaciones" class="imagen_notificacion">
        </div>
    </nav>
    <main>
        <h2><?php echo $mensaje_bienvenida; ?></h2>
        <div class="botones_Gerente">
            <form name="botonSeguirServicio" action="./seguir_servicio.php">
                <input type="submit" name="enviarDatos" value="Seguir Servicios">
            </form>
            <form name="botonNotasServicios" action="./notas_servicios.php">
                <input type="submit" name="enviarDatos" value="Notas Servicios">
            </form>
        </div> 
    </main> 
</body>
</html>
