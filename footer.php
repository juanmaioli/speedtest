<script src="js/bootstrap.bundle.min.js?version=5.3.0"></script>
<script src="js/theme.js"></script>
<div class="separador"></div>
<footer>
  <div class="row mt-2">
    <div class="col-12 text-center">
    <h6 class="text-muted mb-0">Desarrollado con ❤️ por <strong>Juan Gabriel Maioli</strong> &bull; 2026</h6>
    </div>
  </div>
</footer>
</body>
<script>
  async function changeView() {
    const switchTableCard = document.querySelector('#switchTableCard')
    const switchTableCardLabel = document.querySelector('#switchTableCardLabel')
    if (switchTableCard.checked) {
      switchTableCardLabel.innerHTML = '🌙'
      changeTheme('dark')
    } else {
      switchTableCardLabel.innerHTML = '☀️'
      changeTheme('light')
    }
  }
</script>
</html>