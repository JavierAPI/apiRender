FROM php:8.2-cli

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-pip \
    unzip \
    git \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Instala yt-dlp
RUN pip3 install yt-dlp

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece directorio de trabajo
WORKDIR /var/www

# Copia archivos del proyecto
COPY . .

# Instala dependencias de Laravel
RUN composer install

# Expone puerto para Laravel (si usas `php artisan serve`)
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
