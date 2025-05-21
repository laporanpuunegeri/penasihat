FROM php:8.2-fpm

# 1. ✅ Pasang semua keperluan sistem & PostgreSQL extension
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

# 3. ✅ Tetapkan direktori kerja
WORKDIR /var/www

# 4. ✅ Salin semua fail Laravel
COPY . .

# 5. ✅ Salin .env.example → .env jika belum ada
RUN [ -f .env ] || cp .env.example .env

# 6. ✅ Pasang semua dependency PHP (tanpa dev)
RUN composer install --optimize-autoloader --no-dev --no-scripts

# 7. ✅ Artisan commands (elakkan error jika env belum lengkap)
RUN php artisan config:clear || true
RUN php artisan config:cache || true
RUN php artisan key:generate || true

# 8. ✅ Buka port untuk Laravel (default: 8000)
EXPOSE 8000

# 9. ✅ Jalankan migrasi dan servis Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
