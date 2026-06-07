#!/bin/sh
set -e

echo "Starting Laravel application..."

wait_for_db() {
    if [ -z "$DB_CONNECTION" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
        return 0
    fi

    echo "Waiting for database connection..."
    until php -r '
        $conn = getenv("DB_CONNECTION");
        $host = getenv("DB_HOST");
        $port = getenv("DB_PORT");
        $db = getenv("DB_DATABASE");
        $user = getenv("DB_USERNAME");
        $pass = getenv("DB_PASSWORD");

        if (!$port) {
            $port = $conn === "pgsql" ? 5432 : 3306;
        }

        if ($conn === "pgsql") {
            $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        } elseif ($conn === "sqlsrv") {
            $dsn = "sqlsrv:Server=$host,$port;Database=$db";
        } else {
            $dsn = "mysql:host=$host;port=$port;dbname=$db";
        }

        try {
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
    '
    do
        echo "Waiting for DB at ${DB_HOST}:${DB_PORT:-3306}..."
        sleep 5
    done
}

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

wait_for_db

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache

echo "Application started successfully!"

# Execute the main command
exec "$@"
