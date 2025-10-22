<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <title>Registrar Empleados</title>
    <?php
        // Función para validar los datos del formulario de registro de empleados
        function validarDatos($nombre, $apellido, $seccion, $horario, $correo, $contraseña) {
            $errores = [];

            // Validar nombre
            if (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
                $errores[] = "El nombre solo debe contener letras y espacios.";
            }

            // Validar apellido
            if (!preg_match("/^[a-zA-Z\s]+$/", $apellido)) {
                $errores[] = "Los apellidos solo deben contener letras y espacios.";
            }

            // Validar sección
            if (!filter_var($seccion, FILTER_VALIDATE_INT)) {
                $errores[] = "La sección debe ser un número.";
            }

            // Validar horario
            if (empty($horario)) {
                $errores[] = "El horario no puede estar vacío.";
            }

            // Validar correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El correo no es válido.";
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
        $apellido = $_POST["apellido"];
        $seccion = $_POST["seccion"];
        $horario = $_POST["horario"];
        $correo = $_POST["email"];
        $contraseña = $_POST["contraseña"];
    
        $errores = validarDatos($nombre, $apellido, $seccion, $horario, $correo, $contraseña);
    
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
            $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Tecnicos (nombre, apellido, seccion, horario_trabajo, correo, contraseña) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
    
            // Verificar si la preparación fue exitosa
            if ($stmt === false) {
                die('Error de preparación SQL: ' . $conn->error);
            }
    
            // Enlazar parámetros
            $stmt->bind_param("ssisss", $nombre, $apellido, $seccion, $horario, $correo, $hashed_password);
    
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $exito = true;
            } else {
                $errores[] = "Error al guardar los datos: " . $stmt->error;
            }
    
            // Cerrar la declaración y la conexión
            $stmt->close();
            $conn->close();
        }
    }
    ?>
</head>
<body>
    <nav>    
        <div class="imagen_logo">
            <img src="./imagenes/logo.png" alt="img_logo">
        </div>
        <div class="logo_text"><a href="./vista_gerente.html"><b>Plumber-Pro</b></a></div>
        <div class="imagen_notificacion">
            <img src="./imagenes/notificacion.png" alt="img_notificacion">
        </div>
    </nav>

    <main>
        <img class="imagenes_fondo" src="./imagenes/partnes.png" alt="partnes">
        <h2>Registrar Empleado</h2>
        <div class="registrar">
            <div class="registrar_tipo">
                Datos Empleado
            </div>
            <!-- Formulario para registrar empleado (técnico) -->
            <form class="formulario_registro" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if ($exito) {
                            echo "<p>Empleado registrado exitosamente</p>";
                        } else {
                            foreach ($errores as $error) {
                                echo "<p style='color: red;'>Error: $error</p>";
                            }
                        }
                    }
                ?>
                <div>
                    <label for="nombre">Nombre(s):</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div>
                    <label for="apellido">Apellidos: </label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div>
                    <label for="seccion">Sección:</label>
                    <input type="number" id="seccion" name="seccion" required>
                </div>
                <div>
                    <label for="horario">Horario:</label>
                    <input type="text" id="horario" name="horario" required>
                </div>
                <div>
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>

                <button type="submit">Guardar</button>
            </form>
        </div>
    </main>
</body>
</html>