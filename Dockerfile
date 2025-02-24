# Gunakan image PHP dengan ekstensi yang dibutuhkan
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy semua file ke dalam container
COPY . .

# Install dependencies Laravel
RUN composer install

# Beri permission ke storage dan bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
