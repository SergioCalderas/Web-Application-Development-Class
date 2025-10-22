<?php
session_start();

// Función para validar los datos del formulario de login
function validarDatos($correo, $contraseña) {
    $errores = [];

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["email"];
    $contraseña = $_POST["password"];

    $errores = validarDatos($correo, $contraseña);

    if (empty($errores)) {
        // Conectar a la base de datos
        $servername = "localhost";
        $username = "grupog";
        $password = "2024#pfdf#8";
        $dbname = "id22375966_webplumberpro"; 

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Consulta para verificar en la tabla Clientes
        $sql_clientes = "SELECT id_cliente, nombre FROM Clientes WHERE correo = ?";
        $stmt_clientes = $conn->prepare($sql_clientes);
        $stmt_clientes->bind_param("s", $correo);
        $stmt_clientes->execute();
        $result_clientes = $stmt_clientes->get_result();

        // Consulta para verificar en la tabla Tecnicos
        $sql_tecnicos = "SELECT id_tecnico, nombre FROM Tecnicos WHERE correo = ?";
        $stmt_tecnicos = $conn->prepare($sql_tecnicos);
        $stmt_tecnicos->bind_param("s", $correo);
        $stmt_tecnicos->execute();
        $result_tecnicos = $stmt_tecnicos->get_result();

        // Consulta para verificar en la tabla Administradores
        $sql_administradores = "SELECT id_administrador FROM Administradores WHERE correo = ?";
        $stmt_administradores = $conn->prepare($sql_administradores);
        $stmt_administradores->bind_param("s", $correo);
        $stmt_administradores->execute();
        $result_administradores = $stmt_administradores->get_result();

        // Verificar el tipo de usuario y redirigir
        if ($result_clientes->num_rows > 0) {
            // Es cliente
            $row = $result_clientes->fetch_assoc();
            $_SESSION['user_id'] = $row['id_cliente']; // Guardar el ID del cliente en la sesión
            $_SESSION['user_name'] = $row['nombre']; // Guardar el nombre del cliente en la sesión
            echo '<script>window.location.href = "./vista_cliente.php";</script>';
            exit();
        } elseif ($result_tecnicos->num_rows > 0) {
            // Es técnico
            $row = $result_tecnicos->fetch_assoc();
            $_SESSION['user_id'] = $row['id_tecnico']; // Guardar el ID del técnico en la sesión
            $_SESSION['user_name'] = $row['nombre']; // Guardar el nombre del técnico en la sesión
            echo '<script>window.location.href = "./vista_tecnico.php";</script>';
            exit();
        } elseif ($result_administradores->num_rows > 0) {
            // Es administrador
            $row = $result_administradores->fetch_assoc();
            $_SESSION['user_id'] = $row['id_administrador']; // Guardar el ID del administrador en la sesión
            echo '<script>window.location.href = "./vista_gerente.html";</script>';
            exit();
        } else {
            // Usuario no encontrado en ninguna tabla
            $errores[] = "Usuario no encontrado.";
        }

        // Cerrar conexiones y liberar recursos
        $stmt_clientes->close();
        $stmt_tecnicos->close();
        $stmt_administradores->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <title>Login</title>
</head>
<body>

    <main>
        <div class="contenedor_todo">
            <h1>Plumber-Pro</h1>
            <!-- Formulario Login -->
            <form class="formulario_login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <h2>Iniciar Sesión</h2>
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errores)) {
                        foreach ($errores as $error) {
                            echo "<p style='color: red;'>$error</p>";
                        }
                    }
                ?>
                <input type="email" name="email" placeholder="Correo Electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Ingresar</button>
                <button type="button" onclick="window.location.href='./crear_cuenta.php'">Crear cuenta</button>
            </form>
        </div>
        <div class="imagen_logo">
            <img src="./imagenes/logo.png" alt="imagen_logo">
        </div>
    </main>
    
</body>
</html>