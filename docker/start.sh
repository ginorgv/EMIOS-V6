#!/bin/bash
# Script de entrada para Railway
# Nginx escucha en puerto 80 (EXPOSE) y en $PORT (healthcheck/tráfico)
# Railway enruta tráfico público al EXPOSE y healthcheck al $PORT

set -e

# Si $PORT es diferente de 80 y 8080, añadirlo como listen adicional
if [ -n "$PORT" ] && [ "$PORT" != "80" ] && [ "$PORT" != "8080" ]; then
    sed -i "s/listen 8080;/listen 8080;\n        listen $PORT;/" /etc/nginx/nginx.conf
fi

# Crear directorio de sesiones si no existe
mkdir -p /tmp/sessions
chmod 755 /tmp/sessions

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
