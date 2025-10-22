<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>
    <link rel="stylesheet" href="./estilos/estiloCliente.css">

    <?php
        function validarDatos($nombre, $correo, $apellidos, $contraseña) {
            $errores = [];

            // Validar nombre
            if (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
                $errores[] = "El nombre solo debe contener letras y espacios.";
            }

            // Validar correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El correo no es válido.";
            }

            // Validar apellidos
            if (!preg_match("/^[a-zA-Z\s]+$/", $apellidos)) {
                $errores[] = "Los apellidos solo deben contener letras y espacios.";
            }

            // Validar contraseña
            if (empty($contraseña)) {
                $errores[] = "La contraseña no puede estar vacía.";
            }

            return $errores;
        }

        $errores = [];
        $exito = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $apellidos = $_POST["apellidos"];
            $contraseña = $_POST["contraseña"];

            $errores = validarDatos($nombre, $correo, $apellidos, $contraseña);

            if (empty($errores)) {
                // Conexión a la base de datos
                $servername = "localhost"; // Cambia esto si tu base de datos no está en el mismo servidor
                $username = "grupog";
                $password = "2024#pfdf#8";
                $dbname = "proyectoequg";

                // Crear conexión
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Verificar conexión
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Preparar y enlazar la consulta
                $stmt = $conn->prepare("INSERT INTO Clientes (nombre, apellido, correo, contraseña) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nombre, $apellidos, $correo, $contraseña);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    $exito = true;
                } else {
                    $errores[] = "Error al guardar los datos: " . $stmt->error;
                }

                // Cerrar la conexión
                $stmt->close();
                $conn->close();
            }
        }
    ?>
</head>
<body>
    <nav>
        <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
        <div class="logo_text"><a href="./index.html"><b>Plumber-Pro</b></a></div>
    </nav>
    <main>
        <div class="contenedor_todo">
            <h1>Plumber-Pro</h1>
            <h1>Crear Cuenta</h1>

            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if ($exito) {
                        echo "<p>Nuevo Usuario Registrado!!!.</p>";
                    } else {
                        foreach ($errores as $error) {
                            echo "<p>Error: $error</p>";
                        }
                    }
                }
            ?>

            <div class="registrar">
                <form action="" method="post">
                    <div>
                        <label for="nombre">* Nombre(s):</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div>
                        <label for="correo">* Correo:</label>
                        <input type="email" id="correo" name="correo" required>
                    </div>
                    <div>
                        <label for="apellidos">* Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" required>
                    </div>
                    <div>
                        <label for="contraseña">* Contraseña:</label>
                        <input type="password" id="contraseña" name="contraseña" required>
                    </div>
                    <button type="submit">Guardar</button>
                    <button type="button" onclick="window.location.href='./index.php'">Cancelar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
