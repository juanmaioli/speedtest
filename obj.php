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

$st_now = date("Y-m-d H:i:s");
$st_month = date("m");
$st_year = date("Y");

function mesMostrar() {
    $meses = array(
        1 => "Enero",
        2 => "Febrero",
        3 => "Marzo",
        4 => "Abril",
        5 => "Mayo",
        6 => "Junio",
        7 => "Julio",
        8 => "Agosto",
        9 => "Septiembre",
        10 => "Octubre",
        11 => "Noviembre",
        12 => "Diciembre"
    );

    $mesMostrar = $meses[date("m")];
    return $mesMostrar;
}
$mesGraph= mesMostrar($st_year);

    $sql = "SELECT count(*) as total FROM speedtest where st_ip = '$ip_client' ";
    $result = $conn->query($sql);


    if (mysqli_num_rows($result) == true) {
        while($row = $result->fetch_assoc())
            {
                $total = $row["total"];
                if($total==0){header("Location: block.php");}
            }
        }else{$last_report = "S/Rep";}

    $sql = "SELECT speedtest.st_date, ips.ip_name FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE speedtest.st_ip = '".$ip_test."' ORDER BY speedtest.st_date DESC limit 1 ";
    $result = $conn->query($sql);

    if (mysqli_num_rows($result) == true) {
        while($row = $result->fetch_assoc())
            {
                $last_report = $row["st_date"];
                $ip_name = $row["ip_name"];
                $ip_name = $ip_name . "(" . $ip_test .")";
            }
        }else{$last_report = "S/Rep";$ip_name = $ip_test ;}


    $sql="SELECT speedtest.st_ip,ips.ip_name FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number GROUP BY speedtest.st_ip ORDER BY ips.ip_name ASC";
    $result = $conn->query($sql);

    $st_ip_list = "<select class='form-control' id='ip_test' name='ip_test' onchange='this.form.submit()'><option value=''>Seleccionar IP</option>";

    if (mysqli_num_rows($result) == true) {
        while($row = $result->fetch_assoc())
            {
                $st_ip_list .= "<option value='" .$row["st_ip"] . "'>" . $row["ip_name"] . "(" . $row["st_ip"] . ")</option>";
            }
        }
    $st_ip_list .= "</select>";

    $sql = "SELECT * FROM speedtest
    WHERE st_date <= CURTIME( ) AND st_date >= DATE_SUB( CURTIME( ), INTERVAL 24 HOUR ) AND st_ip = '" . $ip_test . "'";
    $result = $conn->query($sql);

    if (mysqli_num_rows($result) == true) {
    $filas="";
        while($row = $result->fetch_assoc())
            {
                $st_id = $row["st_id"];
                $st_ping = $row["st_ping"];
                $st_down = $row["st_down"];
                $st_up = $row["st_up"];
                $st_ip = $row["st_ip"];
                $st_date = $row["st_date"];
                $st_date = substr($st_date,11,5);
                $filas.="['" . $st_date . "'," . $st_ping . "," . $st_down .  "," . $st_up .  "],";
            }
        }else{ $filas="['" . $st_now . "',0,0,0],";}
    $sql = "SELECT day(st_date) as dia,MAX(st_down) as max_down,MAX(st_up) as max_up,MIN(st_down) as min_down,MIN(st_up) as min_up
    FROM speedtest  WHERE MONTH(st_date) = " . $st_month . " AND YEAR(st_date) = " . $st_year . "  AND st_ip = '" . $ip_test . "' GROUP BY DAY(st_date)";
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) == true) {
    $filas_mes="";
        while($row = $result->fetch_assoc())
            {
                $dia = $row["dia"];
                $max_down = $row["max_down"];
                $min_down = $row["min_down"];
                $max_up = $row["max_up"];
                $min_up = $row["min_up"];
                $filas_mes.="['" . $dia . "'," . $max_down  . "," . $min_down .  "," . $max_up .  "," . $min_up . "],";
            }
        }else{ $filas_mes="['" . $st_now . "',0,0,0],";}
