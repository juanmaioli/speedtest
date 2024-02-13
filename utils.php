<?php
include("config.php");
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$st_date_diff = new DateTime(date("Y-m-d H:i:s"));

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$st_ip_list= array();
// $sql="SELECT DISTINCT speedtest.st_ip FROM speedtest ";
$sql="SELECT DISTINCT  speedtest.st_ip FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE ips.ip_delete = 0 GROUP BY speedtest.st_ip ORDER BY ips.ip_name ASC";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
    while($row = $result->fetch_assoc())
        {
            array_push($st_ip_list ,$row["st_ip"] );
        }
    }
$data_last="[";
foreach ($st_ip_list as &$st_ip_list_ip) {
    $sql="SELECT speedtest.st_ip,speedtest.st_date,ips.ip_name
    FROM speedtest
    INNER JOIN ips ON speedtest.st_ip = ips.ip_number
    WHERE speedtest.st_ip = '$st_ip_list_ip'
    ORDER BY speedtest.st_id DESC LIMIT 1";
    $result = $conn->query($sql);
    $total_lineas = $result->num_rows;
    if (mysqli_num_rows($result) == true) {
    while($row = $result->fetch_assoc())
        {
            $st_ip  = $row["st_ip"];
            $st_date  = $row["st_date"];
            $ip_name  = $row["ip_name"];
            $st_report_date= $st_date_diff->diff(new DateTime($st_date));
            $diff_minutes = $st_report_date->days * 24 * 60;
            $diff_minutes += $st_report_date->h * 60;
            $diff_minutes += $st_report_date->i;
            if ($diff_minutes > 59){$color="danger";}else {$color="success";}
            $data_last.=
                "{" . chr(34) ."ip_name" . chr(34). ":" . chr(34). $ip_name. chr(34) .",".
                chr(34) ."st_ip" . chr(34). ":" . chr(34). $st_ip. chr(34) .",".
                chr(34) ."color" . chr(34). ":" . chr(34). $color. chr(34) .",".
                chr(34) ."diff_minutes" . chr(34). ":" . chr(34). $diff_minutes. chr(34) ."},";
        }
    }
}

$data_last.="]";
$data_last=str_replace("},]","}]",$data_last);
$conn->close();
echo $data_last;
?>