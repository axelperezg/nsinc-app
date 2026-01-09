# Solución al Error de Cache en Deployment

## Problema

```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'nsinc_app.cache' doesn't exist
```

Este error ocurre porque el script intenta limpiar el cache (`php artisan cache:clear`) antes de ejecutar las migraciones que crean la tabla `cache`.

## Solución

He actualizado los scripts de deployment para:

1. **Ejecutar migraciones PRIMERO** - Esto crea la tabla `cache` antes de intentar usarla
2. **Manejar errores de cache** - Si la tabla aún no existe, el script continúa sin fallar

## Scripts Actualizados

### `deploy-ploi-simple.sh` (Para copiar en Ploi.io)

El script ahora ejecuta las migraciones antes de limpiar el cache:

```bash
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Error en migraciones, continuando..."

echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar cache de forma segura
php artisan cache:clear || echo "⚠️ Cache no disponible aún, continuando..."
```

## Solución Manual (Si el error persiste)

Si aún tienes problemas, puedes ejecutar manualmente en el terminal de Ploi.io:

```bash
cd /home/ploi/nsinc.hwa-company.com

# 1. Ejecutar migraciones primero
php artisan migrate --force

# 2. Luego limpiar cache
php artisan cache:clear

# 3. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Alternativa: Cambiar Cache Driver Temporalmente

Si prefieres usar cache de archivos en lugar de base de datos, agrega esta variable en Ploi.io:

```env
CACHE_STORE=file
```

Esto evitará el problema de la tabla `cache` no existente, pero usarás cache de archivos en lugar de base de datos.

## Verificar que Funciona

Después del deployment, verifica que la tabla existe:

```bash
php artisan tinker
```

Y luego:
```php
DB::table('cache')->count();
```

Si no da error, la tabla existe correctamente.




