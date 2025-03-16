# Użycie obrazu PHP z PHP-FPM
FROM php:8.2-fpm

# Instalacja wymaganych pakietów systemowych
RUN apt-get update && apt-get install -y \
    libonig-dev \
    librabbitmq-dev \
    libssh-dev \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Instalacja rozszerzeń PHP (w tym AMQP)
RUN docker-php-ext-install pdo pdo_mysql mbstring \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Instalacja Composera
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Skopiowanie plików aplikacji Symfony do kontenera
COPY . /var/www/html

# Instalacja zależności Composera
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ustawienie portu dla PHP-FPM
EXPOSE 9000