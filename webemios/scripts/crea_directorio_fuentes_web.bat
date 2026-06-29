cd ..

set directorio_fuentes="fuentes_web_vX.X.X.X"
echo %directorio_fuentes%

rd /S /Q %directorio_fuentes%
mkdir %directorio_fuentes%

mkdir %directorio_fuentes%\TLNT
xcopy TLNT %directorio_fuentes%\TLNT /E

mkdir %directorio_fuentes%\Wibeee
xcopy Wibeee %directorio_fuentes%\Wibeee /E

mkdir %directorio_fuentes%\includes
xcopy includes\* %directorio_fuentes%\includes

mkdir %directorio_fuentes%\log

mkdir %directorio_fuentes%\repositorio

mkdir %directorio_fuentes%\rsc

mkdir %directorio_fuentes%\rsc\config
xcopy rsc\config\* %directorio_fuentes%\rsc\config

mkdir %directorio_fuentes%\rsc\estilos
xcopy rsc\estilos\* %directorio_fuentes%\rsc\estilos

mkdir %directorio_fuentes%\rsc\ficheros
mkdir %directorio_fuentes%\rsc\ficheros\tmp
mkdir %directorio_fuentes%\rsc\ficheros\plantillas
xcopy rsc\ficheros\plantillas\*.* %directorio_fuentes%\rsc\ficheros\plantillas /E

mkdir %directorio_fuentes%\rsc\fuentes
xcopy rsc\fuentes\* %directorio_fuentes%\rsc\fuentes

mkdir %directorio_fuentes%\rsc\imagenes
xcopy rsc\imagenes\* %directorio_fuentes%\rsc\imagenes

mkdir %directorio_fuentes%\rsc\imagenes\logos
xcopy rsc\imagenes\logos\* %directorio_fuentes%\rsc\imagenes\logos

mkdir %directorio_fuentes%\rsc\imagenes\tmp

mkdir %directorio_fuentes%\rsc\lib
xcopy rsc\lib %directorio_fuentes%\rsc\lib /E

mkdir %directorio_fuentes%\rsc\idiomas
xcopy rsc\idiomas\* %directorio_fuentes%\rsc\idiomas

mkdir %directorio_fuentes%\rsc\mapas

mkdir %directorio_fuentes%\scripts
xcopy scripts\*.sh %directorio_fuentes%\scripts

mkdir %directorio_fuentes%\scripts\etc
xcopy scripts\etc\php.ini %directorio_fuentes%\scripts\etc
mkdir %directorio_fuentes%\scripts\etc\httpd
mkdir %directorio_fuentes%\scripts\etc\httpd\conf
mkdir %directorio_fuentes%\scripts\etc\httpd\conf.d
xcopy scripts\etc\httpd\conf\* %directorio_fuentes%\scripts\etc\httpd\conf
xcopy scripts\etc\httpd\conf.d\* %directorio_fuentes%\scripts\etc\httpd\conf.d
mkdir %directorio_fuentes%\scripts\var
mkdir %directorio_fuentes%\scripts\var\www
mkdir %directorio_fuentes%\scripts\var\www\html
xcopy scripts\var\www\html\.htaccess %directorio_fuentes%\scripts\var\www\html

mkdir %directorio_fuentes%\src
xcopy src %directorio_fuentes%\src /E

mkdir %directorio_fuentes%\comun

mkdir %directorio_fuentes%\comun\includes
xcopy comun\includes %directorio_fuentes%\comun\includes /E

mkdir %directorio_fuentes%\comun\log
xcopy comun\log %directorio_fuentes%\comun\log /E

mkdir %directorio_fuentes%\comun\mantenimiento
xcopy comun\mantenimiento %directorio_fuentes%\comun\mantenimiento /E

mkdir %directorio_fuentes%\comun\rsc

mkdir %directorio_fuentes%\comun\rsc\estilos
xcopy comun\rsc\estilos %directorio_fuentes%\comun\rsc\estilos /E

mkdir %directorio_fuentes%\comun\rsc\idiomas
xcopy comun\rsc\idiomas %directorio_fuentes%\comun\rsc\idiomas /E

mkdir %directorio_fuentes%\comun\rsc\imagenes
xcopy comun\rsc\imagenes\* %directorio_fuentes%\comun\rsc\imagenes

mkdir %directorio_fuentes%\comun\rsc\lib
xcopy comun\rsc\lib %directorio_fuentes%\comun\rsc\lib /E

mkdir %directorio_fuentes%\comun\src
xcopy comun\src %directorio_fuentes%\comun\src /E

mkdir %directorio_fuentes%\comun\TLNT
xcopy comun\TLNT %directorio_fuentes%\comun\TLNT /E

del %directorio_fuentes%\src\api\comprueba_tipo_peticion_api.php
del %directorio_fuentes%\src\api\directorio_raiz_api.php
xcopy comun\src\api\comprueba_tipo_peticion_api.php %directorio_fuentes%\src\api\
xcopy comun\src\api\directorio_raiz_api.php %directorio_fuentes%\src\api\
del %directorio_fuentes%\comun\src\api\comprueba_tipo_peticion_api.php
del %directorio_fuentes%\comun\src\api\directorio_raiz_api.php

xcopy comun\*.php %directorio_fuentes%
xcopy comun\*.jpg %directorio_fuentes%
xcopy comun\*.txt %directorio_fuentes%
xcopy interno.php %directorio_fuentes%

mkdir %directorio_fuentes%\css
xcopy css %directorio_fuentes%\css /E
mkdir %directorio_fuentes%\js
xcopy js %directorio_fuentes%\js /E
