#!/bin/bash

echo [INFO] $(date): Inicio actualización fuentes Web

HOME="/opt/energyminus"
SOURCES_PATH="/var/www/html/webemios"
TMP_SOURCES_PATH=$HOME/tmp/webemios
CURRENT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
UPDATE_FILENAME=$1

if [ ! -f $HOME/tmp/$UPDATE_FILENAME ]
then
	echo [ERROR] $(date): Fichero de fuentes no encontrado: if [ ! -f $HOME/tmp/$UPDATE_FILENAME ]
	exit 1
fi

echo [INFO] $(date): Finalizando servicio httpd ...
service httpd stop
echo [INFO] $(date): Servicio httpd finalizado

echo [INFO] $(date): Descomprimiendo nueva versión de código fuente: $HOME/tmp/$UPDATE_FILENAME ...
unzip_correcto=true
if unzip -t $HOME/tmp/$UPDATE_FILENAME
then
	echo [INFO] $(date): Test de unzip correcto
	unzip_correcto=true
else
	echo [ERROR] $(date): Test de unzip incorrecto
	unzip_correcto=false
fi

if [[ $unzip_correcto == true ]]
then
	rm -rf $TMP_SOURCES_PATH
	unzip -uo $HOME/tmp/$UPDATE_FILENAME -d $TMP_SOURCES_PATH
	tamanyo_fichero_mas_grande=$(find $TMP_SOURCES_PATH -type f -exec ls -al {} \; | sort -nr -k5 | head -n 1 | awk '{ print $5 }')
	if [[ $tamanyo_fichero_mas_grande = 0 ]]
	then
		echo [ERROR] $(date): Los ficheros descomprimidos están vacíos
		unzip_correcto=false
    else
        echo [INFO] $(date): Los ficheros descomprimidos no están vacíos
	fi
fi

if [[ $unzip_correcto == true ]]
then
	echo [INFO] $(date): Descompresión correcta
else
	echo [ERROR] $(date): Descompresión incorrecta, se detiene la actualización de fuentes
	echo [INFO] $(date): Iniciando el servicio httpd ...
    service httpd start
    echo [INFO] $(date): Servicio httpd iniciado
	exit 1
fi
echo [INFO] $(date): Descompresión de nueva versión de código fuente finalizada

echo [INFO] $(date): Guardando repositorio de fuentes y logos ...
if [ -d $HOME/tmp/.repositorio_web ]
then
	rm -rf $HOME/tmp/.repositorio_web
fi
if [ -d $HOME/tmp/.logos_web ]
then
	rm -rf $HOME/tmp/.logos_web
fi
mkdir $HOME/tmp/.repositorio_web
mkdir $HOME/tmp/.logos_web
mv $SOURCES_PATH/repositorio/* $HOME/tmp/.repositorio_web
if [ -d $SOURCES_PATH/comun/rsc/imagenes/logos ]
then
    mv $SOURCES_PATH/comun/rsc/imagenes/logos/* $HOME/tmp/.logos_web
fi
mv $SOURCES_PATH/rsc/imagenes/logos/* $HOME/tmp/.logos_web
rm -R $SOURCES_PATH/repositorio
rm -R $SOURCES_PATH/rsc/imagenes/logos/
echo [INFO] $(date): Repositorio de fuentes y logos guardados

echo [INFO] $(date): Eliminando versión actual de código fuente ...
rm -R $SOURCES_PATH/*
echo [INFO] $(date): Versión actual de código fuente eliminada

echo [INFO] $(date): Moviendo código fuente descomprimido ...
mv $TMP_SOURCES_PATH/* $SOURCES_PATH
rm -rf $TMP_SOURCES_PATH
echo [INFO] $(date): Código fuente descomprimido movido

echo [INFO] $(date): Restaurando repositorio de fuentes y logos ...
mv $HOME/tmp/.repositorio_web/* $SOURCES_PATH/repositorio
mv $HOME/tmp/.logos_web/* $SOURCES_PATH/rsc/imagenes/logos
echo [INFO] $(date): Repositorio de fuentes y logos restaurados

echo [INFO] $(date): Estableciendo permisos de ejecución ...
chmod u+x $SOURCES_PATH/scripts/*
echo [INFO] $(date): Establecimiento de permisos de ejecución finalizada

cd $CURRENT_DIR

echo [INFO] $(date): Finalizando actualización de fuentes ...
$SOURCES_PATH/scripts/finaliza_actualizacion_fuentes_web.sh
echo [INFO] $(date): Actualización de fuentes finalizada

echo [INFO] $(date): Fin actualización fuentes Web