const storedTheme = localStorage.getItem('theme') || 'light' // Default to light if no theme is stored
const themeIcon = {
  dark:'<i class="fa-regular fa-moon-stars fa-fw"></i><span class="d-lg-none ms-2">Modo Claro</span>',
  light:'<i class="fa-regular fa-sun fa-fw"></i><span class="d-lg-none ms-2">Modo Oscuro</span>'
}

// Set initial theme and icon based on storedTheme
document.documentElement.setAttribute('data-bs-theme', storedTheme)
// Check if btn-theme exists before trying to set its innerHTML
const btnTheme = document.querySelector('#btn-theme')
if (btnTheme) {
  btnTheme.innerHTML = themeIcon[storedTheme]
  btnTheme.setAttribute('aria-label', storedTheme === 'dark' ? 'Toggle light mode' : 'Toggle dark mode')
}


function changeTheme(theme){
  document.documentElement.setAttribute('data-bs-theme', theme)
  localStorage.setItem('theme', theme)
  if (btnTheme) {
    btnTheme.innerHTML = themeIcon[theme]
    btnTheme.setAttribute('aria-label', theme === 'dark' ? 'Toggle light mode' : 'Toggle dark mode')
  }
}

function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-bs-theme')
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark'
  changeTheme(newTheme)
}
