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
  }
} else {
  $last_report = "S/Rep";
}

$sql = "SELECT speedtest.st_date, ips.ip_name FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE speedtest.st_ip = '" . $ip_test . "' ORDER BY speedtest.st_date DESC limit 1 ";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $last_report = $row["st_date"];
    $ip_name = $row["ip_name"];
    $ip_name = $ip_name . "(" . $ip_test . ")";
  }
} else {
  $last_report = "S/Rep";
  $ip_name = $ip_test;
}


$sql = "SELECT speedtest.st_ip,ips.ip_name FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number GROUP BY speedtest.st_ip ORDER BY ips.ip_name ASC";
$result = $conn->query($sql);

$st_ip_list = "<select class='form-select' id='ip_test' name='ip_test' onchange='this.form.submit()'><option value=''>Seleccionar IP</option>";

if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $st_ip_list .= "<option value='" . $row["st_ip"] . "'>" . $row["ip_name"] . "(" . $row["st_ip"] . ")</option>";
  }
}
$st_ip_list .= "</select>";
//Ultimas 24hs SELECT speedtest.* , ip_name FROM speedtest left join ips ON ip_number = st_ip WHERE st_date <= CURTIME( ) AND st_date >= DATE_SUB( CURTIME( ), INTERVAL 24 HOUR ) AND ip_name = 'Casa Fibra 800Mb';
$sql = "SELECT * FROM speedtest
    WHERE st_date <= CURTIME( ) AND st_date >= DATE_SUB( CURTIME( ), INTERVAL 24 HOUR ) AND st_ip = '" . $ip_test . "'";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
  $filas = "";
  $chart_24h_labels = [];
  $chart_24h_ping = [];
  $chart_24h_down = [];
  $chart_24h_up = [];
  while ($row = $result->fetch_assoc()) {
    $st_id = $row["st_id"];
    $st_ping = (float)$row["st_ping"];
    $st_down = (float)$row["st_down"];
    $st_up = (float)$row["st_up"];
    $st_ip = $row["st_ip"];
    $st_date = $row["st_date"];
    $st_date_short = substr($st_date, 11, 5);
    $filas .= "['" . $st_date_short . "'," . $st_ping . "," . $st_down .  "," . $st_up .  "],";
    
    $chart_24h_labels[] = $st_date_short;
    $chart_24h_ping[] = $st_ping;
    $chart_24h_down[] = $st_down;
    $chart_24h_up[] = $st_up;
  }
} else {
  $filas = "['" . $st_now . "',0,0,0],";
  $chart_24h_labels = [$st_now];
  $chart_24h_ping = [0];
  $chart_24h_down = [0];
  $chart_24h_up = [0];
}
$sql = "SELECT day(st_date) as dia,MAX(st_down) as max_down,MAX(st_up) as max_up,MIN(st_down) as min_down,MIN(st_up) as min_up
    FROM speedtest  WHERE MONTH(st_date) = " . $st_month . " AND YEAR(st_date) = " . $st_year . "  AND st_ip = '" . $ip_test . "' GROUP BY DAY(st_date)";
$result = $conn->query($sql);
if (mysqli_num_rows($result) == true) {
  $filas_mes = "";
  $chart_mes_labels = [];
  $chart_mes_max_down = [];
  $chart_mes_min_down = [];
  $chart_mes_max_up = [];
  $chart_mes_min_up = [];
  while ($row = $result->fetch_assoc()) {
    $dia = $row["dia"];
    $max_down = (float)$row["max_down"];
    $min_down = (float)$row["min_down"];
    $max_up = (float)$row["max_up"];
    $min_up = (float)$row["min_up"];
    $filas_mes .= "['" . $dia . "'," . $max_down  . "," . $min_down .  "," . $max_up .  "," . $min_up . "],";
    
    $chart_mes_labels[] = "Día " . $dia;
    $chart_mes_max_down[] = $max_down;
    $chart_mes_min_down[] = $min_down;
    $chart_mes_max_up[] = $max_up;
    $chart_mes_min_up[] = $min_up;
  }
} else {
  $filas_mes = "['" . $st_now . "',0,0,0],";
  $chart_mes_labels = [$st_now];
  $chart_mes_max_down = [0];
  $chart_mes_min_down = [0];
  $chart_mes_max_up = [0];
  $chart_mes_min_up = [0];
}
//Gauge
$sql = "SELECT st_ping, st_down, st_up FROM speedtest WHERE st_ip = '" . $ip_test . "' ORDER BY st_date DESC LIMIT 1";
$result = $conn->query($sql);
if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $st_ping_gauge = $row["st_ping"];
    $st_down_gauge = $row["st_down"];
    $st_up_gauge = $row["st_up"];
  }
} else {
  $st_ping_gauge = 0;
  $st_down_gauge = 0;
  $st_up_gauge = 0;
}

