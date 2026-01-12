#!/bin/bash

# Stop on error
set -e

echo "Fixing Laravel permissions and cache..."

# Ensure storage directories exist
echo "Creating storage directories..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p bootstrap/cache

# Fix permissions
# Attempting to set standard writable permissions (775)
# If this fails, you may need to use 'sudo' or check your user/share setup.
echo "Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# If the web server user is different, you might need:
# chown -R $USER:www-data storage bootstrap/cache

# Clear all caches
echo "Clearing artisan caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo "✅ Permissions fixed and caches cleared!"
