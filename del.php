<?php
include("config.php");
$ip_del = $_GET['id'];

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$sql = "DELETE FROM speedtest where st_ip = '$ip_del'";
$result = $conn->query($sql);
$conn->close();
header("Location: index.php");
?>