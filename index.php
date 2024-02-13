<?php
include("config.php");
$ip_client = $_SERVER['REMOTE_ADDR'];

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if (isset($_POST['ip_test'])) {
  $ip_test = $_POST['ip_test'];
} else {
  $ip_test = $ip_client;
}

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
mysqli_set_charset($conn, 'utf8');

$st_date_diff = new DateTime(date("Y-m-d H:i:s"));
$st_now = date("Y-m-d H:i:s");
$st_month = date("m");
$st_year = date("Y");

function mesMostrar()
{
  if (date("m") == 1) {
    $mesMostrar = "Enero";
  }
  if (date("m") == 2) {
    $mesMostrar = "Febrero";
  }
  if (date("m") == 3) {
    $mesMostrar = "Marzo";
  }
  if (date("m") == 4) {
    $mesMostrar = "Abril";
  }
  if (date("m") == 5) {
    $mesMostrar = "Mayo";
  }
  if (date("m") == 6) {
    $mesMostrar = "Junio";
  }
  if (date("m") == 7) {
    $mesMostrar = "Julio";
  }
  if (date("m") == 8) {
    $mesMostrar = "Agosto";
  }
  if (date("m") == 9) {
    $mesMostrar = "Septiembre";
  }
  if (date("m") == 10) {
    $mesMostrar = "Octubre";
  }
  if (date("m") == 11) {
    $mesMostrar = "Noviembre";
  }
  if (date("m") == 12) {
    $mesMostrar = "Diciembre";
  }
  return $mesMostrar;
}
$mesGraph = mesMostrar($st_year);

$sql = "SELECT count(*) as total FROM speedtest where st_ip = '$ip_client' ";
$result = $conn->query($sql);


if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $total = $row["total"];
    // if($total==0){header("Location: block.php");}
  }
} else {
  $last_report = "S/Rep";
}

$st_ip_list = array();

$sql = "SELECT speedtest.st_ip FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE ips.ip_delete = 0 GROUP BY speedtest.st_ip ORDER BY ips.ip_name ASC";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    array_push($st_ip_list, $row["st_ip"]);
  }
}

$lx = 0;
$bars_data = "";
$bars_data_ping = "";
$data_last = "";
foreach ($st_ip_list as &$st_ip_list_ip) {
  $sql = "SELECT speedtest.st_down,speedtest.st_up,speedtest.st_ip,speedtest.st_date,ips.ip_name,speedtest.st_ping 
    FROM speedtest
    INNER JOIN ips ON speedtest.st_ip = ips.ip_number
    WHERE speedtest.st_ip = '$st_ip_list_ip'
    ORDER BY speedtest.st_id DESC  LIMIT 1";
  $result = $conn->query($sql);
  $total_lineas = $result->num_rows;
  if (mysqli_num_rows($result) == true) {
    while ($row = $result->fetch_assoc()) {
      $lx++;
      $st_ip  = $row["st_ip"];
      $st_down  = $row["st_down"];
      $st_up  = $row["st_up"];
      $st_ping  = $row["st_ping"];
      $st_date  = $row["st_date"];
      $ip_name  = $row["ip_name"];
      $bars_data .= "['" . $ip_name . chr(92) . chr(110) .  $st_ip . chr(92) . chr(110) . $st_date . "' ,$st_down ,$st_up],";
      $bars_data_ping .= "['" . $ip_name . "' ,$st_ping ],";
      $st_report_date = $st_date_diff->diff(new DateTime($st_date));
      $diff_minutes = $st_report_date->days * 24 * 60;
      $diff_minutes += $st_report_date->h * 60;
      $diff_minutes += $st_report_date->i;

      if ($diff_minutes > 60) {
        $color = "btn-danger";
      } else {
        $color = "btn-success";
      }

      // $data_last.="<div class='col text-white $color small m-1 rounded-lg' title='$st_date'>$ip_name<br>Hace $diff_minutes minutos</div>";
      $data_last .= "<div class='col text-center'>
            <form action='obj.php' method='post'>
            <input type='hidden' id='ip_test' name='ip_test' value='$st_ip'>
            <div id='$st_ip'><button class='btn $color btn-block'>
            $ip_name<br>Hace $diff_minutes min.
            </button></div>
            </form>
            </div>";
    }
  }
}

