#!/usr/bin/env bash
set -euo pipefail

echo "=== StaffRoster Docker Entrypoint ==="

# Ensure storage directories exist (volumes may be empty on first mount)
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
chgrp -R www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Re-create storage symlink (may be broken if volume was fresh)
php artisan storage:link --force 2>/dev/null || true

# Clear any previously cached config (in case it was cached at build time)
php artisan config:clear --no-interaction || true
php artisan route:clear --no-interaction || true
php artisan event:clear --no-interaction || true

# Wait for database to be ready
echo "Waiting for PostgreSQL to be ready..."
for i in $(seq 1 30); do
    if pg_isready -h "${DB_HOST:-db}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-postgres}" 2>/dev/null; then
        echo "PostgreSQL is ready!"
        break
    fi
    echo "PostgreSQL not ready yet... ($i/30)"
    sleep 2
    if [ "$i" -eq 30 ]; then
        echo "ERROR: PostgreSQL did not become ready in time."
        exit 1
    fi
done

# Optimize for production
echo "=== Optimizing for production ==="
php artisan config:cache
php artisan route:cache
php artisan event:cache

# Run migrations
echo "=== Running migrations ==="
php artisan migrate --force --no-interaction

# Seed if the database is empty
USER_COUNT=$(php artisan tinker --execute "echo \App\Models\User::count() ?: 0;" 2>/dev/null || echo "0")
if [ "${USER_COUNT}" = "0" ]; then
    echo "=== Database is empty, running seeders ==="
    php artisan db:seed --force --no-interaction
fi

echo "=== Starting Supervisor ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
