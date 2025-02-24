FROM php:8.2-cli

WORKDIR /app

COPY . .

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    zip \
    && docker-php-ext-configure gd --with-jpeg --with-png \
    && docker-php-ext-install zip pdo pdo_mysql mbstring gd exif sodium \
    && docker-php-ext-enable pdo_mysql sodium zip gd exif

# Install dependensi Laravel
RUN composer install --no-dev --optimize-autoloader

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
