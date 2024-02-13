<?php
include("config.php");
$ip_client = $_SERVER['REMOTE_ADDR'];

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header("Content-Type: text/plain");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

if( isset( $_GET['ip'])) {
    $ip_test = $_GET['ip'];
}
else
{
    $ip_test = $ip_client;
}
    $sql = "SELECT speedtest.st_date,ips.ip_name,speedtest.st_ping,speedtest.st_down,speedtest.st_up,speedtest.st_ip FROM speedtest
    INNER JOIN ips ON speedtest.st_ip = ips.ip_number
    WHERE speedtest.st_ip = '" . $ip_test . "' ORDER BY speedtest.st_date DESC LIMIT 1"; 

    $result = $conn->query($sql);

    if (mysqli_num_rows($result) == true) {
        while($row = $result->fetch_assoc())
            {
                $last_report = $row["st_date"];
                $st_ip = $row["st_ip"];
                $ip_name = $row["ip_name"];
                $st_ping = $row["st_ping"];
                $st_down = $row["st_down"];
                $st_up = $row["st_up"];
                $ip_name = $ip_name . "(" . $st_ip .")";
                echo "Server: " . $ip_name."\n";
                echo "Last Date: " . $last_report."\n";
                echo "Ping: " . $st_ping ." ms\n";
                echo "Download: " . $st_down ." Mbits/s\n";
                echo "Upload: " . $st_up ." Mbits/s\n";
            } 
        }
?>   
