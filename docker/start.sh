#!/bin/bash
# Script de entrada para Railway
# Configura Nginx para escuchar en el puerto asignado por Railway ($PORT)
# y arranca Supervisor para gestionar Nginx + PHP-FPM

set -e

# Configurar puerto de Nginx (Railway asigna $PORT, por defecto 8080)
NGINX_PORT="${PORT:-80}"

# Reemplazar el puerto en la configuración de Nginx
sed -i "s/listen .*/listen ${NGINX_PORT};/" /etc/nginx/nginx.conf

# Crear directorio de sesiones si no existe
mkdir -p /tmp/sessions
chmod 755 /tmp/sessions

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
