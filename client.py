#!/usr/bin/env python3
import subprocess
import urllib.request
import sys

def main():
    try:
        # Ejecutar el test de velocidad sin mostrar nada
        result = subprocess.run(
            ["speedtest-cli", "--simple", "--secure"],
            capture_output=True,
            text=True,
            check=True
        )
        lines = result.stdout.strip().splitlines()

        if len(lines) < 3:
            sys.exit(1)

        # Extraer valores y limpiar formato
        ping = lines[0].replace("Ping: ", "").replace(" ms", "").replace(".", "p")
        download = lines[1].replace("Download: ", "").replace(" Mbit/s", "").replace(".", "p")
        upload = lines[2].replace("Upload: ", "").replace(" Mbit/s", "").replace(".", "p")

        # Construir URL de envío
        url = f"http://172.21.5.3/speedtest/raw.php?id={ping}-{download}-{upload}-casa_fibra"

        # Enviar resultado sin imprimir nada
        urllib.request.urlopen(url, timeout=10).read()

    except Exception:
        # No mostrar errores, solo salir silenciosamente
        pass

if __name__ == "__main__":
    main()