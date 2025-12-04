#!/bin/bash

# Versión simplificada para copiar en Ploi.io
# Script de deployment optimizado

set -e  # Salir si cualquier comando falla

cd /home/ploi/nsinc.hwa-company.com

echo "🔄 Actualizando código..."
git pull origin main

echo "📚 Instalando dependencias..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "🎨 Compilando assets..."
npm ci --production && npm run build || echo "⚠️ Error en assets, continuando..."

echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Error en migraciones, continuando..."

echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar cache de forma segura (ignorar error si la tabla no existe)
php artisan cache:clear || echo "⚠️ Cache no disponible aún, continuando..."

echo "🔄 Recargando PHP-FPM..."
sudo service php8.4-fpm reload || sudo service php8.3-fpm reload || sudo service php8.2-fpm reload

echo "🚀 ¡Deployment completado!"

