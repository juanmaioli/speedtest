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
        <div class="card border-0 shadow-sm bg-body-tertiary mb-5">
            <div class="card-header border-0 text-center py-3 bg-indigo border border-seco">
            <div class="row mt-1">
              <div class="col-md-12">
                <div class="p-1 shadow-sm rounded-4">
                  <h3 class="text-white">🔌 Estado de los Nodos</h3>
                  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                    <?= $data_last ?>
                  </div>
                </div>
              </div>
            </div>
            </div>
          <div class="card-body px-4">
            <div class="row g-4">
              <div class="col-lg-8">
                <div class="bg-body p-3 shadow-sm rounded-4 border border-secondary-subtle h-100">
                  <canvas id="chart_down_up" height="400"></canvas>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="bg-body p-3 shadow-sm rounded-4 border border-secondary-subtle h-100">
                  <canvas id="chart_ping" height="400"></canvas>
                </div>
              </div>
            </div>

            <div class="row mt-4 g-4 mb-3">
              <div class="col-md-4">
                <div class="h-100">
                  <?= $table_down ?>
                </div>
              </div>
              <div class="col-md-4">
                <div class="h-100">
                  <?= $table_up ?>
                </div>
              </div>
              <div class="col-md-4">
                <div class="h-100">
                  <?= $table_ping ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-1"></div>
    </div>
  </div>
  <br><br><br>
  <script>
    const labels = <?= json_encode($speedtestData['chart_labels']) ?>;
    const dataDown = <?= json_encode($speedtestData['chart_down']) ?>;
    const dataUp = <?= json_encode($speedtestData['chart_up']) ?>;
    const dataPing = <?= json_encode($speedtestData['chart_ping']) ?>;

    const ctxDownUp = document.getElementById('chart_down_up').getContext('2d');
    new Chart(ctxDownUp, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Download (Mb/s)',
          data: dataDown,
          backgroundColor: 'rgba(10, 131, 249, 0.7)',
          borderColor: 'rgba(10, 131, 249, 1)',
          borderWidth: 1
        }, {
          label: 'Upload (Mb/s)',
          data: dataUp,
          backgroundColor: 'rgba(52, 168, 79, 0.7)',
          borderColor: 'rgba(52, 168, 79, 1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: { display: true, text: 'Download y Upload por Servidor' }
        },
        scales: { x: { beginAtZero: true } }
      }
    });

    const ctxPing = document.getElementById('chart_ping').getContext('2d');
    new Chart(ctxPing, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Ping (ms)',
          data: dataPing,
          backgroundColor: 'rgba(254, 188, 55, 0.7)',
          borderColor: 'rgba(254, 188, 55, 1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: { display: true, text: 'Ping por Servidor' }
        },
        scales: { x: { beginAtZero: true } }
      }
    });
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