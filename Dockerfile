FROM php:8.2-fpm

# Install system dependencies & PostgreSQL driver
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Laravel Artisan Commands
RUN php artisan config:clear
RUN php artisan key:generate || true

# Expose port and start Laravel with automatic migration
EXPOSE 8000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
