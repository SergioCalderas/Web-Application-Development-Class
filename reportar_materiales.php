<?php
$errores = [];
$exito = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_servicio = $_POST["id_servicio"] ?? '';
    $ids = $_POST["id_instrumental"] ?? [];
    $tipos = $_POST["tipo_instrumental"] ?? [];
    $cantidades = $_POST["cantidad"] ?? [];

    // Validación en el servidor
    if (empty($id_servicio)) {
        $errores[] = "Debe seleccionar un ID de servicio.";
    }

    $unique_ids = array_unique($ids);
    if (count($unique_ids) != count($ids)) {
        $errores[] = "Cada ID de instrumental debe ser único.";
    }

    foreach ($cantidades as $cantidad) {
        if ($cantidad < 1 || $cantidad > 10) {
            $errores[] = "La cantidad debe ser entre 1 y 10.";
        }
    }

    if (empty($errores)) {
        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="./estilos/estilos_vista.css">
    <link rel="stylesheet" href="./estilos/estilo_reportar_materiales.css">
   
    <title>Reportar Materiales Utilizados</title>
    <style>
        .table-container tr:hover {
            background-color: lightblue;
            cursor: pointer;
        }
        .selected {
            background-color: blue;
            color: white;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
    <script>
        const tiposInstrumental = {
            "001": "Equipo de protección personal (guantes, overol, gogles, botas)",
            "002": "Equipo de desinfección de tinaco (Solventes y recipientes)",
            "003": "Equipo de reparación de fugas (conectores, soldadura, cintas)",
            "004": "Equipo de instalación de calentador de agua",
            "005": "Consumibles generales"
        };

        function actualizarTipoInstrumental(selectElement) {
            const row = selectElement.parentNode.parentNode;
            const tipoInstrumentalCell = row.querySelector('.tipo-instrumental');
            const selectedId = selectElement.value;
            tipoInstrumentalCell.textContent = tiposInstrumental[selectedId] || '';
        }

        function agregarFila() {
            const tableBody = document.querySelector('table tbody');
            const rowCount = tableBody.rows.length;
            if (rowCount >= 5) {
                alert("No se pueden agregar más de 5 registros.");
                return;
            }

            const nuevaFila = `
                <tr>
                    <td>
                        <select name="id_instrumental[]" onchange="actualizarTipoInstrumental(this)" required>
                            <option value="" disabled selected>Seleccione ID</option>
                            <option value="001">001</option>
                            <option value="002">002</option>
                            <option value="003">003</option>
                            <option value="004">004</option>
                            <option value="005">005</option>
                        </select>
                    </td>
                    <td class="tipo-instrumental"></td>
                    <td><input type="number" name="cantidad[]" value="1" min="1" max="10" required></td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', nuevaFila);
        }

        function eliminarFila() {
            const tableBody = document.querySelector('table tbody');
            const rowCount = tableBody.rows.length;
            if (rowCount > 1) {
                tableBody.deleteRow(rowCount - 1);
            }
        }

        function validateForm() {
            const idServicio = document.getElementById('id_servicio').value;
            if (!idServicio) {
                alert("Debe seleccionar un ID de servicio.");
                return false;
            }

            const selectElements = document.querySelectorAll('select[name="id_instrumental[]"]');
            const selectedValues = [];
            for (let selectElement of selectElements) {
                const value = selectElement.value;
                if (selectedValues.includes(value)) {
                    alert("Cada ID de instrumental debe ser único.");
                    return false;
                }
                selectedValues.push(value);
            }
            return true;
        }
    </script>
</head>
<body>
    <nav>
        <img src="./imagenes/logo.png" alt="Plumber-Pro Logo" class="imagen_logo">
        <div class="logo_text"><a href="./vista_tecnico.php"><b>Plumber-Pro</b></a></div>
        <img src="./imagenes/notificacion.png" alt="Notificaciones" class="imagen_notificacion">
    </nav>
    </nav>
    <main class="content">
        <h1>Reportar Materiales Utilizados</h1>
        <p>*Seleccione los instrumentos utilizados en la jornada:</p>
        <form method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="id_servicio">ID Servicio</label>
                <select id="id_servicio" name="id_servicio" required>
                    <option value="" disabled selected>Seleccione ID Servicio</option>
                    <?php for ($i = 1; $i <= 100; $i++): ?>
                        <option value="<?php echo str_pad($i, 3, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i, 3, '0', STR_PAD_LEFT); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Instrumental</th>
                            <th>Tipo de Instrumental</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="id_instrumental[]" onchange="actualizarTipoInstrumental(this)" required>
                                    <option value="" disabled selected>Seleccione ID</option>
                                    <option value="001">001</option>
                                    <option value="002">002</option>
                                    <option value="003">003</option>
                                    <option value="004">004</option>
                                    <option value="005">005</option>
                                </select>
                            </td>
                            <td class="tipo-instrumental"></td>
                            <td><input type="number" name="cantidad[]" value="1" min="1" max="10" required></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="button-container">
                <button type="button" onclick="agregarFila()">Agregar Fila</button>
                <button type="button" onclick="eliminarFila()">Eliminar Fila</button>
                <button type="submit">Guardar y Enviar</button>
            </div>
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($exito) {
                echo '<p class="success">Datos válidos. Reporte de materiales enviado con éxito.</p>';
            } else {
                foreach ($errores as $error) {
                    echo '<p class="error">Error: ' . htmlspecialchars($error) . '</p>';
                }
            }
        }
        ?>
    </main>
</body>
</html>
