# Użycie obrazu PHP z PHP-FPM
FROM php:8.2-fpm

# Instalacja wymaganych rozszerzeń PHP
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip mbstring

# Instalacja Composera
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Skopiowanie plików aplikacji Symfony do kontenera
COPY . /var/www/html

# Instalacja zależności za pomocą Composera
RUN composer install

# Ustawienie uprawnień dla Symfony (cache i logi)
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public

# Otworzenie portu dla PHP-FPM
EXPOSE 9000
