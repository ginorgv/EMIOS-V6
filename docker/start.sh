#!/bin/bash
# Script de entrada para Railway

set -e

# Forzar que PHP-FPM escuche en 127.0.0.1:9001 (modificar docker.conf de la imagen base)
sed -i "s/listen = 9000/listen = 127.0.0.1:9001/" /usr/local/etc/php-fpm.d/docker.conf 2>/dev/null || true

# Añadir $PORT como puerto adicional en Nginx si no está ya configurado
if [ -n "$PORT" ] && ! grep -q "listen 0.0.0.0:$PORT;" /etc/nginx/nginx.conf; then
    sed -i "s/listen 0.0.0.0:9000;/listen 0.0.0.0:9000;\n    listen 0.0.0.0:$PORT;/" /etc/nginx/nginx.conf
fi

# ============================================
# Linux es sensible a mayúsculas (Windows no)
# Crear enlaces simbólicos para variaciones
# comunes de mayúsculas/minúsculas en includes
# ============================================
cd /var/www/html/webemios

# src/Modulos -> src/modulos (el más común)
[ -d src/modulos ] && ln -sfn modulos src/Modulos 2>/dev/null || true

# Otras variaciones que puedan existir
for dir in \
    "src/Modulos" \
    "src/Lib" \
    "src/BasesDatos"; do
    lower_dir=$(echo "$dir" | tr 'A-Z' 'a-z')
    if [ -d "$lower_dir" ] && [ ! -e "$dir" ]; then
        parent=$(dirname "$dir")
        base=$(basename "$dir" | tr 'A-Z' 'a-z')
        ln -sfn "$base" "$parent/$(basename "$dir")" 2>/dev/null || true
    fi
done

# Crear directorio de sesiones si no existe
mkdir -p /tmp/sessions
chmod 755 /tmp/sessions

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
