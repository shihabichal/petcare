#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Running composer install..."
composer install --optimize-autoloader --no-dev

echo "Clearing caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "Build finished!"
