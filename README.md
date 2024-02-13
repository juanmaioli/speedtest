# Speedtest Logging (PHP, MySql & Python)

Es un sistema que te permite llevar un control y estadísticas de la velocidad de conexión de tus servidores.

## 1 - Instalar speedtest-cli
#### 1.1 - Debian/Ubuntu
```bash
    sudo apt-get update
    sudo apt-get install speedtest
```

#### 1.2 - Fedora/Centos
```bash
    sudo yum install wget
    wget https://bintray.com/ookla/rhel/rpm -O bintray-ookla-rhel.repo
    sudo mv bintray-ookla-rhel.repo /etc/yum.repos.d/
    sudo yum install speedtest
```

#### 1.3 - MacOS
```bash
    brew tap teamookla/speedtest
    brew update
    brew install speedtest --force
```
[Más info: www.speedtest.net](www.speedtest.net/es/apps/cli)

## 2 - Clonar el repocitorio en el directorio de tu web server con php (Ej. /var/www)
```bash
git clone
```

## 2 - Crear una db en Mysql y Crear la tabla speedtest
```sql
CREATE TABLE `speedtest` (
  `st_id` int(11) NOT NULL AUTO_INCREMENT,
  `st_ping` float(6,2) DEFAULT NULL,
  `st_down` float(6,2) DEFAULT NULL,
  `st_up` float(6,2) DEFAULT NULL,
  `st_ip` varchar(15) NOT NULL,
  `st_date` datetime NOT NULL,
  PRIMARY KEY (`st_id`,`st_ip`,`st_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
```

## 2.1 - Crear la tabla ips
```sql
CREATE TABLE `ips` (
  `ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_number` varchar(20) NOT NULL,
  `ip_name` varchar(255) DEFAULT NULL,
  `ip_delete` int(255) DEFAULT 0,
  `ip_alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ip_id`,`ip_number`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
```

## 2.2 - Editar config_example.php y renombrar a config.php
```php
<?php
  $db_server      = "localhost";// Db Servidor
  $db_user        = "db_user";// Db Usuario
  $db_pass        = "db_password";//Db Clave
  $db_name        = "db_name";//DB nombre
  $db_serverport  = "3306";
?>
```

## 3 - Crear client.py en el home del usuario
```bash
  nano client.py
```
#### Contenido de client.py
Reemplazar alias con un nombre corto para tu servidor manteniendo el guion.
```python
import urllib.request
import subprocess

testspeed = subprocess.Popen('speedtest-cli --simple --secure', shell=True, stdout=subprocess.PIPE).stdout.read().decode()
testspeed = testspeed.splitlines()

testspeed[0] = testspeed[0].replace('Ping: ','')
testspeed[0] = testspeed[0].replace(' ms','')
testspeed[1] = testspeed[1].replace('Download: ','')
testspeed[1] = testspeed[1].replace(' Mbit/s','')
testspeed[2] = testspeed[2].replace('Upload: ','')
testspeed[2] = testspeed[2].replace(' Mbit/s','')
testspeed[0] = testspeed[0].replace('.','p')
testspeed[1] = testspeed[1].replace('.','p')
testspeed[2] = testspeed[2].replace('.','p')

url_raw = 'https://pikapp.com.ar/'+testspeed[0]+'-'+testspeed[1]+'-'+testspeed[2]+'-alias'
response = urllib.request.urlopen(url_raw)
headers = response.getheaders()
content_type = response.getheader('Content-Type')
rta = response.read().decode()
print(rta)
```

## 4 - Crear speedtest.sh en el home del usuario
```bash
  nano speedtest.sh
```

#### Contenido de speedtest.sh
```bash
 #!/bin/bash
 /usr/bin/python3 /home/usuario/client.py
```

## 5 - Crear una tarea en cron
```bash
  crontab -e
  # Agregar la linea para ejecutar la tarea en el minuto 30 cada hora.
  */30   *       *       *       *       sudo /home/usuario/speedtest.sh
```
