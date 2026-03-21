<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Medidor de Velocidad">
  <meta name="author" content="Juan Maioli">
  <title id='titulo'>Speedtest Web <?= $ip_client ?></title>
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css?version=5.3.0">
  <!-- Bootstrap Color Extension CSS -->
  <link rel="stylesheet" href="css/bootstrap-color-extension.css?version=1.6.0">
  <!-- FontAwesome CSS -->
  <link rel="stylesheet" href="css/all.min.css?version=6.4.0">
  <link rel="stylesheet" href="css/bootstrap-color-extension.css?version=1.6.0">
  <!-- Proyect CSS -->
  <link rel="stylesheet" href="css/style.css">
  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin">
  <style>@import url('https://fonts.googleapis.com/css2?family=Lato:wght@300&family=Montserrat:wght@400&family=Roboto:wght@300&display=swap'); </style>
      <link rel='icon' href='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏁</text></svg>'>

  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="theme-color" content="#ffffff">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Header Hero -->
<div class="hero-section text-center text-white shadow">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3">🏁 Speedtest Web</h1>
        <p class="lead opacity-75">Basao en fast.com·</p>
        <p class="text-muted mb-0 ms-1">
          <i class="fa-solid fa-location-dot me-1"></i> Su IP actual: <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($ip_client) ?></span>
        </p>

</div>
</div>
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 1000;">
    <button class="btn btn-outline-secondary bg-body shadow-sm" id="btn-theme" onclick="toggleTheme()"></button>
  </div>


