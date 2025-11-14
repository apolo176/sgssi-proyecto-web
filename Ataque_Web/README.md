# 1. Introducción
 
 En este fichero se explica cómo instalar todos los programas utilizados en el ataque web.

# 2. Web atacada
La web que se ha atacado está almacenada en la rama *entrega_1* del siguiente repositorio:
[github.com/TabataMorente/ProyectoSeguridad.git](https://github.com/TabataMorente/ProyectoSeguridad.git)

# 3. Programas usados

Se han usado 4 programas extra además de ZAP para llevar acabo el ataque:

- *sqlmap*: Ataques mediante inyección SQL

- *OWASP DirBuster*: Ataque mediante diccionario y fuerza bruta

- *hydra*:

- Burp Suite:

# 4. Cómo instalar los distintos programas

### 1. *sqlmap*
Abrir una terminal de Linux e introducir el siguiente comando:
```bash
sudo snap install sqlmap
```
Ya está instalado. *sqlmap* se utiliza mediante la terminal, no tiene interfaz gráfica.

### 2. *OWASP DirBuster*

Descomprimir DirBuster-1.0-RC1.zip de la carpeta Ataque_Web

Moverte al directorio:
```bash
cd DirBuster-1.0-RC1/
```
Ejecutar el siguiente comando:
```bash
./DirBuster-1.0-RC1.sh
```

### 3. *hydra*
Abrir una terminal de Linux e introducir el siguiente comando:
```bash
sudo apt install hydra
```

### 4. *Burp Suite*

Acceder a la web https://portswigger.net/burp/documentation/desktop/getting-started/download-and-install .
Elija la opcion "Community Edition". Elija el sistema operativo en el que se instalará el programa. Una vez descargado, en Linux, ejecutar el archivo.sh . 
En distribuciones como Kali Linux el programa esta instalado por defecto.

# 5. Autores

- Eder Torres

- Liviu Deleanu

- Jon Requies

- Alex Isasi

- Iker Ciordia


