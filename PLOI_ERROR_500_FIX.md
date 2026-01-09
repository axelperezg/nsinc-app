# Guía para Solucionar Error 500 en Ploi.io

## Diagnóstico del Error 500

Un error 500 indica un problema del servidor. Sigue estos pasos para identificar y solucionar el problema.

## Paso 1: Verificar los Logs de Laravel

El primer paso es revisar los logs para identificar el error específico.

### En Ploi.io:

1. Ve a tu sitio → **"Terminal"** o **"SSH"**
2. Ejecuta:

```bash
cd /home/ploi/nsinc.hwa-company.com
tail -n 50 storage/logs/laravel.log
```

O para ver los últimos errores:

```bash
tail -f storage/logs/laravel.log
```

### También puedes verificar:

```bash
# Ver los últimos 100 errores
tail -n 100 storage/logs/laravel.log | grep ERROR

# Ver errores de hoy
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log
```

## Paso 2: Verificar Variables de Entorno

Asegúrate de que todas las variables necesarias estén configuradas en Ploi.io:

### Variables Críticas:

```env
APP_NAME=NSINC
APP_ENV=production
APP_KEY=base64:... (DEBE estar configurado)
APP_DEBUG=false
APP_URL=https://nsinc.hwa-company.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_bd
DB_USERNAME=usuario_bd
DB_PASSWORD=contraseña_bd
```

### Verificar en Terminal:

```bash
php artisan tinker
```

Si hay errores al iniciar Tinker, hay un problema de configuración.

## Paso 3: Verificar Permisos

Los permisos incorrectos son una causa común de errores 500:

```bash
cd /home/ploi/nsinc.hwa-company.com

# Verificar permisos actuales
ls -la storage/
ls -la bootstrap/cache/

# Corregir permisos
chmod -R 775 storage bootstrap/cache
chown -R ploi:ploi storage bootstrap/cache
```

## Paso 4: Verificar APP_KEY

Si `APP_KEY` no está configurado, Laravel no funcionará:

```bash
# Verificar si existe
php artisan tinker
>>> config('app.key')

# Si está vacío, generar uno nuevo
php artisan key:generate

# Luego actualizar en Ploi.io → Environment Variables
```

## Paso 5: Limpiar Cache

El cache corrupto puede causar errores 500:

```bash
cd /home/ploi/nsinc.hwa-company.com

# Limpiar todos los caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Reconstruir cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Paso 6: Verificar Base de Datos

Problemas de conexión a la base de datos causan errores 500:

```bash
# Probar conexión
php artisan tinker
>>> DB::connection()->getPdo();

# Si hay error, verifica las variables DB_* en Ploi.io
```

## Paso 7: Verificar Archivos Faltantes

Asegúrate de que los archivos necesarios existan:

```bash
# Verificar archivo .env (o variables en Ploi.io)
ls -la .env

# Verificar vendor (dependencias)
ls -la vendor/

# Si vendor no existe, instalar:
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
```

## Paso 8: Verificar Errores de PHP

Revisa los logs de PHP/Nginx:

### En Ploi.io:

1. Ve a **"Logs"** → **"Error Log"** o **"Nginx Error Log"**
2. Busca errores recientes

### O en Terminal:

```bash
# Ver logs de PHP-FPM
sudo tail -f /var/log/php8.4-fpm.log
# O según tu versión de PHP:
sudo tail -f /var/log/php8.3-fpm.log
sudo tail -f /var/log/php8.2-fpm.log

# Ver logs de Nginx
sudo tail -f /var/log/nginx/error.log
```

## Paso 9: Habilitar Debug Temporalmente (Solo para Diagnóstico)

⚠️ **IMPORTANTE:** Solo para diagnosticar, NO dejar en producción.

En Ploi.io → Environment Variables:

```env
APP_DEBUG=true
APP_ENV=local
```

Esto mostrará el error específico en lugar del error 500 genérico.

**Recuerda volver a cambiarlo después:**

```env
APP_DEBUG=false
APP_ENV=production
```

## Paso 10: Verificar Errores Comunes Específicos

### Error: "No application encryption key has been specified"

```bash
php artisan key:generate
# Copiar el valor a Ploi.io → Environment Variables → APP_KEY
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"

- Verifica `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` en Ploi.io
- Verifica que la base de datos esté corriendo

### Error: "Class 'X' not found"

```bash
composer dump-autoload
php artisan optimize:clear
php artisan optimize
```

### Error: "Permission denied" en storage

```bash
chmod -R 775 storage bootstrap/cache
chown -R ploi:ploi storage bootstrap/cache
```

### Error: "The stream or file could not be opened"

```bash
# Crear directorios si no existen
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Aplicar permisos
chmod -R 775 storage
```

## Paso 11: Script de Verificación Completa

Ejecuta este script para verificar todo:

```bash
cd /home/ploi/nsinc.hwa-company.com

echo "🔍 Verificando configuración..."

# Verificar APP_KEY
echo "APP_KEY:"
php artisan tinker --execute="echo config('app.key') ? '✅ Configurado' : '❌ Faltante';"

# Verificar Base de Datos
echo "Base de Datos:"
php artisan tinker --execute="try { DB::connection()->getPdo(); echo '✅ Conectada'; } catch(Exception \$e) { echo '❌ Error: ' . \$e->getMessage(); }"

# Verificar permisos
echo "Permisos storage:"
ls -ld storage | grep -q "drwxrwxr-x" && echo "✅ Correctos" || echo "❌ Incorrectos"

# Verificar vendor
echo "Dependencias:"
[ -d "vendor" ] && echo "✅ Instaladas" || echo "❌ Faltantes"

# Verificar logs
echo "Último error en logs:"
tail -n 1 storage/logs/laravel.log 2>/dev/null || echo "No hay logs aún"
```

## Solución Rápida: Reset Completo

Si nada funciona, intenta un reset completo:

```bash
cd /home/ploi/nsinc.hwa-company.com

# 1. Limpiar todo
php artisan optimize:clear
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*

# 2. Reinstalar dependencias
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Regenerar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Verificar permisos
chmod -R 775 storage bootstrap/cache
chown -R ploi:ploi storage bootstrap/cache

# 5. Recargar PHP-FPM
sudo service php8.4-fpm reload
```

## Verificar que Funciona

Después de aplicar las soluciones:

```bash
# Probar que la aplicación responde
curl -I https://nsinc.hwa-company.com

# Debería mostrar: HTTP/1.1 200 OK
```

## Contacto y Soporte

Si después de seguir todos estos pasos el problema persiste:

1. **Revisa los logs completos** en `storage/logs/laravel.log`
2. **Copia el error específico** de los logs
3. **Contacta al soporte de Ploi.io** con el error específico
4. **Verifica la documentación de Laravel** sobre el error específico

## Notas Importantes

- ⚠️ **Nunca dejes `APP_DEBUG=true` en producción** por seguridad
- 🔒 Siempre verifica que `APP_KEY` esté configurado
- 📝 Los logs están en `storage/logs/laravel.log`
- 🔄 Después de cambios en `.env`, ejecuta `php artisan config:clear`
- 🛡️ Los permisos correctos son críticos para Laravel




