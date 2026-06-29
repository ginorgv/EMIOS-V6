#!/bin/bash

if [ $# -eq 0 ]
then
	echo [ERROR] $(date): Número de parámetros incorrecto
	echo [ERROR] $(date): Número de parámetros incorrecto >> /var/log/energyminus/actualiza_fuentes_web.log
	exit 1
fi

/var/www/html/webemios/scripts/actualiza_fuentes_web.sh $1 2>> /var/log/energyminus/actualiza_fuentes_web.log >> /var/log/energyminus/actualiza_fuentes_web.log

