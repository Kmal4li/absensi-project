FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    pkg-config \
    libonig-dev \
    zip \
    && docker-php-ext-configure gd --with-jpeg --with-png \
    && docker-php-ext-install zip pdo pdo_mysql mbstring gd exif

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
