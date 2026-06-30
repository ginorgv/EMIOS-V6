#!/bin/bash
# Script de entrada para Railway

set -e

# Si $PORT no está ya en la configuración, añadirlo como listen adicional
if [ -n "$PORT" ] && ! grep -q "listen $PORT;" /etc/nginx/nginx.conf; then
    sed -i "s/listen 9000;/listen 9000;\n        listen $PORT;/" /etc/nginx/nginx.conf
fi

# Crear directorio de sesiones si no existe
mkdir -p /tmp/sessions
chmod 755 /tmp/sessions

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
