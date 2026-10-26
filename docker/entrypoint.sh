#!/bin/sh
set -e

# Đổi port Nginx theo biến môi trường PORT nếu có (Render/Railway tự gán)
PORT=${PORT:-8080}
sed -i "s/listen 8080/listen ${PORT}/g" /etc/nginx/nginx.conf

# Đảm bảo quyền truy cập storage và cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Tạo storage symlink
php artisan storage:link || true

# Tối ưu hóa cache của Laravel cho production
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing Laravel for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Chạy migration nếu biến RUN_MIGRATIONS = true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Chạy seeder nếu biến RUN_SEEDER = true
if [ "$RUN_SEEDER" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force || true
fi

echo "Starting web server on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
