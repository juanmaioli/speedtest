<script src="js/bootstrap.bundle.min.js?version=5.3.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/chart.umd.min.js"></script>
<script src="js/theme.js"></script>
<div class="separador"></div>
<footer>
  <div class="row mt-2">
    <div class="col-1">
    </div>
    <div class="col-10 text-center">
      <a href="https://juanmaioli.com.ar" class="text-decoration-none" target="_blank">
        <h6 class="text-white mt-2 fa-beat float-right m-2" style="--fa-animation-duration: 5s; --fa-fade-opacity: 0.1;  --fa-beat-scale: 0.8;">
          <i class="fa-regular fa-code"></i>&nbsp;juanmaioli.com.ar
        </h6>
      </a>
    </div>
    <div class="col-1">
      <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" role="switch" id="switchTableCard" onchange="changeView()">
      <label class="form-check-label text-white" id="switchTableCardLabel" for="switchTableCard" onclick="document.getElementById('switchTableCard').toogle"><i class="fa-regular fa-sun fa-fw fa-2x"></i></label>  
    </div>
    </div>
  </div>
</footer>
</body>
<script>
  async function changeView() {
    const switchTableCard = document.querySelector('#switchTableCard')
    const switchTableCardLabel = document.querySelector('#switchTableCardLabel')
    if (switchTableCard.checked) {
      switchTableCardLabel.innerHTML = '<i class="fa-regular fa-moon-stars fa-fw fa-2x"></i>'
      changeTheme('dark')
    } else {
      switchTableCardLabel.innerHTML = '<i class="fa-regular fa-sun fa-fw fa-2x"></i>'
      changeTheme('light')
    }
  }
</script>
</html>