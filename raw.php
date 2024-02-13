<?php
include("config.php");
if(empty($_GET['id']))
{
    header('Location: index.php');
}else{
    $datos = $_GET['id'];
}


mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$datos = str_replace("p",".",$datos);

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$parametros = explode("-",$datos);

$st_ip = $_SERVER['REMOTE_ADDR'];
$st_ping = $parametros[0];
$st_down = $parametros[1];
$st_up = $parametros[2];
$st_date = date("Y-m-d H:i:s");

$sql = "INSERT INTO speedtest (st_ip,st_ping,st_down,st_up,st_date) VALUES ('" . $st_ip . "', " . $st_ping . ", " . $st_down . ", " . $st_up . ", '" . $st_date . "')";
$result = $conn->query($sql);
if($parametros[3]){
  $sql = "SELECT * FROM ips WHERE ip_alias = '$parametros[3]' ";
  $result = $conn->query($sql);
  if (mysqli_num_rows($result) == true) {
    while($row = $result->fetch_assoc())
      {
        $ip_id = $row["ip_id"];
        $ip_db = $row["ip_number"];
      }
    }
    if($ip_db != $st_ip){
      $sql = "UPDATE ips SET ip_number = '$st_ip' WHERE ip_id = $ip_id";
      $result = $conn->query($sql);

    }

}
$conn->close();

echo "OK";
?>

