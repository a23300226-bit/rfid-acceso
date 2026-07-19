<?php
// 1. Datos de conexión a la base de datos de Aiven
$servername = "mysql-89e2927-ceti-41ee.k.aivencloud.com";
$username = "avnadmin";
$password = "AVNS_6b5wucqdsPNyp8H1dYq"; 
$dbname = "defaultdb"; 
$port = 10714;

// 2. Inicializar la extensión mysqli
$conn = mysqli_init();

if (!$conn) {
    die("Fallo en mysqli_init: " . mysqli_connect_error());
}

// 3. Configurar la conexión para ignorar la verificación estricta del certificado SSL local en Render
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// 4. Realizar la conexión segura incluyendo el puerto y la bandera SSL
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

// 5. Procesar la petición del Arduino
// Verificamos si llegó el parámetro 'uid' por la URL (ej: registro.php?uid=D7117F25)
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    
    // Limpiamos la variable para evitar inyecciones SQL básicas
    $uid = mysqli_real_escape_string($conn, $uid);
    
    // Insertamos la lectura en la tabla de accesos
    // NOTA: Asegúrate de que tu tabla en Aiven se llame 'accesos' y tenga las columnas 'uid' y 'fecha_hora'
    $sql = "INSERT INTO accesos (uid, fecha_hora) VALUES ('$uid', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        echo "ACCESO_REGISTRADO_OK";
    } else {
        echo "ERROR_AL_GUARDAR: " . mysqli_error($conn);
    }
    
} else {
    echo "ERROR: No se recibio ningun UID.";
}

// 6. Cerrar la conexión
mysqli_close($conn);
?>
