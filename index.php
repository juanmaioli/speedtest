<?php
include("config.php");
include("data_logic.php");

$ip_client = $_SERVER['REMOTE_ADDR'];
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
mysqli_set_charset($conn, 'utf8');

// --- Verificación de IP del cliente (usando sentencias preparadas) ---
$stmt = $conn->prepare("SELECT count(*) as total FROM speedtest where st_ip = ?");
$stmt->bind_param("s", $ip_client);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    header("Location: block.php");
    exit();
}
$stmt->close();

// --- Obtener todos los datos de las funciones centralizadas ---
$speedtestData = getSpeedtestData($conn);
$avgTablesData = getAvgTables($conn);

$bars_data = $speedtestData['bars_data'];
$bars_data_ping = $speedtestData['bars_data_ping'];
$data_last = $speedtestData['data_last'];

$table_down = $avgTablesData['table_down'];
$table_up = $avgTablesData['table_up'];
$table_ping = $avgTablesData['table_ping'];

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
                <span class="text-primary">(Su IP: <?= htmlspecialchars($ip_client) ?>)</span>
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