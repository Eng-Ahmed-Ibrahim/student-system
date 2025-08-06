#!/bin/bash

echo "🚀 Running deploy script"

if [ -d .git ]; then
  echo "[1/7] 📥 Resetting and pulling latest code"
  git reset --hard
  git clean -fd
  git pull origin main --ff-only
else
  echo "⚠️ Not a git repository, skipping git pull"
fi

echo "[2/7] 🗃️ Creating database if one isn't found"
touch database/database.sqlite

echo "[3/7] 📦 Installing packages using composer"
composer install --no-interaction --prefer-dist --optimize-autoloader
composer dump-autoload

echo "[4/7] ⚙️ Publishing API Platform assets"
if php artisan list | grep -q "api-platform:"; then
  php artisan api-platform:install
else
  echo "ℹ️ Skipping api-platform install (not available)"
fi

echo "[5/7] 🧹 Clearing and caching config/routes/views"
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[6/7] 🛠️ Migrating database"
php artisan migrate --force

echo "[7/7] 🌱 Seeding database"
php artisan db:seed

echo "✅ The app has been deployed successfully!"
