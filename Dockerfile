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
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configurar Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configurar Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de inicio
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Copiar la aplicación
COPY . /var/www/html

# Establecer el directorio web como raíz
WORKDIR /var/www/html/webemios

# Crear directorios necesarios
RUN mkdir -p /tmp/sessions /var/www/html/webemios/tmp \
    && chmod -R 755 /tmp/sessions \
    && chown -R www-data:www-data /tmp/sessions \
    && chown -R www-data:www-data /var/www/html/webemios/tmp

# Exponer puerto
EXPOSE 80

# Script de entrada
CMD ["/usr/local/bin/start.sh"]
