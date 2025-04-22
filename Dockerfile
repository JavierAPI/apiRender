FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip git curl ffmpeg python3 python3-pip \
    && apt-get clean

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala yt-dlp
RUN pip install yt-dlp

# Copia el código
WORKDIR /var/www
COPY . .

# Instala dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
