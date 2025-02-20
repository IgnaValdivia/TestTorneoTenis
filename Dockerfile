FROM php:8.2-fpm

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure gd \
    && docker-php-ext-install pdo pdo_mysql gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Asegurar permisos para almacenamiento
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Establecer usuario correcto
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto para PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

