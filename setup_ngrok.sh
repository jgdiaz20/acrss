#!/bin/bash

# ngrok Setup Script for QR Code Testing
# This script helps you quickly set up ngrok for mobile QR code testing

echo "🚀 ngrok Setup Script for QR Code Testing"
echo "========================================"

# Check if ngrok is installed
if ! command -v ngrok &> /dev/null; then
    echo "❌ ngrok is not installed or not in PATH"
    echo "Please install ngrok first: https://ngrok.com/download"
    exit 1
fi

echo "✅ ngrok is installed"

# Check if Laravel server is running
if ! curl -s http://localhost:8000 > /dev/null; then
    echo "❌ Laravel server is not running on port 8000"
    echo "Please start your Laravel server first:"
    echo "   php artisan serve --host=0.0.0.0 --port=8000"
    exit 1
fi

echo "✅ Laravel server is running on port 8000"

# Start ngrok tunnel
echo "🌐 Starting ngrok tunnel..."
echo "This will open a new terminal window with ngrok running"
echo ""

# For Windows
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
    start cmd /k "ngrok http 8000"
    echo "📱 ngrok tunnel started in new window"
    echo "Please copy the HTTPS URL from the ngrok window"
    echo "Then run: export APP_URL=https://your-ngrok-url.ngrok.io"
    echo "And: php artisan config:clear"
else
    # For macOS/Linux
    gnome-terminal -- bash -c "ngrok http 8000; exec bash" 2>/dev/null || \
    xterm -e "ngrok http 8000" 2>/dev/null || \
    osascript -e 'tell app "Terminal" to do script "ngrok http 8000"' 2>/dev/null || \
    echo "Please run 'ngrok http 8000' in a new terminal"
fi

echo ""
echo "📋 Next Steps:"
echo "1. Copy the HTTPS URL from the ngrok window (e.g., https://abc123.ngrok.io)"
echo "2. Update your APP_URL:"
echo "   export APP_URL=https://your-ngrok-url.ngrok.io"
echo "3. Clear Laravel cache:"
echo "   php artisan config:clear"
echo "4. Test QR codes on your mobile device!"
echo ""
echo "🔍 ngrok Web Interface: http://127.0.0.1:4040"
echo "📱 Test your QR codes by scanning them with your phone!"
