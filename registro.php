<?php
// 1. Datos de conexión a la base de datos de Aiven
$servername = "mysql-89e2927-ceti-41ee.k.aivencloud.com";
$username = "avnadmin";
$password = "AVNS_6b5wucqdsPNyp8H1dYq"; 
$dbname = "rfid-accesos"; // Tu base de datos manual confirmada
$port = 10714;

// 2. Inicializar la extensión mysqli
$conn = mysqli_init();

if (!$conn) {
    die("Fallo en mysqli_init: " . mysqli_connect_error());
}

// 3. Configurar la conexión para ignorar la verificación de certificado SSL
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// 4. Realizar la conexión segura
$res = mysqli_real_connect(
    $conn, 
    $servername, 
    $username, 
    $password, 
    $dbname, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
);

if (!$res) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 5. AUTO-CREACIÓN DE TABLA
$tabla_sql = "CREATE TABLE IF NOT EXISTS accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $tabla_sql);

// 6. Procesar la petición del Arduino / ESP8266
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    $uid = mysqli_real_escape_string($conn, $uid);
    
    $sql = "INSERT INTO accesos (uid, fecha_hora) VALUES ('$uid', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        echo "ACCESO_REGISTRADO_OK";
    } else {
        echo "ERROR_AL_GUARDAR: " . mysqli_error($conn);
    }
    
} else {
    echo "ERROR: No se recibio ningun UID. El script funciona correctamente.";
}

mysqli_close($conn);
?>
