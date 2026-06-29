FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    unzip \
    git \
    gettext-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        curl \
        bcmath \
        intl \
        exif \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configurar Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configurar Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar la aplicación
COPY . /var/www/html

# Establecer el directorio web como raíz
WORKDIR /var/www/html/webemios

# Crear directorios necesarios
RUN mkdir -p /tmp/sessions /var/www/html/webemios/tmp \
    && chmod -R 755 /tmp/sessions \
    && chown -R www-data:www-data /tmp/sessions \
    && chown -R www-data:www-data /var/www/html/webemios/tmp

# Exponer puerto (Railway usa $PORT, por defecto 80)
EXPOSE 80

# Script de entrada: sustituir ${PORT} en nginx.conf y arrancar servicios
CMD envsubst '${PORT}' < /etc/nginx/nginx.conf > /etc/nginx/nginx.conf.tmp \
    && mv /etc/nginx/nginx.conf.tmp /etc/nginx/nginx.conf \
    && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
