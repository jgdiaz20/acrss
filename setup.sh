#!/bin/bash

echo "========================================"
echo "Laravel School Timetable Calendar Setup"
echo "========================================"
echo

echo "[1/8] Installing PHP dependencies..."
composer install
if [ $? -ne 0 ]; then
    echo "ERROR: Composer install failed!"
    exit 1
fi

echo
echo "[2/8] Installing Node.js dependencies..."
npm install
if [ $? -ne 0 ]; then
    echo "ERROR: NPM install failed!"
    exit 1
fi

echo
echo "[3/8] Creating environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Environment file created from .env.example"
else
    echo "Environment file already exists"
fi

echo
echo "[4/8] Generating application key..."
php artisan key:generate
if [ $? -ne 0 ]; then
    echo "ERROR: Key generation failed!"
    exit 1
fi

echo
echo "[5/8] Setting up database..."
echo "Please ensure MySQL is running"
echo "Create a database named 'laravel_timetable'"
echo "Press Enter when ready..."
read

echo
echo "[6/8] Running database migrations..."
php artisan migrate
if [ $? -ne 0 ]; then
    echo "ERROR: Migration failed! Please check database connection."
    exit 1
fi

echo
echo "[7/8] Seeding database..."
php artisan db:seed
if [ $? -ne 0 ]; then
    echo "ERROR: Seeding failed!"
    exit 1
fi

echo
echo "[8/8] Compiling assets..."
npm run dev
if [ $? -ne 0 ]; then
    echo "ERROR: Asset compilation failed!"
    exit 1
fi

echo
echo "========================================"
echo "Setup completed successfully!"
echo "========================================"
echo
echo "Next steps:"
echo "1. Start your web server and MySQL"
echo "2. Open http://localhost/laravel-timetable/public"
echo "3. Login with default admin credentials"
echo
echo "Default admin credentials:"
echo "Email: admin@admin.com"
echo "Password: password"
echo
echo "For production deployment, see DEPLOYMENT_GUIDE.md"
echo
