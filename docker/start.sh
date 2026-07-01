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
# Y algunos includes tienen rutas incorrectas
# Crear enlaces simbólicos para compatibilidad
# ============================================
cd /var/www/html/webemios

# src/Modulos -> src/modulos (mayúsculas)
[ -d src/modulos ] && ln -sfn modulos src/Modulos 2>/dev/null || true

# Includes con rutas incorrectas (faltan subdirectorios)
# src/lib/modulos/util_widgets.php -> src/lib/modulos/widgets/util_widgets.php
mkdir -p src/lib/modulos/widgets
for f in util_widgets.php util_pestanyas_widgets.php CuadriculaWidgets.php \
         anyade_widget.php anyade_pestanya_widgets.php modifica_widget.php \
         dame_informacion_widgets.php dame_informacion_widget.php \
         muestra_ventana_anyadir_modificar_widget.php \
         muestra_ventana_anyadir_modificar_pestanya_widgets.php; do
    if [ -f "src/lib/modulos/widgets/$f" ] && [ ! -f "src/lib/modulos/$f" ]; then
        ln -sfn "widgets/$f" "src/lib/modulos/$f" 2>/dev/null || true
    fi
done

# src/lib/modulos/util_informes_automaticos.php -> src/lib/modulos/InformesFichero/util_informes_automaticos.php
if [ -f "src/lib/modulos/InformesFichero/util_informes_automaticos.php" ] && [ ! -f "src/lib/modulos/util_informes_automaticos.php" ]; then
    ln -sfn "InformesFichero/util_informes_automaticos.php" "src/lib/modulos/util_informes_automaticos.php" 2>/dev/null || true
fi

# src/modulos/ModulosWeb/ModuloSmartmeter/util_tarifas.php -> Tarifas/util_tarifas.php
if [ -f "src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php" ] && [ ! -f "src/modulos/ModulosWeb/ModuloSmartmeter/util_tarifas.php" ]; then
    ln -sfn "Tarifas/util_tarifas.php" "src/modulos/ModulosWeb/ModuloSmartmeter/util_tarifas.php" 2>/dev/null || true
fi

# src/modulos/ModulosWeb/ModuloLocalizaciones/Ratio/Ratio.php -> Ratios/Ratio.php
if [ -f "src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/Ratio.php" ]; then
    mkdir -p "src/modulos/ModulosWeb/ModuloLocalizaciones/Ratio"
    ln -sfn "../Ratios/Ratio.php" "src/modulos/ModulosWeb/ModuloLocalizaciones/Ratio/Ratio.php" 2>/dev/null || true
fi

# Crear directorio de sesiones si no existe
mkdir -p /tmp/sessions
chmod 755 /tmp/sessions

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
