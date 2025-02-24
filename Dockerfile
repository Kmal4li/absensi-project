FROM php:8.0
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY . /app
RUN npm run

CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181