<?php
function getSpeedtestData($conn) {
    $output = [
        'bars_data' => '',
        'bars_data_ping' => '',
        'data_last' => '',
        'data_list_for_json' => [],
        'chart_labels' => [],
        'chart_down' => [],
        'chart_up' => [],
        'chart_ping' => []
    ];

    $st_date_diff = new DateTime(date("Y-m-d H:i:s"));

    // Consulta optimizada para obtener el último registro de cada IP (soluciona el problema N+1)
    $sql = "
        SELECT 
            t1.st_ip, t1.st_down, t1.st_up, t1.st_ping, t1.st_date, i.ip_name
        FROM 
            speedtest t1
        INNER JOIN (
            SELECT st_ip, MAX(st_id) AS max_id
            FROM speedtest
            GROUP BY st_ip
        ) t2 ON t1.st_ip = t2.st_ip AND t1.st_id = t2.max_id
        INNER JOIN 
            ips i ON t1.st_ip = i.ip_number
        WHERE 
            i.ip_delete = 0
        ORDER BY 
            i.ip_name ASC
    ";

    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $st_ip = $row["st_ip"];
            $st_down = $row["st_down"];
            $st_up = $row["st_up"];
            $st_ping = $row["st_ping"];
            $st_date = $row["st_date"];
            $ip_name = $row["ip_name"];

            // --- Preparar datos para Gráficos de Google ---
            $output['bars_data'] .= "['" . $ip_name . "\n" . $st_ip . "\n" . $st_date . "' ,$st_down ,$st_up],";
            $output['bars_data_ping'] .= "['" . $ip_name . "' ,$st_ping],";

            // --- Preparar datos para Chart.js ---
            $output['chart_labels'][] = $ip_name;
            $output['chart_down'][] = (float)$st_down;
            $output['chart_up'][] = (float)$st_up;
            $output['chart_ping'][] = (float)$st_ping;

            // --- Preparar datos para la sección "Último Reporte" ---
            $st_report_date = $st_date_diff->diff(new DateTime($st_date));
            $diff_minutes = $st_report_date->days * 24 * 60;
            $diff_minutes += $st_report_date->h * 60;
            $diff_minutes += $st_report_date->i;

            $color = ($diff_minutes > 60) ? "btn-danger" : "btn-success";
            $json_color = ($diff_minutes > 59) ? "danger" : "success";
            
            $output['data_last'] .= "
                <div class='col text-center mb-2'>
                    <form action='obj.php' method='post'>
                        <input type='hidden' id='ip_test' name='ip_test' value='$st_ip'>
                        <div id='$st_ip'>
                            <button class='btn $color w-100 shadow-sm border-0 py-2'>
                                🖥️ <strong>$ip_name</strong><br>
                                <small>🕒 Hace $diff_minutes min.</small>
                            </button>
                        </div>
                    </form>
                </div>";

            // --- Preparar datos para la actualización via JSON (utils.php) ---
            $output['data_list_for_json'][] = [
                'st_ip' => $st_ip,
                'st_date' => $st_date,
                'ip_name' => $ip_name,
                'color' => $json_color,
                'diff_minutes' => $diff_minutes
            ];
        }
    }
    
    return $output;
}

function getAvgTables($conn) {
    $output = [
        'table_down' => '',
        'table_up' => '',
        'table_ping' => ''
    ];

    // --- Tabla de promedios de Download ---
    $sql_down = "SELECT ips.ip_name, speedtest.st_ip, round(Avg(speedtest.st_down), 1) AS PromDownload FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE ips.ip_delete = 0 GROUP BY speedtest.st_ip ORDER BY PromDownload DESC";
    $result_down = $conn->query($sql_down);
    $table_down = "<div class='table-responsive shadow-sm rounded border-0 bg-body'><table class='table table-striped table-hover table-sm mb-0'><thead><tr class='table-primary'><th colspan='2' class='text-center'>⬇️ AVG Download (Mbps)</th></tr></thead>";
    if ($result_down) {
        while ($row = $result_down->fetch_assoc()) {
            $table_down .= "<tr><td class='ps-3'>" . htmlspecialchars($row["ip_name"]) . " <small class='text-muted'>(" . htmlspecialchars($row["st_ip"]) . ")</small></td><td class='text-end pe-3 font-monospace fw-bold'>" . $row["PromDownload"] . "</td></tr>";
        }
    }
    $output['table_down'] = $table_down . "</table></div>";

    // --- Tabla de promedios de Upload ---
    $sql_up = "SELECT ips.ip_name, st_ip, round(avg(st_up), 1) AS PromUpload FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE ips.ip_delete = 0 GROUP BY st_ip ORDER BY PromUpload DESC";
    $result_up = $conn->query($sql_up);
    $table_up = "<div class='table-responsive shadow-sm rounded border-0 bg-body'><table class='table table-striped table-hover table-sm mb-0'><thead><tr class='table-success'><th colspan='2' class='text-center'>⬆️ AVG Upload (Mbps)</th></tr></thead>";
     if ($result_up) {
        while ($row = $result_up->fetch_assoc()) {
            $table_up .= "<tr><td class='ps-3'>" . htmlspecialchars($row["ip_name"]) . " <small class='text-muted'>(" . htmlspecialchars($row["st_ip"]) . ")</small></td><td class='text-end pe-3 font-monospace fw-bold'>" . $row["PromUpload"] . "</td></tr>";
        }
    }
    $output['table_up'] = $table_up . "</table></div>";

    // --- Tabla de promedios de Ping ---
    $sql_ping = "SELECT ips.ip_name, st_ip, round(avg(st_ping), 1) AS PromPing FROM speedtest INNER JOIN ips ON speedtest.st_ip = ips.ip_number WHERE ips.ip_delete = 0 GROUP BY st_ip ORDER BY PromPing ASC";
    $result_ping = $conn->query($sql_ping);
    $table_ping = "<div class='table-responsive shadow-sm rounded border-0 bg-body'><table class='table table-striped table-hover table-sm mb-0'><thead><tr class='table-warning'><th colspan='2' class='text-center'>⚡ AVG Ping (ms)</th></tr></thead>";
    if ($result_ping) {
        while ($row = $result_ping->fetch_assoc()) {
            $table_ping .= "<tr><td class='ps-3'>" . htmlspecialchars($row["ip_name"]) . " <small class='text-muted'>(" . htmlspecialchars($row["st_ip"]) . ")</small></td><td class='text-end pe-3 font-monospace fw-bold'>" . $row["PromPing"] . "</td></tr>";
        }
    }
    $output['table_ping'] = $table_ping . "</table></div>";

    return $output;
}
?>
