<?php
include("config.php");
$ip_client = $_SERVER['REMOTE_ADDR'];

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if( isset( $_POST['ip_test'])) {
    $ip_test = $_POST['ip_test'];
}
else
{
    $ip_test = $ip_client;
}



$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$sql = "SELECT count(*) as total FROM speedtest where st_ip = '$ip_client' order by st_date desc limit 1"; 
$result = $conn->query($sql);


if (mysqli_num_rows($result) == true) {
    while($row = $result->fetch_assoc())
        {
            $total = $row["total"];
            if($total>0){header("Location: index.php");}
        } 
    }else{$last_report = "S/Rep";}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title id='titulo'>PikApp <?=$ip_client?></title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css?version=5.1.0">
    <!-- Favicon for this template -->
    <link rel="apple-touch-icon" sizes="57x57" href="../images/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../images/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../images/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../images/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../images/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../images/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../images/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../images/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../images/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../images/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../images/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png">
    <link rel="manifest" href="../images/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    </head>
    <body>
        <div class="container-fluid">

        <div class="row mt-5">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h2 class='text-success'>Su IP: <?=$ip_client?></h2></div>
                    <div class="card-body">
                    <h1 class="display-1 text-danger border border-danger text-center p-5">IP no autorizada</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>


        </div>

    <br><br><br>
    </body>
</html>
