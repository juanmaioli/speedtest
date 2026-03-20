
/**
 * Dibuja un gauge radial en un canvas.
 * @param {string} id - ID del elemento canvas.
 * @param {number} data - Valor actual a mostrar.
 * @param {object} [options] - Opciones de configuración opcionales.
 */
function gaugeDraw(id, data, options = {}) {
  const c = document.getElementById(id);
  const ctx = c.getContext("2d");

  // --- Configuración por defecto (Default Configuration) ---
  const config = {
    min: options.min !== undefined ? options.min : -50,
    max: options.max !== undefined ? options.max : 50,
    unit: options.unit || String.fromCharCode(176) + "C",
    colors: options.colors || [
      { max: 8, color: "#007BFF" },   // Azul
      { max: 27, color: "#28A745" },  // Verde
      { max: 30, color: "#FFC107" },  // Amarillo
      { max: Infinity, color: "#DC3545" } // Rojo (Resto)
    ]
  };

  // --- Dimensiones Dinámicas ---
  const width = c.width;
  const height = c.height;
  const X = width / 2;
  const Y = height / 1.7; 
  const minDimension = Math.min(width, height);
  const outterRadius = minDimension * 0.45;
  const innerRadius = outterRadius * 0.80;

  // --- Limpieza ---
  ctx.clearRect(0, 0, width, height);

  // --- Lógica de Color ---
  let activeColor = "#000"; 
  const sortedColors = [...config.colors].sort((a, b) => a.max - b.max);
  
  for (const range of sortedColors) {
    if (data <= range.max) {
      activeColor = range.color;
      break;
    }
  }

  // --- Lógica Matemática de Ángulos ---
  const startAngle = Math.PI * 0.85;
  const endAngle = Math.PI * 2.15;
  const totalAngle = endAngle - startAngle;

  const totalRange = config.max - config.min;
  const progress = (data - config.min) / totalRange;
  const clampedProgress = Math.max(0, Math.min(1, progress));
  
  const currentAngle = startAngle + (totalAngle * clampedProgress);

  // --- Renderizado de Texto Inteligente ---
  let mainFontSize = Math.round(outterRadius * 0.45); // Base font size
  const labelFontSize = Math.round(outterRadius * 0.18);
  
  const mainText = data + " " + config.unit;
  
  ctx.textAlign = "center";
  ctx.textBaseline = "middle";
  ctx.fillStyle = "#bbb";

  // Ajuste automático de fuente para el texto principal
  ctx.font = `${mainFontSize}px sans-serif`;
  let textWidth = ctx.measureText(mainText).width;
  const maxTextWidth = innerRadius * 1.5; // Espacio máximo disponible (aprox 1.5 veces el radio interior)

  if (textWidth > maxTextWidth) {
    // Escalar hacia abajo si excede el ancho
    const scaleFactor = maxTextWidth / textWidth;
    mainFontSize = Math.floor(mainFontSize * scaleFactor);
    ctx.font = `${mainFontSize}px sans-serif`;
  }

  // Valor Principal
  ctx.fillText(mainText, X, Y + (innerRadius * 0.4));

  // Etiquetas Min/Max
  ctx.font = `${labelFontSize}px sans-serif`;
  ctx.fillText(config.min + config.unit, X - (outterRadius * 0.8), Y + (outterRadius * 0.7));
  ctx.fillText(config.max + config.unit, X + (outterRadius * 0.8), Y + (outterRadius * 0.7));

  // --- Renderizado de Arcos ---
  setRadialGradient("#EEE", "#EEE");
  drawDonut(startAngle, endAngle);

  setRadialGradient(activeColor, activeColor);
  drawDonut(startAngle, currentAngle);

  // --- Funciones Auxiliares ---
  function drawDonut(sRadian, eRadian){
      ctx.beginPath();
      ctx.strokeStyle = ctx.fillStyle;
      ctx.lineWidth = outterRadius - innerRadius;
      ctx.lineCap = "round";
      ctx.arc(X, Y, (outterRadius + innerRadius) / 2, sRadian, eRadian, false);
      ctx.stroke();
  }

  function setRadialGradient(sgc, bgc){
      const grd = ctx.createRadialGradient(X, Y, innerRadius, X, Y, outterRadius);
      grd.addColorStop(0, sgc);
      grd.addColorStop(1, bgc);
      ctx.fillStyle = grd;
  }
}
