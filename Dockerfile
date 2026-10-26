# Stage 1: Build Frontend Assets with Node
FROM node:20-alpine AS node_builder
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Stage 2: PHP Application Environment
FROM php:8.4-fpm-alpine

# Cài đặt các gói hệ thống cần thiết
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    icu-dev

# Cài đặt các PHP extensions hỗ trợ Laravel & MySQL
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Tải Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Sao chép mã nguồn ứng dụng
COPY . .

# Sao chép thư mục public/build từ stage node_builder
COPY --from=node_builder /app/public/build ./public/build

# Cài đặt các dependencies PHP cho production
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Sao chép các cấu hình Docker
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Xóa ký tự xuống dòng Windows (CRLF -> LF) và phân quyền
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cổng mặc định
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
