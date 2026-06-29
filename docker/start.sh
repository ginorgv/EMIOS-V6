#!/bin/bash
# Script de entrada para Railway
# Nginx escucha en puerto 80 (EXPOSE 80 en Dockerfile)
# Railway enruta el tráfico automáticamente al puerto EXPOSE

set -e

# Crear directorio de sesiones si no existe
mkdir -p /tmp/sessions
chmod 755 /tmp/sessions

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
