#!/bin/bash

cd ..

FUENTES="fuentes_web_vX.X.X.X"
echo $FUENTES


rm -rf $FUENTES
mkdir -p $FUENTES

mkdir -p $FUENTES/TLNT
cp -r TLNT/* $FUENTES/TLNT

mkdir -p $FUENTES/Wibeee
cp -r Wibeee/* $FUENTES/Wibeee

mkdir -p $FUENTES/includes
cp -r includes/* $FUENTES/includes

mkdir -p $FUENTES/log

mkdir -p $FUENTES/repositorio

mkdir -p $FUENTES/rsc

mkdir -p $FUENTES/rsc/config
cp -r rsc/config/* $FUENTES/rsc/config

mkdir -p $FUENTES/rsc/estilos
cp -r rsc/estilos/* $FUENTES/rsc/estilos

mkdir -p $FUENTES/rsc/ficheros
mkdir -p $FUENTES/rsc/ficheros/tmp
mkdir -p $FUENTES/rsc/ficheros/plantillas
cp -r rsc/ficheros/plantillas/*.* $FUENTES/rsc/ficheros/plantillas

mkdir -p $FUENTES/rsc/fuentes
cp -r rsc/fuentes/* $FUENTES/rsc/fuentes

mkdir -p $FUENTES/rsc/imagenes
cp -r rsc/imagenes/* $FUENTES/rsc/imagenes

mkdir -p $FUENTES/rsc/imagenes/logos
cp -r rsc/imagenes/logos/* $FUENTES/rsc/imagenes/logos

mkdir -p $FUENTES/rsc/imagenes/tmp

mkdir -p $FUENTES/rsc/lib
cp -r rsc/lib/* $FUENTES/rsc/lib

mkdir -p $FUENTES/rsc/idiomas
cp -r rsc/idiomas/* $FUENTES/rsc/idiomas

mkdir -p $FUENTES/rsc/mapas

mkdir -p $FUENTES/scripts
find scripts/ -type f -print0 | xargs -0 dos2unix
cp -r scripts/*.sh $FUENTES/scripts

mkdir -p $FUENTES/scripts/etc
cp -r scripts/etc/php.ini $FUENTES/scripts/etc
mkdir -p $FUENTES/scripts/etc/httpd
mkdir -p $FUENTES/scripts/etc/httpd/conf
mkdir -p $FUENTES/scripts/etc/httpd/conf.d
cp -r scripts/etc/httpd/conf/* $FUENTES/scripts/etc/httpd/conf
cp -r scripts/etc/httpd/conf.d/* $FUENTES/scripts/etc/httpd/conf.d
mkdir -p $FUENTES/scripts/var
mkdir -p $FUENTES/scripts/var/www
mkdir -p $FUENTES/scripts/var/www/html
cp -r scripts/var/www/html/.htaccess $FUENTES/scripts/var/www/html

mkdir -p $FUENTES/src
cp -r src/* $FUENTES/src

mkdir -p $FUENTES/comun

mkdir -p $FUENTES/comun/includes
cp -r comun/includes/* $FUENTES/comun/includes

mkdir -p $FUENTES/comun/log
cp -r comun/log/* $FUENTES/comun/log

mkdir -p $FUENTES/comun/mantenimiento
cp -r comun/mantenimiento/* $FUENTES/comun/mantenimiento

mkdir -p $FUENTES/comun/rsc

mkdir -p $FUENTES/comun/rsc/estilos
cp -r comun/rsc/estilos/* $FUENTES/comun/rsc/estilos

mkdir -p $FUENTES/comun/rsc/idiomas
cp -r comun/rsc/idiomas/* $FUENTES/comun/rsc/idiomas

mkdir -p $FUENTES/comun/rsc/imagenes
cp -r comun/rsc/imagenes/* $FUENTES/comun/rsc/imagenes

mkdir -p $FUENTES/comun/rsc/lib
cp -r comun/rsc/lib/* $FUENTES/comun/rsc/lib

mkdir -p $FUENTES/comun/src
cp -r comun/src/* $FUENTES/comun/src

mkdir -p $FUENTES/comun/TLNT
cp -r comun/TLNT/* $FUENTES/comun/TLNT

rm -rf $FUENTES/src/api/comprueba_tipo_peticion_api.php
rm -rf $FUENTES/src/api/directorio_raiz_api.php
cp -r comun/src/api/comprueba_tipo_peticion_api.php $FUENTES/src/api/
cp -r comun/src/api/directorio_raiz_api.php $FUENTES/src/api/
rm -rf $FUENTES/comun/src/api/comprueba_tipo_peticion_api.php
rm -rf $FUENTES/comun/src/api/directorio_raiz_api.php

cp -r comun/*.php $FUENTES
cp -r comun/*.jpg $FUENTES
cp -r comun/*.txt $FUENTES
cp -r interno.php $FUENTES

mkdir -p $FUENTES/css
cp -r css/* $FUENTES/css
mkdir -p $FUENTES/js
cp -r js/* $FUENTES/js


#[cblanco] Omitir paso posterior de entrar al directorio creado y crear un zip manualmente sobre el contenido para subir posteriormente por ftp

cd fuentes_web_vX.X.X.X
zip ../fuentes_web_vX.X.X.X.zip -r * >/dev/null
rm -rf ../fuentes_web_vX.X.X.X