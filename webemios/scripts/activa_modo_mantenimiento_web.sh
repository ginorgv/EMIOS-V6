#!/bin/bash

HOME="/opt/energyminus"
SOURCES_PATH="/var/www/html/webemios"

echo [INFO] $(date): Finalizando el servicio httpd ...
service httpd stop
echo [INFO] $(date): Servicio httpd finalizado

echo [INFO] $(date): Iniciando modo mantenimiento ...
mv $SOURCES_PATH/index.php $SOURCES_PATH/index.php_
mv $SOURCES_PATH/login.php $SOURCES_PATH/login.php_
mv $SOURCES_PATH/interno.php $SOURCES_PATH/interno.php_
mv $SOURCES_PATH/src/api $SOURCES_PATH/src/api_
mv $SOURCES_PATH/Wibeee $SOURCES_PATH/Wibeee_
cp $SOURCES_PATH/comun/mantenimiento/index.php $SOURCES_PATH/index.php
cp $SOURCES_PATH/comun/mantenimiento/login.php $SOURCES_PATH/login.php
chown -R apache:desarrollo $SOURCES_PATH
chmod -R g+w $SOURCES_PATH
echo [INFO] $(date): Modo mantenimiento iniciado

echo [INFO] $(date): Iniciando el servicio httpd ...
service httpd start
echo [INFO] $(date): Servicio httpd iniciado