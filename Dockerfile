FROM php:8.2-fpm

# 1. ✅ Pasang dependencies untuk Laravel + PostgreSQL
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
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# 2. ✅ Pasang Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. ✅ Set direktori kerja
WORKDIR /var/www

# 4. ✅ Salin semua fail projek Laravel
COPY . .

# 5. ✅ Salin .env.example → .env jika belum ada
RUN cp .env.example .env || true

# 6. ✅ Pasang dependencies PHP
RUN composer install --optimize-autoloader --no-dev

# 7. ✅ Laravel Artisan: Generate key dan cache config
RUN php artisan config:clear || true
RUN php artisan config:cache || true
RUN php artisan key:generate || true

# 8. ✅ Buka port 8000 untuk Laravel
EXPOSE 8000

# 9. ✅ Migrate database dan mulakan Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
