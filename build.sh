#!/bin/bash

echo "🚀 Running deploy script"

# تأكد إنك داخل Git repo
if [ -d .git ]; then
  echo "[1/8] 📥 Pulling latest code from GitHub (بدون مسح ملفات)"
  git pull origin main --ff-only
else
  echo "⚠️ Not a git repository, skipping git pull"
fi

echo "[2/8] 🗃️ Creating database if one isn't found"
touch database/database.sqlite

echo "[3/8] 📦 Installing packages using composer"
php composer.phar install --no-interaction --prefer-dist --optimize-autoloader
composer dump-autoload

echo "[4/8] ⚙️ Publishing API Platform assets"
if php artisan list | grep -q "api-platform:"; then
  php artisan api-platform:install
else
  echo "ℹ️ Skipping api-platform install (not available)"
fi

echo "[5/8] 🧹 Clearing and caching config/routes/views"
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[6/8] 🛠️ Migrating database"
php artisan migrate --force

echo "[7/8] 🌱 Seeding database"
php artisan db:seed



echo "✅ The app has been deployed successfully!"
