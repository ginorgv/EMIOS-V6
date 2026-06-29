#!/bin/bash

HOME="/opt/energyminus"
SOURCES_PATH="/var/www/html/webemios"

echo [INFO] $(date): Copiando ficheros de configuración de php 'etc/php.ini' ...
mv /etc/php.ini /etc/php.ini.old
mv $SOURCES_PATH/scripts/etc/php.ini /etc/php.ini
echo [INFO] $(date): Copiado de fichero de configuración de php 'etc/php.ini' finalizado

echo [INFO] $(date): Copiando ficheros de configuración de httpd 'etc/httpd/conf' ...
mv /etc/httpd/conf/httpd.conf /etc/httpd/conf/httpd.conf.old
mv $SOURCES_PATH/scripts/etc/httpd/conf/httpd.conf /etc/httpd/conf
echo [INFO] $(date): Copiado de fichero de configuración de httpd 'etc/httpd/conf' finalizado

echo [INFO] $(date): Copiando ficheros de configuración de httpd 'etc/httpd/conf.d' ...
mv /etc/httpd/conf.d/custom_errors.conf /etc/httpd/conf.d/custom_errors.conf.old
mv /etc/httpd/conf.d/mod_deflate.conf /etc/httpd/conf.d/mod_deflate.conf.old
mv $SOURCES_PATH/scripts/etc/httpd/conf.d/custom_errors.conf /etc/httpd/conf.d
mv $SOURCES_PATH/scripts/etc/httpd/conf.d/mod_deflate.conf /etc/httpd/conf.d
echo [INFO] $(date): Copiado de ficheros de configuración de httpd 'etc/httpd/conf.d' finalizado

echo [INFO] $(date): Copiando ficheros '.htaccess' ...
mv /var/www/html/.htaccess /var/www/html/.htaccess.old
mv $SOURCES_PATH/scripts/var/www/html/.htaccess /var/www/html
echo [INFO] $(date): Copiado de fichero '.htaccess' finalizado

echo [INFO] $(date): Establecendo usuario y grupos de los ficheros de la nueva versión de código fuente ...
chown -R apache:desarrollo $SOURCES_PATH
chmod -R g+w $SOURCES_PATH
echo [INFO] $(date): Establecimiento de usuario y grupo de los ficheros de la nueva versión de código fuente finalizada

echo [INFO] $(date): Activando el modo de mantenimiento ...
$SOURCES_PATH/scripts/activa_modo_mantenimiento_web.sh
echo [INFO] $(date): Modo de mantenimiento activado
