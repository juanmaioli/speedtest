#! /bin/python3
import urllib.request
import subprocess

listaServidores = subprocess.Popen('speedtest-cli --secure --list', shell=True, stdout=subprocess.PIPE).stdout.read().decode()
listaServidores = listaServidores.splitlines()
servidor = listaServidores[1].split(')')[0]

testspeed = subprocess.Popen('speedtest-cli --simple --secure --server ' + servidor, shell=True, stdout=subprocess.PIPE).stdout.read().decode()
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

url_raw = 'https://pikapp.com.ar/'+testspeed[0]+'-'+testspeed[1]+'-'+testspeed[2]+'-casa_fibra'
response = urllib.request.urlopen(url_raw)
headers = response.getheaders()
content_type = response.getheader('Content-Type')
rta = response.read().decode()
print(rta)