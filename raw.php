<?php
include("config.php");

if (empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$datos = $_GET['id'];
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$datos = str_replace("p", ".", $datos);
$parametros = explode("-", $datos);

if (count($parametros) < 3) {
    // Datos insuficientes
    http_response_code(400);
    echo "Error: Datos incompletos.";
    exit();
}

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Error: Fallo en la conexión a la base de datos.";
    exit();
}
mysqli_set_charset($conn, 'utf8');

$st_ip = $_SERVER['REMOTE_ADDR'];
$st_ping = $parametros[0];
$st_down = $parametros[1];
$st_up = $parametros[2];
$st_date = date("Y-m-d H:i:s");

// --- INSERT usando sentencias preparadas ---
$sql_insert = "INSERT INTO speedtest (st_ip, st_ping, st_down, st_up, st_date) VALUES (?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
if ($stmt_insert) {
    $stmt_insert->bind_param("sddss", $st_ip, $st_ping, $st_down, $st_up, $st_date);
    $stmt_insert->execute();
    $stmt_insert->close();
} else {
    // Error en la preparación de la consulta
    http_response_code(500);
    echo "Error: Fallo en la inserción de datos.";
    $conn->close();
    exit();
}


// --- Lógica para actualizar la IP basada en el alias ---
if (isset($parametros[3])) {
    $alias = $parametros[3];
    $ip_id = null;
    $ip_db = null;

    // --- SELECT usando sentencias preparadas ---
    $sql_select = "SELECT ip_id, ip_number FROM ips WHERE ip_alias = ?";
    $stmt_select = $conn->prepare($sql_select);
    if ($stmt_select) {
        $stmt_select->bind_param("s", $alias);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($row = $result->fetch_assoc()) {
            $ip_id = $row["ip_id"];
            $ip_db = $row["ip_number"];
        }
        $stmt_select->close();
    }

    // Si la IP ha cambiado, se actualiza
    if ($ip_id && $ip_db != $st_ip) {
        // --- UPDATE usando sentencias preparadas ---
        $sql_update = "UPDATE ips SET ip_number = ? WHERE ip_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("si", $st_ip, $ip_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }
}

$conn->close();

echo "OK";
?>

