# Guía: Configurar APP_KEY en Ploi.io

## ¿Qué es APP_KEY?

`APP_KEY` es una clave de encriptación que Laravel usa para:
- Encriptar cookies y sesiones
- Generar tokens seguros
- Encriptar datos sensibles

**Sin APP_KEY, Laravel NO funcionará** y mostrará error 500.

## Método 1: Generar APP_KEY desde el Terminal de Ploi.io (Recomendado)

### Paso 1: Acceder al Terminal

1. Inicia sesión en **Ploi.io**
2. Ve a tu servidor
3. Selecciona tu sitio (`nsinc.hwa-company.com`)
4. Haz clic en **"Terminal"** o **"SSH"**

### Paso 2: Generar la Clave

Ejecuta estos comandos:

```bash
cd /home/ploi/nsinc.hwa-company.com

# Generar la clave (esto mostrará el valor)
php artisan key:generate --show
```

**Ejemplo de salida:**
```
base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Paso 3: Copiar el Valor

Copia **todo el valor** que aparece, incluyendo el prefijo `base64:`

**Ejemplo:**
```
base64:AbCdEfGhIjKlMnOpQrStUvWxYz1234567890AbCdEfGhIjKlMnOpQrStUvWxYz1234567890=
```

### Paso 4: Configurar en Ploi.io

1. En Ploi.io, ve a tu sitio
2. Haz clic en **"Environment Variables"** o **"Variables de Entorno"**
3. Busca la variable `APP_KEY` o créala si no existe:
   - **Key:** `APP_KEY`
   - **Value:** Pega el valor completo que copiaste (ej: `base64:AbCdEfGhIjKlMnOpQrStUvWxYz...`)
4. Haz clic en **"Save"** o **"Guardar"**

### Paso 5: Limpiar y Reconstruir Cache

Después de configurar, ejecuta en el terminal:

```bash
cd /home/ploi/nsinc.hwa-company.com

# Limpiar cache de configuración
php artisan config:clear

# Reconstruir cache
php artisan config:cache
```

### Paso 6: Verificar que Funciona

```bash
# Verificar que APP_KEY está configurado
php artisan tinker
>>> config('app.key')
```

Debería mostrar el valor que configuraste. Si está vacío o es `null`, hay un problema.

## Método 2: Usar el .env Local (Alternativa)

Si prefieres generar la clave localmente y luego copiarla:

### Paso 1: Generar en tu Máquina Local

En tu proyecto local (donde tienes el código):

```bash
cd C:\xampp\htdocs\nsinc-app

# Generar la clave
php artisan key:generate --show
```

### Paso 2: Copiar el Valor

Copia el valor completo que aparece (ej: `base64:...`)

### Paso 3: Configurar en Ploi.io

1. Ve a Ploi.io → Tu Sitio → **Environment Variables**
2. Agrega o edita:
   - **Key:** `APP_KEY`
   - **Value:** El valor que copiaste
3. Guarda

### Paso 4: Limpiar Cache en el Servidor

En el terminal de Ploi.io:

```bash
cd /home/ploi/nsinc.hwa-company.com
php artisan config:clear
php artisan config:cache
```

## Método 3: Generar Manualmente (Avanzado)

Si por alguna razón `php artisan key:generate` no funciona:

```bash
cd /home/ploi/nsinc.hwa-company.com

# Generar clave manualmente
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

Copia el resultado y configúralo en Ploi.io como `APP_KEY`.

## Verificar que Está Configurado Correctamente

### Verificación 1: Desde Terminal

```bash
cd /home/ploi/nsinc.hwa-company.com

php artisan tinker
```

Luego en Tinker:
```php
config('app.key')
```

**Debería mostrar:** `base64:...` (un string largo)

**Si muestra:** `null` o está vacío → **NO está configurado**

### Verificación 2: Probar la Aplicación

1. Abre tu navegador
2. Ve a `https://nsinc.hwa-company.com`
3. Si el error 500 desaparece y ves la aplicación → **✅ Funciona**
4. Si sigue el error 500 → Revisa los logs

## Solución de Problemas

### Problema: "No application encryption key has been specified"

**Solución:**
```bash
cd /home/ploi/nsinc.hwa-company.com
php artisan key:generate --show
# Copiar el valor y agregarlo en Ploi.io → Environment Variables → APP_KEY
php artisan config:clear
php artisan config:cache
```

### Problema: El valor no se guarda en Ploi.io

**Solución:**
1. Verifica que no haya espacios antes o después del valor
2. Asegúrate de copiar el valor completo incluyendo `base64:`
3. Guarda y espera unos segundos
4. Recarga la página de variables de entorno para verificar

### Problema: Después de configurar sigue el error 500

**Solución:**
```bash
cd /home/ploi/nsinc.hwa-company.com

# Limpiar todo el cache
php artisan optimize:clear

# Reconstruir cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar que se aplicó
php artisan tinker
>>> config('app.key')
```

### Problema: El comando `key:generate` no funciona

**Solución:**
1. Verifica que estés en el directorio correcto
2. Verifica que Composer esté instalado: `composer --version`
3. Verifica que las dependencias estén instaladas: `ls -la vendor/`
4. Si no están instaladas: `composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev`

## Formato Correcto de APP_KEY

El valor debe tener este formato:

```
base64:AbCdEfGhIjKlMnOpQrStUvWxYz1234567890AbCdEfGhIjKlMnOpQrStUvWxYz1234567890=
```

**Características:**
- ✅ Empieza con `base64:`
- ✅ Tiene aproximadamente 88 caracteres después de `base64:`
- ✅ Termina con `=` (puede tener 1 o 2 signos `=`)

**Ejemplos INCORRECTOS:**
- ❌ `AbCdEfGh...` (falta el prefijo `base64:`)
- ❌ `base64: AbCdEfGh...` (tiene espacio después de `:`)
- ❌ Solo números o letras sin `base64:`

## Variables de Entorno Relacionadas

Asegúrate de tener estas variables también configuradas:

```env
APP_NAME=NSINC
APP_ENV=production
APP_KEY=base64:... (LA QUE ACABAS DE CONFIGURAR)
APP_DEBUG=false
APP_URL=https://nsinc.hwa-company.com
```

## Notas Importantes

- ⚠️ **NUNCA compartas tu APP_KEY** públicamente
- 🔒 **Cada aplicación debe tener su propia APP_KEY única**
- 📝 **Si cambias APP_KEY**, todos los datos encriptados anteriormente se perderán
- 🔄 **Después de cambiar APP_KEY**, siempre ejecuta `php artisan config:clear`
- ✅ **Verifica siempre** que esté configurado con `php artisan tinker` → `config('app.key')`

## Checklist de Configuración

- [ ] Generé la clave con `php artisan key:generate --show`
- [ ] Copié el valor completo (incluyendo `base64:`)
- [ ] Agregué `APP_KEY` en Ploi.io → Environment Variables
- [ ] Guardé los cambios en Ploi.io
- [ ] Ejecuté `php artisan config:clear` en el servidor
- [ ] Ejecuté `php artisan config:cache` en el servidor
- [ ] Verifiqué con `php artisan tinker` → `config('app.key')`
- [ ] Probé la aplicación en el navegador
- [ ] El error 500 desapareció ✅

## Comandos Rápidos de Referencia

```bash
# Generar y mostrar clave
php artisan key:generate --show

# Verificar que está configurada
php artisan tinker
>>> config('app.key')

# Limpiar y reconstruir cache
php artisan config:clear
php artisan config:cache

# Verificar logs si hay problemas
tail -n 50 storage/logs/laravel.log
```



