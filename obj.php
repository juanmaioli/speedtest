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
  while ($row = $result->fetch_assoc()) {
    $st_id = $row["st_id"];
    $st_ping = $row["st_ping"];
    $st_down = $row["st_down"];
    $st_up = $row["st_up"];
    $st_ip = $row["st_ip"];
    $st_date = $row["st_date"];
    $st_date = substr($st_date, 11, 5);
    $filas .= "['" . $st_date . "'," . $st_ping . "," . $st_down .  "," . $st_up .  "],";
  }
} else {
  $filas = "['" . $st_now . "',0,0,0],";
}
$sql = "SELECT day(st_date) as dia,MAX(st_down) as max_down,MAX(st_up) as max_up,MIN(st_down) as min_down,MIN(st_up) as min_up
    FROM speedtest  WHERE MONTH(st_date) = " . $st_month . " AND YEAR(st_date) = " . $st_year . "  AND st_ip = '" . $ip_test . "' GROUP BY DAY(st_date)";
$result = $conn->query($sql);
if (mysqli_num_rows($result) == true) {
  $filas_mes = "";
  while ($row = $result->fetch_assoc()) {
    $dia = $row["dia"];
    $max_down = $row["max_down"];
    $min_down = $row["min_down"];
    $max_up = $row["max_up"];
    $min_up = $row["min_up"];
    $filas_mes .= "['" . $dia . "'," . $max_down  . "," . $min_down .  "," . $max_up .  "," . $min_up . "],";
  }
} else {
  $filas_mes = "['" . $st_now . "',0,0,0],";
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
  while ($row = $result->fetch_assoc()) {
    $dia = $row["fecha"];
    $max_down = $row["max_down"];
    $min_down = $row["min_down"];
    $max_up = $row["max_up"];
    $min_up = $row["min_up"];
    $filas_anio .= "['" . $dia . "'," . $max_down  . "," . $min_down .  "," . $max_up .  "," . $min_up . "],";
  }
} else {
  $filas_anio = "['" . $st_now . "',0,0,0],";
}
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
              <div class="col-md-1 text-center"><a href="index.php" class="btn btn-success"><i class="fa-regular fa-home fa-fw fa-lg"></i></a></div>
              <div class="col-md-7">
                <h2 class='text-success'><img src="images/speedometer.svg" class="" width="50px" /> Speed Test de <span class="text-primary"><?= $ip_name ?></span></h2>
                <span class="text-primary">(Su IP: <?= $ip_client ?>)</span>
              </div>
              <div class="col-md-2 text-end"><label class="">Cambiar a Estadísticas de la IP: </label></div>
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
                <div class="border p-3 shadow-purple-md rounded" align='center'>
                  <canvas id="chart_div_ping_canvas"></canvas>
                </div>
              </div>
              <div class="col-md-4">
                <div class="border p-3 shadow-darkblue-md rounded" align='center'>
                  <canvas id="chart_div_down_canvas"></canvas>
                </div>
              </div>
              <div class="col-md-4">
                <div class="border p-3 shadow-orange-md rounded" align='center'>
                  <canvas id="chart_div_up_canvas"></canvas>
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
        <div class="card shadow-night-sm">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="border p-3 shadow-darkmagenta-md rounded">
                  <canvas id="line_top_x_canvas"></canvas>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border p-3 shadow-darkmagenta-md rounded">
                  <canvas id="line_top_x_mes_canvas"></canvas>
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
        <div class="card shadow-night-sm">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="border p-3 shadow-darkmagenta-md rounded">
                  <canvas id="line_top_x_anio_canvas"></canvas>
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
  <script type="text/javascript">
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const isDarkMode = currentTheme === 'dark';
    const bodyColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color').trim();
    const bodyBg = 'transparent'; // Use transparent as the canvas will be in a themed card

    // Plugin for drawing text in the center of the doughnut chart
    const centerText = {
        id: 'centerText',
        beforeDraw(chart) {
            const { ctx, data, chartArea: { width, height } } = chart;
            ctx.save();
            ctx.font = 'bolder 20px sans-serif';
            ctx.fillStyle = bodyColor;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            let text = data.datasets[0].data[0];
            let x = width / 2;
            let y = height / 2 + (chart.getDatasetMeta(0).data[0].y - chart.chartArea.top);
            ctx.fillText(text, x, y);
            ctx.restore();
        }
    };

    function createGaugeChart(canvasId, label, value, max, redThreshold, yellowThreshold, colors) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        const data = {
            labels: [label, ''],
            datasets: [{
                data: [value, max - value],
                backgroundColor: colors,
                borderColor: bodyBg,
                borderWidth: 1
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: 270,
                cutout: '80%', // Thickness of the gauge
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    },
                    title: {
                        display: true,
                        text: label,
                        color: bodyColor,
                        font: {
                            size: 16
                        }
                    }
                }
            },
            plugins: [centerText]
        };
        return new Chart(ctx, config);
    }

    // Ping Gauge
    const pingMax = 80;
    const pingRedFrom = 60;
    const pingYellowFrom = 40;
    const pingValue = <?= $st_ping_gauge ?>;
    const pingColors = [
        pingValue >= pingRedFrom ? '#D91A46' : (pingValue >= pingYellowFrom ? '#FEBC37' : '#34A84F'), // Actual value color
        '#E0E0E0' // Remaining part color
    ];
    createGaugeChart('chart_div_ping_canvas', 'Ping ms', pingValue, pingMax, pingRedFrom, pingYellowFrom, pingColors);

    // Download Gauge
    const downloadMax = 500;
    const downloadRedFrom = 0; // Assuming low values are red for download
    const downloadYellowFrom = 10;
    const downloadValue = <?= $st_down_gauge ?>;
    const downloadColors = [
        downloadValue <= 10 ? '#D91A46' : (downloadValue <= 20 ? '#FEBC37' : '#0A83F9'), // Actual value color
        '#E0E0E0' // Remaining part color
    ];
    createGaugeChart('chart_div_down_canvas', 'Download Mbit/s', downloadValue, downloadMax, downloadRedFrom, downloadYellowFrom, downloadColors);

    // Upload Gauge
    const uploadMax = 500;
    const uploadRedFrom = 0; // Assuming low values are red for upload
    const uploadYellowFrom = 5;
    const uploadValue = <?= $st_up_gauge ?>;
    const uploadColors = [
        uploadValue <= 5 ? '#D91A46' : (uploadValue <= 10 ? '#FEBC37' : '#34A84F'), // Actual value color
        '#E0E0E0' // Remaining part color
    ];
    createGaugeChart('chart_div_up_canvas', 'Upload Mbit/s', uploadValue, uploadMax, uploadRedFrom, uploadYellowFrom, uploadColors);

    // Line Charts
    function createLineChart(canvasId, title, subtitle, labels, datasets) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        const config = {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title,
                        color: bodyColor,
                        font: {
                            size: 16
                        }
                    },
                    subtitle: {
                        display: true,
                        text: subtitle,
                        color: bodyColor,
                        font: {
                            size: 12
                        }
                    },
                    legend: {
                        labels: {
                            color: bodyColor
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: bodyColor
                        },
                        grid: {
                            color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y: {
                        ticks: {
                            color: bodyColor
                        },
                        grid: {
                            color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                backgroundColor: bodyBg
            }
        };
        return new Chart(ctx, config);
    }

    // Data for Últmas 24hs
    const filasData = [<?= $filas ?>];
    const labels24hs = filasData.map(row => row[0]);
    const pingData24hs = filasData.map(row => row[1]);
    const downloadData24hs = filasData.map(row => row[2]);
    const uploadData24hs = filasData.map(row => row[3]);

    const datasets24hs = [{
        label: 'Ping ms',
        data: pingData24hs,
        borderColor: '#34A84F',
        tension: 0.1,
        fill: false
    }, {
        label: 'Download Mbit/s',
        data: downloadData24hs,
        borderColor: '#0A83F9',
        tension: 0.1,
        fill: false
    }, {
        label: 'Upload Mbit/s',
        data: uploadData24hs,
        borderColor: '#FEBC37',
        tension: 0.1,
        fill: false
    }];
    createLineChart('line_top_x_canvas', 'Speed Test Últmas 24hs', 'Velocidades por hora', labels24hs, datasets24hs);

    // Data for Mes
    const filasMesData = [<?= $filas_mes ?>];
    const labelsMes = filasMesData.map(row => row[0]);
    const maxDownloadMes = filasMesData.map(row => row[1]);
    const minDownloadMes = filasMesData.map(row => row[2]);
    const maxUploadMes = filasMesData.map(row => row[3]);
    const minUploadMes = filasMesData.map(row => row[4]);

    const datasetsMes = [{
        label: 'Max Download Mbit/s',
        data: maxDownloadMes,
        borderColor: '#0A83F9',
        tension: 0.1,
        fill: false
    }, {
        label: 'Min Download Mbit/s',
        data: minDownloadMes,
        borderColor: '#34A84F',
        tension: 0.1,
        fill: false
    }, {
        label: 'Max Upload Mbit/s',
        data: maxUploadMes,
        borderColor: '#FEBC37',
        tension: 0.1,
        fill: false
    }, {
        label: 'Min Upload Mbit/s',
        data: minUploadMes,
        borderColor: '#D91A46',
        tension: 0.1,
        fill: false
    }];
    createLineChart('line_top_x_mes_canvas', 'Speed Test durante <?= $mesGraph ?> de <?= $st_year ?>', 'Velocidades por hora', labelsMes, datasetsMes);

    // Data for Año
    const filasAnioData = [<?= $filas_anio ?>];
    const labelsAnio = filasAnioData.map(row => row[0]);
    const maxDownloadAnio = filasAnioData.map(row => row[1]);
    const minDownloadAnio = filasAnioData.map(row => row[2]);
    const maxUploadAnio = filasAnioData.map(row => row[3]);
    const minUploadAnio = filasAnioData.map(row => row[4]);

    const datasetsAnio = [{
        label: 'Max Download Mbit/s',
        data: maxDownloadAnio,
        borderColor: '#0A83F9',
        tension: 0.1,
        fill: false
    }, {
        label: 'Min Download Mbit/s',
        data: minDownloadAnio,
        borderColor: '#34A84F',
        tension: 0.1,
        fill: false
    }, {
        label: 'Max Upload Mbit/s',
        data: maxUploadAnio,
        borderColor: '#FEBC37',
        tension: 0.1,
        fill: false
    }, {
        label: 'Min Upload Mbit/s',
        data: minUploadAnio,
        borderColor: '#D91A46',
        tension: 0.1,
        fill: false
    }];
    createLineChart('line_top_x_anio_canvas', 'Speed Test durante <?= $st_year ?>', 'Velocidades por dia', labelsAnio, datasetsAnio);
  </script>
  <?php include("footer.php"); ?>