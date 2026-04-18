#!/bin/bash

# Aura Dating App - Startup Script

echo "🚀 Starting Aura Dating App..."

# Navigate to backend
cd "$(dirname "$0")/backend"

# Start Laravel server in background
php artisan serve --host=127.0.0.1 --port=8000 &
LARAVEL_PID=$!

echo "✅ Laravel server started on http://127.0.0.1:8000"
echo "📝 Admin Panel: http://127.0.0.1:8000/admin/login"
echo "📝 API Base: http://127.0.0.1:8000/api"
echo ""
echo "Admin Credentials:"
echo "  Email: admin@aura.com"
echo "  Password: admin123"
echo ""
echo "Press Ctrl+C to stop the server"

# Wait for the process
wait $LARAVEL_PID
