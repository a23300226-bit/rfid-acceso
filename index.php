<?php
// 1. Datos de conexión a la base de datos de Aiven
$servername = "mysql-89e2927-ceti-41ee.k.aivencloud.com";
$username = "avnadmin";
$password = "AVNS_6b5wucqdsPNyp8H1dYq"; 
$dbname = "defaultdb"; // Si creaste una base de datos llamada 'rfid-accesos' en el panel de Aiven, cámbialo aquí.
$port = 10714;

// 2. Inicializar y conectar con SSL obligatorio
$conn = mysqli_init();
if (!$conn) { 
    die("Fallo en mysqli_init: " . mysqli_connect_error()); 
}

mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
$res = mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);

if (!$res) { 
    die("Error de conexión: " . mysqli_connect_error()); 
}

// 3. ASEGURAR QUE LAS TABLAS EXISTAN (Auto-creación preventiva)
// Tabla de usuarios
$tabla_u = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    uid VARCHAR(50) NOT NULL UNIQUE
)";
mysqli_query($conn, $tabla_u);

// Tabla de accesos (con la columna fecha_hora)
$tabla_a = "CREATE TABLE IF NOT EXISTS accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $tabla_a);

// Insertar un usuario de prueba por si la tabla está vacía
$check_u = mysqli_query($conn, "SELECT id FROM usuarios LIMIT 1");
if (mysqli_num_rows($check_u) == 0) {
    mysqli_query($conn, "INSERT INTO usuarios (nombre, uid) VALUES ('Usuario de Prueba', 'D7117F25')");
}

// 4. CONSULTA CORREGIDA
// Usamos LEFT JOIN para incluir tarjetas leídas que aún no estén asignadas a un usuario en específico
$sql = "SELECT a.id, a.uid, IFNULL(u.nombre, 'Tarjeta No Registrada') AS nombre, a.fecha_hora 
        FROM accesos a 
        LEFT JOIN usuarios u ON a.uid = u.uid 
        ORDER BY a.fecha_hora DESC LIMIT 20"; 

$resultado = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel RFID</title>
    <link rel="stylesheet" href="CSS/style.css">
    <meta http-equiv="refresh" content="5"> <!-- Se actualiza cada 5 segundos para ver los datos en tiempo real -->
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
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        while($fila = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>";
                            echo "<td>#" . $fila['id'] . "</td>";
                            echo "<td><span class='codigo-uid'>" . $fila['uid'] . "</span></td>";
                            echo "<td class='nombre-usuario'>" . $fila['nombre'] . "</td>";
                            echo "<td class='fecha-registro'>" . date('d/m/Y h:i:s A', strtotime($fila['fecha_hora'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='sin-datos'>No hay registros todavía. Pasa una tarjeta por el lector.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <?php mysqli_close($conn); ?>
</body>

</html>
