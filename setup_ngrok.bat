@echo off
REM ngrok Setup Script for QR Code Testing (Windows)
REM This script helps you quickly set up ngrok for mobile QR code testing

echo 🚀 ngrok Setup Script for QR Code Testing
echo ========================================

REM Check if ngrok is installed
where ngrok >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ ngrok is not installed or not in PATH
    echo Please install ngrok first: https://ngrok.com/download
    pause
    exit /b 1
)

echo ✅ ngrok is installed

REM Check if Laravel server is running
curl -s http://localhost:8000 >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Laravel server is not running on port 8000
    echo Please start your Laravel server first:
    echo    php artisan serve --host=0.0.0.0 --port=8000
    pause
    exit /b 1
)

echo ✅ Laravel server is running on port 8000

REM Start ngrok tunnel
echo 🌐 Starting ngrok tunnel...
echo This will open a new command window with ngrok running
echo.

start cmd /k "ngrok http 8000"

echo 📱 ngrok tunnel started in new window
echo Please copy the HTTPS URL from the ngrok window
echo.
echo 📋 Next Steps:
echo 1. Copy the HTTPS URL from the ngrok window (e.g., https://abc123.ngrok.io)
echo 2. Update your APP_URL:
echo    set APP_URL=https://your-ngrok-url.ngrok.io
echo 3. Clear Laravel cache:
echo    php artisan config:clear
echo 4. Test QR codes on your mobile device!
echo.
echo 🔍 ngrok Web Interface: http://127.0.0.1:4040
echo 📱 Test your QR codes by scanning them with your phone!
echo.
pause
