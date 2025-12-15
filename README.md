# Speedtest Logger

Es un sistema simple para registrar y visualizar pruebas de velocidad de conexión a internet de manera periódica desde múltiples servidores o clientes. Utiliza `speedtest-cli` para las mediciones, un panel web en PHP para la visualización y un cliente Python para enviar los datos.

## ✨ Características

*   **Panel Web:** Interfaz limpia que muestra el último reporte de cada cliente.
*   **Gráficos Históricos:** Visualización de promedios de subida, bajada y ping.
*   **Gráficos en Tiempo Real:** Gráficos que muestran el estado del último test de todos los servidores.
*   **Cliente Ligero:** Un script en Python se encarga de realizar el test y enviar los resultados.
*   **Fácil Automatización:** Diseñado para ser ejecutado fácilmente a través de un `cron job`.

## 📋 Requisitos

*   Servidor web con **PHP 7.4** o superior.
*   Base de datos **MySQL** o **MariaDB**.
*   **Python 3.x** y el paquete `speedtest-cli` en los clientes que realizarán las pruebas.

## 🚀 Instalación

### 1. Speedtest-CLI

En cada cliente que vaya a realizar las pruebas, instala `speedtest-cli`.

*   **Debian/Ubuntu:** `sudo apt update && sudo apt install speedtest-cli`
*   **Fedora/CentOS:** `sudo yum install speedtest`
*   **macOS (con Homebrew):** `brew install speedtest-cli`

Para más información, visita [speedtest.net/apps/cli](https://www.speedtest.net/es/apps/cli).

### 2. Repositorio en el Servidor Web

Clona este repositorio en un directorio accesible por tu servidor web (ej. `/var/www/html/`).

```bash
git clone https://github.com/juanmaioli/speedtest.git
```

### 3. Base de Datos

Crea una base de datos en tu servidor MySQL y ejecuta las siguientes consultas para crear las tablas `speedtest` e `ips`.

```sql
CREATE TABLE `speedtest` (
  `st_id` int(11) NOT NULL AUTO_INCREMENT,
  `st_ping` float(6,2) DEFAULT NULL,
  `st_down` float(6,2) DEFAULT NULL,
  `st_up` float(6,2) DEFAULT NULL,
  `st_ip` varchar(15) NOT NULL,
  `st_date` datetime NOT NULL,
  PRIMARY KEY (`st_id`,`st_ip`,`st_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb3;

CREATE TABLE `ips` (
  `ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_number` varchar(20) NOT NULL,
  `ip_name` varchar(255) DEFAULT NULL,
  `ip_delete` int(1) DEFAULT 0,
  `ip_alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ip_id`,`ip_number`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb3;
```
En la tabla `ips`, puedes pre-cargar los servidores que vas a monitorear, asignándoles un `ip_alias` y un `ip_name` para identificarlos fácilmente.

### 4. Configuración del Servidor

Renombra `config_example.php` a `config.php` y edítalo con los datos de acceso a tu base de datos.

```php
<?php
  // config.php
  $db_server      = "localhost";    // Servidor de la DB
  $db_user        = "db_user";      // Usuario de la DB
  $db_pass        = "db_password";  // Clave de la DB
  $db_name        = "db_name";      // Nombre de la DB
  $db_serverport  = "3306";
?>
```

## 💻 Configuración del Cliente

El script `client.py` incluido en el repositorio es el encargado de correr el test y enviar los datos a tu servidor. Para facilitar su ejecución, puedes usar el script de ejemplo `speedtest.sh.example`.

1.  **Copia el ejemplo:** Copia `speedtest.sh.example` a una ubicación personal, por ejemplo, `/home/tu_usuario/speedtest.sh`.

    ```bash
    cp speedtest.sh.example /home/tu_usuario/speedtest.sh
    ```

2.  **Edita el script:** Modifica las variables `PROJECT_DIR` y `RAW_URL` dentro de `speedtest.sh` para que apunten a tu instalación y dominio.

3.  **Da permisos de ejecución:**

    ```bash
    chmod +x /home/tu_usuario/speedtest.sh
    ```

## 🤖 Automatización con Cron

Para que las pruebas se ejecuten automáticamente, crea una tarea en `cron`.

1.  Abre el editor de cron:
    ```bash
    crontab -e
    ```

2.  Añade una línea para ejecutar tu script `speedtest.sh` en el intervalo que prefieras. Este ejemplo lo ejecuta cada 30 minutos:
    ```cron
    */30 * * * * /home/tu_usuario/speedtest.sh
    ```

¡Y eso es todo! Tu cliente comenzará a reportar datos de velocidad a tu servidor y podrás verlos en el panel web.