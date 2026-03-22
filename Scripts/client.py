import subprocess
import json
import sys
import urllib.request

def run_speed_test():
    try:
        # 🚀 Ejecutamos el comando fast-cli con flags -u (unidades) y --json
        # Usamos capture_output=True para obtener la salida del comando
        result = subprocess.run(['fast', '-u', '--json'], capture_output=True, text=True, check=True)
        
        # 🧩 Convertimos el string JSON en un diccionario de Python
        data = json.loads(result.stdout)
        
        # 📦 Asignación a variables (ajustado a la estructura típica de fast-cli)
        # Nota: La estructura puede variar según la versión, pero 'downloadSpeed' suele estar presente.
        download_speed = data.get('downloadSpeed')
        upload_speed = data.get('uploadSpeed')
        latency = data.get('latency')
        buffer_bloat = data.get('bufferBloat')
        base_url = "https://webserver.docker:5443/speedtest/raw.php?id="
        url = f"{base_url}{latency}-{download_speed}-{upload_speed}-casa_fibra"
        # 📊 Mostrar los resultados asignados
        # print(f"📉 URL: {url}")

        # Enviar resultado sin imprimir nada
        urllib.request.urlopen(url, timeout=10).read()

        return download_speed, upload_speed, latency, buffer_bloat

    except subprocess.CalledProcessError as e:
        print(f"❌ Error al ejecutar 'fast': {e}", file=sys.stderr)
    except json.JSONDecodeError as e:
        print(f"❌ Error al decodificar el JSON: {e}", file=sys.stderr)
    except Exception as e:
        print(f"❌ Ocurrió un error inesperado: {e}", file=sys.stderr)

if __name__ == "__main__":
    download, upload, ping, bloat = run_speed_test()
