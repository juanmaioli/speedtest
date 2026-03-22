# 📊 Speedtest Web Dashboard

<p align="center">
  <img src="images/speedometer.png" alt="Speedtest Logo" width="120">
</p>

Panel de control centralizado para el monitoreo de velocidad de internet desde múltiples nodos remotos. Visualiza el rendimiento de tu red en tiempo real con gráficos elegantes y estadísticas detalladas.

---

## 🚀 Características Principales

- 📈 **Gráficos en Tiempo Real:** Visualización dinámica de Download, Upload y Ping mediante Chart.js.
- 🏢 **Monitoreo Multinodo:** Soporta múltiples servidores o IPs registradas.
- ⏱️ **Estadísticas de Promedio:** Tablas calculadas automáticamente para ver el rendimiento histórico.
- 🟢 **Indicadores de Estado:** Semáforo visual para saber si un nodo está reportando correctamente.
- 📱 **Interfaz Adaptable:** Diseño moderno basado en Bootstrap 5.3 con soporte para distintos temas y modo oscuro.

---

## 🏗️ Arquitectura del Sistema

El sistema se divide en dos partes:

1.  **Servidor Central (Web):**
    - Procesa los reportes mediante `raw.php`.
    - Almacena los datos en MySQL/MariaDB.
    - Renderiza el Dashboard principal (`index.php`).
    - Compatible con entornos locales de desarrollo (Docker/Vagrant).

2.  **Agentes de Monitoreo (Nodos):**
    - Ejecutan pruebas de velocidad usando `fast-cli`.
    - Reportan los resultados automáticamente al servidor central (soporta HTTPS y puertos personalizados).

---

## ⚙️ Configuración Rápida

### 1. Requisitos Previos
- Servidor Web (Apache/Nginx) con PHP 7.4+.
- Base de Datos MySQL/MariaDB.
- `fast-cli` instalado en los nodos (`npm install --global fast-cli`).

### 2. Instalación del Servidor
1. Clona este repositorio.
2. Crea una base de datos e importa las tablas necesarias.
3. Renombra `config_example.php` a `config.php` y completa tus credenciales.

### 3. Configuración del Cliente
1. Ajustá el script `Scripts/client.py` con la `base_url` de tu servidor:
   ```python
   base_url = "https://tu-servidor.com/speedtest/raw.php?id="
   ```
2. Configurá el script `speedtest.sh` con las rutas correctas.
3. Agregá una tarea al CRON para automatizar los reportes:
   ```bash
   */15 * * * * /ruta/a/Scripts/speedtest.sh
   ```

---

## 📂 Estructura de Archivos

| Archivo/Carpeta | Descripción |
| :--- | :--- |
| `index.php` | Panel principal de estadísticas y gráficos. |
| `data_logic.php` | Lógica de negocio y consultas SQL optimizadas. |
| `raw.php` | API para recepción de datos de los nodos. |
| `Scripts/` | Scripts de Python y Bash para ejecución en clientes. |
| `css/` & `js/` | Activos estáticos para el frontend (Bootstrap 5.3 + Chart.js). |

---

## 👨‍💻 Contribuciones

Este proyecto es mantenido por **Juan Gabriel Maioli**. Si encontrás errores o querés proponer mejoras, ¡sentite libre de abrir un Issue o Pull Request!

---

<p align="center">
  Hecho con ❤️ para una red más rápida.
</p>
