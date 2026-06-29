#!/bin/bash

HOME="/opt/energyminus"
SOURCES_PATH="/var/www/html/webemios"

echo [INFO] $(date): Finalizando el servicio httpd ...
service httpd stop
echo [INFO] $(date): Servicio httpd finalizado

echo [INFO] $(date): Desactivando modo mantenimiento ...
rm $SOURCES_PATH/index.php
cp $SOURCES_PATH/index.php_ $SOURCES_PATH/index.php
rm $SOURCES_PATH/login.php
cp $SOURCES_PATH/login.php_ $SOURCES_PATH/login.php
mv $SOURCES_PATH/interno.php_ $SOURCES_PATH/interno.php
mv $SOURCES_PATH/src/api_ $SOURCES_PATH/src/api
mv $SOURCES_PATH/Wibeee_ $SOURCES_PATH/Wibeee
chown -R apache:desarrollo $SOURCES_PATH
chmod -R g+w $SOURCES_PATH
echo [INFO] $(date): Desactivado modo mantenimiento

echo [INFO] $(date): Iniciando el servicio httpd ...
service httpd start
echo [INFO] $(date): Servicio httpd iniciado