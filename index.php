<?php
include("config.php");
include("data_logic.php");

$ip_client = $_SERVER['REMOTE_ADDR'];
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
mysqli_set_charset($conn, 'utf8');



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
                <div class="border p-3 shadow-darkmagenta-md rounded">
                  <canvas id="bars_last_test_canvas"></canvas>
                </div>
              </div>
              <div class="col-md-4">
                <div class="border p-3 shadow-darkmagenta-md rounded">
                  <canvas id="bars_last_ping_canvas"></canvas>
                </div>
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
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const isDarkMode = currentTheme === 'dark';
    const bodyColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color').trim();
    const bodyBg = 'transparent'; // Use transparent as the canvas will be in a themed card

    function createBarChart(canvasId, title, subtitle, labels, datasets, horizontal = false) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        const config = {
            type: horizontal ? 'bar' : 'bar', // Chart.js bar is already horizontal by default for some configurations
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: horizontal ? 'y' : 'x', // Make bars horizontal
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

    // Data for bars_last_test
    const barsDataRaw = [<?= $bars_data ?>];
    const barsLabels = barsDataRaw.map(row => row[0]);
    const downloadData = barsDataRaw.map(row => row[1]);
    const uploadData = barsDataRaw.map(row => row[2]);

    const barsDatasets = [{
        label: 'Download',
        data: downloadData,
        backgroundColor: '#0A83F9'
    }, {
        label: 'Upload',
        data: uploadData,
        backgroundColor: '#FEBC37'
    }];
    createBarChart('bars_last_test_canvas', 'Speedtest de todos los Servers', 'Download y Upload según último reporte', barsLabels, barsDatasets, true);

    // Data for bars_last_ping
    const barsPingDataRaw = [<?= $bars_data_ping ?>];
    const barsPingLabels = barsPingDataRaw.map(row => row[0]);
    const pingData = barsPingDataRaw.map(row => row[1]);

    const barsPingDatasets = [{
        label: 'Ping',
        data: pingData,
        backgroundColor: '#34A84F'
    }];
    createBarChart('bars_last_ping_canvas', 'Ping de todos los Servers', '', barsPingLabels, barsPingDatasets, true);
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