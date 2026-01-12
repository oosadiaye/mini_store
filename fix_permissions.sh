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
# We use 777 here because on some setups the web server user is different
# from the command line user and not in the same group.
echo "Setting permissions..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# If the web server user is different, you might need:
# chown -R $USER:www-data storage bootstrap/cache

# Clear all caches
echo "Clearing artisan caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo "✅ Permissions fixed and caches cleared!"
