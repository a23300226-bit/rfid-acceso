<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "control_acceso";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Error: " . $conn->connect_error); }

$sql = "SELECT a.id, a.uid, u.nombre, a.fecha 
        FROM accesos a 
        INNER JOIN usuarios u ON a.uid = u.uid 
        ORDER BY a.fecha DESC LIMIT 20"; 
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel RFID</title>
    <link rel="stylesheet" href="CSS/style.css">
    <meta http-equiv="refresh" content="5">
</head>

<body>

    <div class="contenedor">
        <header class="encabezado">
            <h1>Monitoreo de Acceso IoT</h1>
            <p>Historial de tarjetas leídas en tiempo real</p>
        </header>

        <main class="tabla-contenedor">
            <table class="tabla-accesos">
                <thead>
                    <tr>
                        <th>ID Log</th>
                        <th>UID Tarjeta</th>
                        <th>Usuario</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while($fila = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $fila['id'] . "</td>";
                            echo "<td><span class='codigo-uid'>" . $fila['uid'] . "</span></td>";
                            echo "<td class='nombre-usuario'>" . $fila['nombre'] . "</td>";
                            echo "<td class='fecha-registro'>" . date('d/m/Y h:i:s A', strtotime($fila['fecha'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='sin-datos'>No hay registros todavía. Pasa una tarjeta.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <?php $conn->close(); ?>
</body>

</html>