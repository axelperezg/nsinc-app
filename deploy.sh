#!/bin/bash

# Script de deployment mejorado para Ploi.io
# Configurado para Laravel 12 con PHP 8.4

set -e  # Salir si cualquier comando falla

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Directorio del proyecto
PROJECT_DIR="/home/ploi/nsinc.hwa-company.com"

# Verificar que estamos en el directorio correcto
if [ ! -d "$PROJECT_DIR" ]; then
    error "El directorio del proyecto no existe: $PROJECT_DIR"
fi

cd "$PROJECT_DIR" || error "No se pudo cambiar al directorio del proyecto"

log "📦 Iniciando deployment..."
log "📂 Directorio: $PROJECT_DIR"

# 1. Actualizar código desde Git
log "🔄 Actualizando código desde Git..."
if ! git pull origin main; then
    error "Error al hacer git pull"
fi
log "✅ Código actualizado"

# 2. Instalar dependencias de Composer
log "📚 Instalando dependencias de Composer..."
if ! composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev; then
    error "Error al instalar dependencias de Composer"
fi
log "✅ Dependencias instaladas"

# 3. Instalar dependencias de NPM (si es necesario)
if [ -f "package.json" ]; then
    log "📦 Instalando dependencias de NPM..."
    if ! npm ci --production; then
        warning "Error al instalar dependencias de NPM, continuando..."
    else
        log "✅ Dependencias de NPM instaladas"
    fi
    
    # Compilar assets
    log "🎨 Compilando assets..."
    if ! npm run build; then
        warning "Error al compilar assets, continuando..."
    else
        log "✅ Assets compilados"
    fi
fi

# 4. Ejecutar migraciones (ANTES de optimizar para evitar errores de cache)
log "🗄️ Ejecutando migraciones..."
if ! php artisan migrate --force; then
    warning "Error al ejecutar migraciones, continuando..."
else
    log "✅ Migraciones ejecutadas"
fi

# 5. Optimizar Laravel (cache de configuración, rutas, vistas)
log "⚡ Optimizando Laravel..."
php artisan config:cache || warning "Error al cachear configuración"
php artisan route:cache || warning "Error al cachear rutas"
php artisan view:cache || warning "Error al cachear vistas"
php artisan event:cache || warning "Error al cachear eventos"
log "✅ Laravel optimizado"

# 6. Limpiar cache de aplicación (después de migraciones)
log "🧹 Limpiando cache..."
php artisan cache:clear || warning "Error al limpiar cache (puede ser normal si la tabla no existe aún)"
log "✅ Cache limpiado"

# 7. Recargar PHP-FPM
log "🔄 Recargando PHP-FPM..."
if sudo service php8.4-fpm reload 2>/dev/null; then
    log "✅ PHP-FPM recargado"
elif sudo service php8.3-fpm reload 2>/dev/null; then
    log "✅ PHP-FPM recargado (PHP 8.3)"
elif sudo service php8.2-fpm reload 2>/dev/null; then
    log "✅ PHP-FPM recargado (PHP 8.2)"
else
    warning "No se pudo recargar PHP-FPM automáticamente"
    log "💡 Intenta manualmente: sudo service php8.4-fpm reload"
fi

# 8. Verificar permisos de storage y cache
log "🔐 Verificando permisos..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || warning "Error al cambiar permisos"
chown -R ploi:ploi storage bootstrap/cache 2>/dev/null || warning "Error al cambiar propietario"

log "🚀 ¡Deployment completado exitosamente!"

