# 📊 Speedtest Web Dashboard

<p align="center">
  <img src="images/speedometer.png" alt="Speedtest Logo" width="120">
</p>

Panel de control centralizado para el monitoreo de velocidad de internet desde múltiples nodos remotos. Visualiza el rendimiento de tu red en tiempo real con gráficos elegantes y estadísticas detalladas.

---

## 🚀 Características Principales

- 📉 **Medidores Radiales Dinámicos:** Nuevos gauges personalizados en `obj.php` usando `gauge.js` para Ping, Download y Upload.
- 📈 **Gráficos Históricos:** Visualización de tendencias de las últimas 24hs, mensual y anual mediante Chart.js.
- 🏢 **Monitoreo Multinodo:** Soporta múltiples servidores o IPs registradas.
- ⏱️ **Estadísticas de Promedio:** Tablas calculadas automáticamente para ver el rendimiento histórico.
- 🟢 **Indicadores de Estado:** Semáforo visual para saber si un nodo está reportando correctamente.
- 📱 **Interfaz Adaptable:** Diseño moderno basado en Bootstrap 5.3 con soporte para distintos temas.

---

## 🏗️ Arquitectura del Sistema

El sistema se divide en dos partes:

1.  **Servidor Central (Web):**
    - Procesa los reportes mediante `raw.php`.
    - Almacena los datos en MySQL/MariaDB.
    - Renderiza el Dashboard principal (`index.php`).

2.  **Agentes de Monitoreo (Nodos):**
    - Ejecutan pruebas de velocidad usando `fast-cli`.
    - Reportan los resultados automáticamente al servidor central.

---

## ⚙️ Configuración Rápida

### 1. Requisitos Previos
- Servidor Web (Apache/Nginx) con PHP 7.4+.
- Base de Datos MySQL/MariaDB.
- `fast-cli` instalado en los nodos (`npm install --global fast-cli`).

### 2. Instalación del Servidor
1. Clona este repositorio.
2. Crea una base de datos e importa las tablas necesarias.
3. Renombra `config_example.php` a `config.php` y completa tus credenciales:
   ```php
   $db_server = "localhost";
   $db_user   = "tu_usuario";
   $db_pass   = "tu_contraseña";
   $db_name   = "speedtest_db";
   ```

### 3. Configuración del Cliente
1. Copia el contenido de `Scripts/` a tu servidor cliente.
2. Configura el script `speedtest.sh` con la URL de tu servidor central.
3. Agrega una tarea al CRON para automatizar los reportes:
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
| `css/` & `js/` | Activos estáticos para el frontend. |

---

## 👨‍💻 Contribuciones

Este proyecto es mantenido por **Juan Gabriel Maioli**. Si encuentras errores o quieres proponer mejoras, ¡siéntete libre de abrir un Issue o Pull Request!

---

<p align="center">
  Hecho con ❤️ para una red más rápida.
</p>
