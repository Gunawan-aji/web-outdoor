FROM php:8.2-apache

# Install driver database MySQL & ekstensi penting lainnya
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli gd \
    && a2enmod rewrite

# Tentukan lokasi file di server
WORKDIR /var/www/html

# Copy semua file dari repository GitHub ke dalam server
COPY . .

# Berikan izin akses folder
RUN chown -R www-data:www-data /var/www/html

RUN docker-php-ext-install pdo pdo_mysql mysqli