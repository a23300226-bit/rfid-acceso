<?php
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "control_acceso";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

if (isset($_GET['uid'])) {
    // 1. Limpiar espacios en blanco e invisibles
    $uid = trim($_GET['uid']);
    
    // 2. Forzar a que esté completamente en MAYÚSCULAS
    $uid = strtoupper($uid);
    
    // 3. Proteger contra Inyección SQL de forma segura sin usar preg_replace incorrectos
    $uid = $conn->real_escape_string($uid);
    
    // Consultar si el usuario existe y está activo
    $sql_usuario = "SELECT nombre, activo FROM usuarios WHERE uid = '$uid'";
    $resultado = $conn->query($sql_usuario);
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        if ($usuario['activo'] == 1) {
            // Registrar el evento en la tabla de accesos
            $sql_acceso = "INSERT INTO accesos (uid) VALUES ('$uid')";
            if ($conn->query($sql_acceso) === TRUE) {
                echo "ACCESO_CONCEDIDO";
            } else {
                echo "ERROR_TABLA_ACCESOS";
            }
        } else {
            echo "ACCESO_DENEGADO_INACTIVO";
        }
    } else {
        echo "TARJETA_NO_REGISTRADA";
    }
} else {
    echo "NO_SE_RECIBIO_UID";
}

$conn->close();
?>