@echo off
echo ========================================
echo Laravel School Timetable Calendar Setup
echo ========================================
echo.

echo [1/8] Installing PHP dependencies...
composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)

echo.
echo [2/8] Installing Node.js dependencies...
npm install
if %errorlevel% neq 0 (
    echo ERROR: NPM install failed!
    pause
    exit /b 1
)

echo.
echo [3/8] Creating environment file...
if not exist .env (
    copy .env.example .env
    echo Environment file created from .env.example
) else (
    echo Environment file already exists
)

echo.
echo [4/8] Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Key generation failed!
    pause
    exit /b 1
)

echo.
echo [5/8] Setting up database...
echo Please ensure MySQL is running in XAMPP
echo Create a database named 'laravel_timetable' in phpMyAdmin
echo Press any key when ready...
pause

echo.
echo [6/8] Running database migrations...
php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Migration failed! Please check database connection.
    pause
    exit /b 1
)

echo.
echo [7/8] Seeding database...
php artisan db:seed
if %errorlevel% neq 0 (
    echo ERROR: Seeding failed!
    pause
    exit /b 1
)

echo.
echo [8/8] Compiling assets...
npm run dev
if %errorlevel% neq 0 (
    echo ERROR: Asset compilation failed!
    pause
    exit /b 1
)

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Start XAMPP (Apache + MySQL)
echo 2. Open http://localhost/laravel-timetable/public
echo 3. Login with default admin credentials
echo.
echo Default admin credentials:
echo Email: admin@admin.com
echo Password: password
echo.
echo For production deployment, see DEPLOYMENT_GUIDE.md
echo.
pause