$table_down = "<table class='table table-striped table-hover table-sm'> <thead> <th colspan='2'>AVG Download</th></thead>";
$table_up = "<table class='table table-striped table-hover table-sm'> <thead> <th colspan='2'>AVG Upload</th></thead>";
$table_ping = "<table class='table table-striped table-hover table-sm'> <thead> <th colspan='2'>AVG Ping</th> </thead>";

$sql = "SELECT ips.ip_name,speedtest.st_ip,round( Avg( speedtest.st_down ),1) AS PromDownload FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number GROUP BY speedtest.st_ip ORDER BY PromDownload DESC";
$result = $conn->query($sql);
if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $table_down .= "<tr><td>" . $row["ip_name"] . "(" . $row["st_ip"] . ")</td><td>" . $row["PromDownload"] . "</td></tr>";
  }
}
$sql = "SELECT ips.ip_name,st_ip,round( avg( st_up ),1) AS PromUpload FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number GROUP BY st_ip ORDER BY PromUpload DESC ";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $table_up .= "<tr><td>" . $row["ip_name"] . "(" . $row["st_ip"] . ")</td><td>" . $row["PromUpload"] . "</td></tr>";
  }
}
$sql = "SELECT ips.ip_name,st_ip,round( avg( st_ping ), 1 ) AS PromPing FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number  GROUP BY st_ip  ORDER BY PromPing ASC";
$result = $conn->query($sql);
if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $table_ping .= "<tr><td>" . $row["ip_name"] . "(" . $row["st_ip"] . ")</td><td>" . $row["PromPing"] . "</td></tr>";
  }
}

$table_down .= "</table>";
$table_up .= "</table>";
$table_ping .= "</table>";

$conn->close();
include("header.php");
?>


  <div class="container-fluid">
    <div class="row mt-2">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <div class="card shadow-night-sm">
          <div class="card-header">
            <div class="row">
              <div class="col-md-12">
                <h2 class='text-darkmagenta'><img src="images/speedometer.svg" class="" width="50px" /> Resumén SpeedTest</h2>
                <span class="text-primary">(Su IP: <?= $ip_client ?>)</span>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row mt-3">
              <div class="col-md">
                <div class="border p-3 shadow-darkmagenta-md rounded">
                  <div class="row"><?= $data_last ?></div>
                </div>
              </div>

            </div>
            <div class="row mt-3">
              <div class="col-md-8">
                <div id="bars_last_test" class="border p-3 shadow-darkmagenta-md rounded"></div>
              </div>
              <div class="col-md-4">
                <div id="bars_last_ping" class="border p-3 shadow-darkmagenta-md rounded"></div>
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-md"><?= $table_down ?></div>
              <div class="col-md"><?= $table_up ?></div>
              <div class="col-md"><?= $table_ping ?></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-1"></div>
    </div>
  </div>
  <br><br><br>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Server', 'Download', 'Upload'],
        <?= $bars_data ?>
      ]);

      var options = {
        bars: 'horizontal',
        height: 600,
        chart: {
          title: 'Speedtest de todos los Servers',
          subtitle: 'Download y Upload según último reporte',
        }
      };

      var chart = new google.charts.Bar(document.getElementById('bars_last_test'));

      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Server', 'Ping'],
        <?= $bars_data_ping ?>
      ]);

      var options = {
        title: 'Ping de todos los Servers',
        bars: 'horizontal',
        hAxis: {
          title: 'Server'
        },
        height: 600
      };

      var chart = new google.charts.Bar(document.getElementById('bars_last_ping'));

      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>
  <script>
    setInterval(obtener_json, 60000);
    function obtener_json() {
      fetch('utils.php')
        .then(datos => datos.json())
        .then(datos => {
          for (let dato of datos) {
            valor = `${dato.st_ip}`;
            data = `<button class='btn btn-${dato.color}'>${dato.ip_name}<br>Hace ${dato.diff_minutes} min.</button>`
            document.getElementById(valor).innerHTML = data;
          }
        })
      // console.log(new Date(Date.now()));
    }
  </script>
  <?php include("footer.php"); ?>