<?php
include("config.php");
include("data_logic.php");

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
mysqli_set_charset($conn, 'utf8');

// Obtener todos los datos usando la función centralizada
$speedtestData = getSpeedtestData($conn);

// Extraer solo la lista necesaria para el JSON
$data_list = $speedtestData['data_list_for_json'];

$conn->close();

// Imprimir el JSON
echo json_encode($data_list);
?>