//Gauge
    $sql = "SELECT st_ping, st_down, st_up FROM speedtest WHERE st_ip = '" . $ip_test . "' ORDER BY st_date DESC LIMIT 1";
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) == true) {
        while($row = $result->fetch_assoc())
            {
                $st_ping_gauge = $row["st_ping"];
                $st_down_gauge = $row["st_down"];
                $st_up_gauge = $row["st_up"];
            }
        }else{ $st_ping_gauge = 0;$st_down_gauge = 0;$st_up_gauge = 0;}

    $sql="SELECT CONCAT (DAY( st_date ) ,'-', MONTH ( st_date )) as fecha, MAX( st_down ) AS max_down, MAX( st_up ) AS max_up, MIN( st_down ) AS min_down, MIN( st_up ) AS min_up
    FROM speedtest WHERE YEAR ( st_date ) = " . $st_year . "  AND st_ip = '" . $ip_test . "' GROUP BY DAY ( st_date ), MONTH ( st_date ) ORDER BY st_date";
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) == true) {
    $filas_anio="";
        while($row = $result->fetch_assoc())
            {
                $dia = $row["fecha"];
                $max_down = $row["max_down"];
                $min_down = $row["min_down"];
                $max_up = $row["max_up"];
                $min_up = $row["min_up"];
                $filas_anio.="['" . $dia . "'," . $max_down  . "," . $min_down .  "," . $max_up .  "," . $min_up . "],";
            }
        }else{ $filas_anio="['" . $st_now . "',0,0,0],";}
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
    <link rel="apple-touch-icon" sizes="57x57" href="images/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="images/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Hora');
            data.addColumn('number', 'Ping ms');
            data.addColumn('number', 'Download Mbit/s');
            data.addColumn('number', 'Upload Mbit/s');
            data.addRows([<?=$filas?>]);
            var options = {chart: {title: 'Speed Test Últmas 24hs',subtitle: 'Velocidades por hora',legend: 'none'}, height: 400, axes: {x: {0: {side: 'buttom'}}},colors: ['#34A84F','#0A83F9','#FEBC37']};
            var chart = new google.charts.Line(document.getElementById('line_top_x'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Dia');
            data.addColumn('number', 'Max Download Mbit/s');
            data.addColumn('number', 'Min Download Mbit/s');
            data.addColumn('number', 'Max Upload Mbit/s');
            data.addColumn('number', 'Min Upload Mbit/s');
            data.addRows([<?=$filas_mes?>]);

            var options = {
            chart: {title: 'Speed Test durante <?=$mesGraph?> de <?=$st_year?>',subtitle: 'Velocidades por hora',},height: 400,axes: {x: {0: {side: 'buttom'}}},colors: ['#0A83F9','#34A84F','#FEBC37','#D91A46']
            };
            var chart = new google.charts.Line(document.getElementById('line_top_x_mes'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Fecha');
            data.addColumn('number', 'Max Download Mbit/s');
            data.addColumn('number', 'Min Download Mbit/s');
            data.addColumn('number', 'Max Upload Mbit/s');
            data.addColumn('number', 'Min Upload Mbit/s');
            data.addRows([<?=$filas_anio?>]);

            var options = {
            chart: {title: 'Speed Test durante <?=$st_year?>',subtitle: 'Velocidades por dia',},height: 400,axes: {x: {0: {side: 'buttom'}}},colors: ['#0A83F9','#34A84F','#FEBC37','#D91A46']
            };
            var chart = new google.charts.Line(document.getElementById('line_top_x_anio'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([['Label', 'Value'],['Ping', <?=$st_ping_gauge?>]]);
            var options_ping = {width: 220, height: 220,redFrom: 60, redTo: 80,yellowFrom:40, yellowTo: 60, max:80};
            var chart = new google.visualization.Gauge(document.getElementById('chart_div_ping'));
            chart.draw(data, options_ping);

            var data = google.visualization.arrayToDataTable([['Label', 'Value'],['Download',  <?=$st_down_gauge?>]])
            var options_down = {width: 220, height: 220,redFrom: 0, redTo: 10,yellowFrom:10, yellowTo: 20,max:50};
            var chart = new google.visualization.Gauge(document.getElementById('chart_div_down'));
            chart.draw(data, options_down);

            var data = google.visualization.arrayToDataTable([['Label', 'Value'],['Upload',  <?=$st_up_gauge?>]])
            var options_up = {width: 220, height: 220,redFrom: 0, redTo: 5,yellowFrom:5, yellowTo: 10,max:25};
            var chart = new google.visualization.Gauge(document.getElementById('chart_div_up'));
            chart.draw(data, options_up);
        }
    </script>

    </head>
    <body>
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-7"><h2 class='text-success'><img src="images/speedometer.svg" class="" width="50px" /> Speed Test de <span class="text-primary"><?=$ip_name?></span></h2>
                        <span class="text-primary">(Su IP: <?=$ip_client?>)</span></div>
                        <div class="col-md-2 text-end"><label class="">Cambiar a Estadisticas de la IP: </label></div>
                        <div class="col-md-2 text-end"><form action='obj.php' method='post'><?=$st_ip_list?></form></div>
                        <div class="col-md-1 text-end"><a href="index.php" class="btn btn-success">Inicio</a></div>
                    </div>

                </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center"><h2 class='text-primary'>Último Reporte: <?=$last_report?></h2></div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4"><div id="chart_div_ping" class="border p-3 border-success rounded-lg" align='center'></div></div>
                            <div class="col-md-4"><div id="chart_div_down" class="border p-3 border-success rounded-lg"  align='center'></div></div>
                            <div class="col-md-4"><div id="chart_div_up" class="border p-3 border-success rounded-lg" align='center'></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>

        <div class="row mt-2">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6"><div id="line_top_x" class="border p-3 border-success rounded-lg"></div></div>
                            <div class="col-md-6"><div id="line_top_x_mes" class="border p-3 border-success rounded-lg"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12"><div id="line_top_x_anio" class="border p-3 border-success rounded-lg"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-1"></div>
            <div class="col-md-10 m-1 text-center"><a href="del.php?id=<?=$ip_test?>" class="btn btn-danger">Borrar Estadisticas de <?=$ip_test?></a></div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <br><br><br>
    </body>
</html>