$sql = "SELECT CONCAT (DAY( st_date ) ,'-', MONTH ( st_date )) as fecha, MAX( st_down ) AS max_down, MAX( st_up ) AS max_up, MIN( st_down ) AS min_down, MIN( st_up ) AS min_up
    FROM speedtest WHERE YEAR ( st_date ) = " . $st_year . "  AND st_ip = '" . $ip_test . "' GROUP BY DAY ( st_date ), MONTH ( st_date ) ORDER BY st_date";
$result = $conn->query($sql);
if (mysqli_num_rows($result) == true) {
  $filas_anio = "";
  $chart_anio_labels = [];
  $chart_anio_max_down = [];
  $chart_anio_min_down = [];
  $chart_anio_max_up = [];
  $chart_anio_min_up = [];
  while ($row = $result->fetch_assoc()) {
    $dia = $row["fecha"];
    $max_down = (float)$row["max_down"];
    $min_down = (float)$row["min_down"];
    $max_up = (float)$row["max_up"];
    $min_up = (float)$row["min_up"];
    $filas_anio .= "['" . $dia . "'," . $max_down  . "," . $min_down .  "," . $max_up .  "," . $min_up . "],";
    
    $chart_anio_labels[] = $dia;
    $chart_anio_max_down[] = $max_down;
    $chart_anio_min_down[] = $min_down;
    $chart_anio_max_up[] = $max_up;
    $chart_anio_min_up[] = $min_up;
  }
} else {
  $filas_anio = "['" . $st_now . "',0,0,0],";
  $chart_anio_labels = [$st_now];
  $chart_anio_max_down = [0];
  $chart_anio_min_down = [0];
  $chart_anio_max_up = [0];
  $chart_anio_min_up = [0];
}
$conn->close();
include("header.php");
?>
  <div class="container-fluid">
    <div class="row mt-2">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <div class="card border-0 shadow-sm bg-body-tertiary">
          <div class="card-header border-0 bg-body-tertiary">
            <div class="row">
              <div class="col-md-1 text-center"><a href="index.php" class="btn btn-success"><i class="fa-regular fa-home fa-fw fa-lg"></i></a></div>
              <div class="col-md-7">
                <h2 class='text-success'><img src="images/speedometer.svg" class="" width="50px" /> Speed Test de <span class="text-primary"><?= $ip_name ?></span></h2>
                <span class="text-primary">(Su IP: <?= $ip_client ?>)</span>
              </div>
              <div class="col-md-2 text-end"><label class="">Cambiar a la IP: </label></div>
              <div class="col-md-2 text-end">
                <form action='obj.php' method='post'><?= $st_ip_list ?></form>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 text-center">
                <h2 class='text-primary'>Último Reporte: <?= $last_report ?></h2>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-4">
                <div class="border border-secondary-subtle p-3 shadow-sm rounded bg-body">
                  <canvas id="gauge_ping" height="200"></canvas>
                </div>
              </div>
              <div class="col-md-4">
                <div class="border border-secondary-subtle p-3 shadow-sm rounded bg-body">
                  <canvas id="gauge_down" height="200"></canvas>
                </div>
              </div>
              <div class="col-md-4">
                <div class="border border-secondary-subtle p-3 shadow-sm rounded bg-body">
                  <canvas id="gauge_up" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-1"></div>
    </div>

    <div class="row mt-3">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <div class="card border-0 shadow-sm bg-body-tertiary">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="border border-secondary-subtle p-3 shadow-sm rounded bg-body">
                  <canvas id="line_24h" height="400"></canvas>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border border-secondary-subtle p-3 shadow-sm rounded bg-body">
                  <canvas id="line_mes" height="400"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-1"></div>
    </div>
    <div class="row mt-3">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <div class="card border-0 shadow-sm bg-body-tertiary">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="border border-secondary-subtle p-3 shadow-sm rounded bg-body">
                   <canvas id="line_anio" height="400"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-1"></div>
    </div>
    <div class="row mt-3">
      <div class="col-md-1"></div>
      <div class="col-md-10 m-1 text-center"><a href="del.php?id=<?= $ip_test ?>" class="btn btn-danger"><i class="fa-regular fa-trash-can fa-fw"></i> Borrar Estadísticas de <?= $ip_test ?></a></div>
      <div class="col-md-1"></div>
    </div>
  </div>
  <br><br><br>
  <script>
    // Configuración común para Gauges
    const gaugeOptions = (title, max) => ({
      type: 'doughnut',
      data: {
        datasets: [{
          data: [0, max],
          backgroundColor: ['rgba(10, 131, 249, 0.7)', 'rgba(0, 0, 0, 0.1)'],
          borderWidth: 0,
          circumference: 180,
          rotation: 270,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: { display: true, text: title },
          tooltip: { enabled: false },
          legend: { display: false }
        }
      }
    });

    // Gauge Ping
    const ctxGP = document.getElementById('gauge_ping').getContext('2d');
    const gaugePing = new Chart(ctxGP, gaugeOptions('Ping (ms)', 100));
    gaugePing.data.datasets[0].data = [<?= $st_ping_gauge ?>, 100 - <?= $st_ping_gauge ?>];
    gaugePing.data.datasets[0].backgroundColor = [<?= $st_ping_gauge ?> > 60 ? 'rgba(217, 26, 70, 0.7)' : 'rgba(10, 131, 249, 0.7)', 'rgba(0, 0, 0, 0.1)'];
    gaugePing.update();

    // Gauge Download
    const ctxGD = document.getElementById('gauge_down').getContext('2d');
    const gaugeDown = new Chart(ctxGD, gaugeOptions('Download (Mb/s)', 1000));
    gaugeDown.data.datasets[0].data = [<?= $st_down_gauge ?>, 1000 - <?= $st_down_gauge ?>];
    gaugeDown.update();

    // Gauge Upload
    const ctxGU = document.getElementById('gauge_up').getContext('2d');
    const gaugeUp = new Chart(ctxGU, gaugeOptions('Upload (Mb/s)', 1000));
    gaugeUp.data.datasets[0].data = [<?= $st_up_gauge ?>, 1000 - <?= $st_up_gauge ?>];
    gaugeUp.update();

    // Line Chart 24h
    new Chart(document.getElementById('line_24h').getContext('2d'), {
      type: 'line',
      data: {
        labels: <?= json_encode($chart_24h_labels) ?>,
        datasets: [
          { label: 'Ping', data: <?= json_encode($chart_24h_ping) ?>, borderColor: '#34A84F', fill: false, tension: 0.1 },
          { label: 'Download', data: <?= json_encode($chart_24h_down) ?>, borderColor: '#0A83F9', fill: false, tension: 0.1 },
          { label: 'Upload', data: <?= json_encode($chart_24h_up) ?>, borderColor: '#FEBC37', fill: false, tension: 0.1 }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Últimas 24hs' } } }
    });

    // Line Chart Mes
    new Chart(document.getElementById('line_mes').getContext('2d'), {
      type: 'line',
      data: {
        labels: <?= json_encode($chart_mes_labels) ?>,
        datasets: [
          { label: 'Max Download', data: <?= json_encode($chart_mes_max_down) ?>, borderColor: '#0A83F9', fill: false },
          { label: 'Min Download', data: <?= json_encode($chart_mes_min_down) ?>, borderColor: '#34A84F', fill: false },
          { label: 'Max Upload', data: <?= json_encode($chart_mes_max_up) ?>, borderColor: '#FEBC37', fill: false },
          { label: 'Min Upload', data: <?= json_encode($chart_mes_min_up) ?>, borderColor: '#D91A46', fill: false }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Mes Actual' } } }
    });

    // Line Chart Anual
    new Chart(document.getElementById('line_anio').getContext('2d'), {
      type: 'line',
      data: {
        labels: <?= json_encode($chart_anio_labels) ?>,
        datasets: [
          { label: 'Max Download', data: <?= json_encode($chart_anio_max_down) ?>, borderColor: '#0A83F9', fill: false },
          { label: 'Min Download', data: <?= json_encode($chart_anio_min_down) ?>, borderColor: '#34A84F', fill: false },
          { label: 'Max Upload', data: <?= json_encode($chart_anio_max_up) ?>, borderColor: '#FEBC37', fill: false },
          { label: 'Min Upload', data: <?= json_encode($chart_anio_min_up) ?>, borderColor: '#D91A46', fill: false }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Reporte Anual' } } }
    });
  </script>
  <?php include("footer.php"); ?>