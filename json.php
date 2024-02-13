<?php
include("config.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$ip_client = $_SERVER['REMOTE_ADDR'];
$ip_client = "95.111.233.149";


if( isset( $_GET['ip'])) {
    $ip_test = $_GET['ip'];
}
else
{
    $ip_test = $ip_client;
}

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

    $sql = "SELECT * FROM speedtest  WHERE st_date <= CURTIME( ) AND st_date >= DATE_SUB( CURTIME( ), INTERVAL 24 HOUR ) AND st_ip = '" . $ip_test . "'";
    //echo $sql;
    if (strlen($sql) > 5){
        $result = $conn->query($sql);
        $dataCount = mysqli_num_rows($result);
        $rawdata = array();
    
        $i=0;
            while($row = mysqli_fetch_assoc($result))
            {
                $rawdata[$i] = $row;
                $i++;
            }
            echo json_encode($rawdata);
    
        $conn->close();
    }
    ?>