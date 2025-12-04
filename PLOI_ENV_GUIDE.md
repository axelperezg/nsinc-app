# Guía de Variables de Entorno para Ploi.io

## Variables de Base de Datos (MySQL/MariaDB)

Cuando creas una base de datos en Ploi.io, te proporcionan las credenciales. Aquí están las variables que debes configurar en la sección **Environment Variables** de tu sitio:

### Variables Requeridas:

```env
# Tipo de conexión (mysql o mariadb)
DB_CONNECTION=mysql

# Host de la base de datos
# En Ploi.io generalmente es: 127.0.0.1 o localhost
DB_HOST=127.0.0.1

# Puerto (generalmente 3306 para MySQL/MariaDB)
DB_PORT=3306

# Nombre de la base de datos
# Lo encuentras en: Databases > Tu Base de Datos > Overview
DB_DATABASE=nombre_de_tu_base_de_datos

# Usuario de la base de datos
# Lo encuentras en: Databases > Tu Base de Datos > Overview
DB_USERNAME=usuario_de_la_bd

# Contraseña de la base de datos
# La contraseña que configuraste al crear la base de datos
DB_PASSWORD=tu_contraseña_segura
```

### Variables Opcionales (con valores por defecto):

```env
# Charset (por defecto: utf8mb4)
DB_CHARSET=utf8mb4

# Collation (por defecto: utf8mb4_unicode_ci)
DB_COLLATION=utf8mb4_unicode_ci

# Socket Unix (generalmente vacío en Ploi.io)
DB_SOCKET=
```

## Cómo Obtener los Valores en Ploi.io:

1. **Ve a tu servidor en Ploi.io**
2. **Haz clic en "Databases"** en el menú lateral
3. **Selecciona tu base de datos** (o créala si no existe)
4. **En la pestaña "Overview"** encontrarás:
   - **Database Name**: Usa este valor para `DB_DATABASE`
   - **Database User**: Usa este valor para `DB_USERNAME`
   - **Database Password**: Usa este valor para `DB_PASSWORD`
   - **Host**: Generalmente `127.0.0.1` o `localhost` para `DB_HOST`

## Ejemplo Completo de Configuración:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nsinc_app
DB_USERNAME=nsinc_user
DB_PASSWORD=TuContraseñaSegura123!
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

## Notas Importantes:

- ⚠️ **Nunca compartas tus credenciales** de base de datos
- 🔒 Las contraseñas en Ploi.io se muestran ocultas por seguridad
- 📝 Si olvidaste la contraseña, puedes cambiarla desde la interfaz de Ploi.io
- 🔄 Después de cambiar las variables de entorno, recarga PHP-FPM o reinicia el sitio

## Verificar la Conexión:

Después de configurar las variables, puedes verificar la conexión ejecutando en el terminal de Ploi.io:

```bash
php artisan tinker
```

Y luego en Tinker:
```php
DB::connection()->getPdo();
```

Si no hay errores, la conexión está funcionando correctamente.

