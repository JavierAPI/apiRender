FROM php:8.2-cli

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-venv \
    unzip \
    git \
    zip \
    curl

# Crear y activar entorno virtual de Python
RUN python3 -m venv /venv
ENV PATH="/venv/bin:$PATH"

# Instalar yt-dlp dentro del entorno virtual
RUN pip install --upgrade pip && pip install yt-dlp

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install

# Exponer puerto de Laravel
EXPOSE 8000

# Comando para iniciar Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
