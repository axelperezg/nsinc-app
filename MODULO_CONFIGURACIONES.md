# Módulo de Configuraciones del Sistema

**Fecha:** 2025-10-16
**Estado:** ✅ Completado e Implementado

---

## 📋 Descripción

Se ha creado un sistema completo de configuraciones que permite al **super_admin** activar/desactivar funcionalidades del sistema desde una interfaz gráfica en Filament, sin necesidad de modificar código.

### 🎯 Objetivo Principal

Permitir al administrador controlar la visibilidad del **Widget de Fechas de Vencimiento** para usuarios de institución, mediante un simple toggle en la interfaz de administración.

---

## ✅ Funcionalidades Implementadas

### 1. **Modelo de Configuración Flexible**
- Sistema key-value con soporte para múltiples tipos de datos
- Cache automático (1 hora) para optimizar rendimiento
- Actualización automática del cache al modificar valores

### 2. **Interfaz de Administración (Solo super_admin)**
- Módulo "Configuraciones" en el menú "Sistema"
- Lista de configuraciones con:
  - Badge de grupo (Widgets, General, Notificaciones)
  - Estado visual (✓/✗)
  - Última modificación
  - Descripción de cada configuración
- Edición simple con Toggle para valores booleanos
- **NO permite** crear ni eliminar configuraciones (seguridad)

### 3. **Widget de Vencimientos Controlable**
- Visibilidad controlada por configuración
- Por defecto: **Activado**
- El administrador puede desactivarlo con un clic

---

## 🗂️ Estructura de Archivos

### Migración
```
database/migrations/2025_10_16_190619_create_configurations_table.php
```

**Tabla `configurations`:**
- `id`: Identificador único
- `key`: Clave única (ej: `widget.expiration_dates.enabled`)
- `value`: Valor (string, se convierte según type)
- `type`: Tipo de dato (boolean, string, integer, json, array)
- `group`: Grupo (widgets, general, notifications)
- `label`: Etiqueta descriptiva
- `description`: Descripción detallada
- `timestamps`: Fechas de creación/actualización

### Modelo
```
app/Models/Configuration.php
```

**Métodos principales:**
- `Configuration::get($key, $default)` - Obtener valor con cache
- `Configuration::set($key, $value)` - Establecer valor
- `$config->getTypedValue()` - Obtener valor convertido
- `Configuration::clearCache()` - Limpiar todo el cache

**Ejemplo de uso:**
```php
// Obtener configuración
$enabled = Configuration::get('widget.expiration_dates.enabled', true);

// Establecer configuración
Configuration::set('widget.expiration_dates.enabled', false);
```

### Resource de Filament
```
app/Filament/Resources/ConfigurationResource.php
app/Filament/Resources/ConfigurationResource/Pages/ListConfigurations.php
app/Filament/Resources/ConfigurationResource/Pages/EditConfiguration.php
```

**Características:**
- Solo visible para `super_admin`
- NO permite crear configuraciones (se crean por seeder/migración)
- NO permite eliminar configuraciones
- Solo permite editar el valor

### Seeder
```
database/seeders/ConfigurationSeeder.php
```

**Configuraciones por defecto:**
```php
[
    'key' => 'widget.expiration_dates.enabled',
    'value' => '1',  // Activado
    'type' => 'boolean',
    'group' => 'widgets',
    'label' => 'Widget de Fechas de Vencimiento',
    'description' => 'Mostrar el widget de fechas de vencimiento...',
]
```

### Widget Modificado
```
app/Filament/Widgets/ExpirationDatesWidget.php
```

**Cambio en método `canView()`:**
```php
public static function canView(): bool
{
    $user = Auth::user();

    // Verificar rol de institución
    if (!$user || !$user->role || !in_array($user->role->name, [
        'institution_user',
        'institution_admin',
    ])) {
        return false;
    }

    // Verificar si está activado en configuración
    return \App\Models\Configuration::get('widget.expiration_dates.enabled', true);
}
```

---

## 🚀 Cómo Usar el Sistema

### Para el Super Admin:

1. **Acceder a Configuraciones:**
   - Iniciar sesión como `super_admin`
   - En el menú lateral, ir a **Sistema → Configuraciones**

2. **Ver Configuraciones:**
   - Se muestra una tabla con todas las configuraciones disponibles
   - Columnas: Grupo, Configuración, Estado, Última modificación

3. **Activar/Desactivar Widget:**
   - Hacer clic en **"Editar"** en la fila "Widget de Fechas de Vencimiento"
   - Cambiar el toggle "Activado" a ON/OFF
   - Hacer clic en **"Guardar"**

4. **Efecto Inmediato:**
   - El cambio se aplica de inmediato (con cache de máximo 1 hora)
   - Los usuarios de institución verán/no verán el widget según la configuración

### Para Desarrolladores:

**Agregar nueva configuración:**

1. Crear entrada en seeder:
```php
[
    'key' => 'nueva.configuracion',
    'value' => 'valor_por_defecto',
    'type' => 'boolean', // o string, integer, json
    'group' => 'general',
    'label' => 'Nombre Descriptivo',
    'description' => 'Descripción detallada',
]
```

2. Ejecutar seeder:
```bash
php artisan db:seed --class=ConfigurationSeeder
```

3. Usar en código:
```php
$valor = Configuration::get('nueva.configuracion', 'fallback');
```

---

## 📊 Configuraciones Disponibles

| Clave | Tipo | Grupo | Descripción | Por Defecto |
|-------|------|-------|-------------|-------------|
| `widget.expiration_dates.enabled` | boolean | widgets | Mostrar widget de vencimientos a instituciones | `true` |

*(Más configuraciones se pueden agregar en el futuro)*

---

## 🔒 Seguridad

### Permisos Implementados:

1. **Solo super_admin puede:**
   - Ver el módulo de configuraciones
   - Editar valores de configuraciones

2. **Nadie puede:**
   - Crear configuraciones desde la UI (solo por seeder/código)
   - Eliminar configuraciones (protección de datos críticos)

3. **El sistema:**
   - NO expone claves sensibles
   - Solo muestra valores editables
   - Valida tipos de datos automáticamente

---

## 🎨 Interfaz de Usuario

### Vista de Lista:
```
┌─────────────────────────────────────────────────┐
│ Sistema → Configuraciones                        │
├─────────────────────────────────────────────────┤
│ Grupo      Configuración                 Estado │
│ [widgets]  Widget de Fechas de Venc...  ✓      │
│                                                  │
│ Última modificación: 16/10/2025 19:15           │
└─────────────────────────────────────────────────┘
```

### Vista de Edición:
```
┌─────────────────────────────────────────────────┐
│ Editar Configuración                             │
├─────────────────────────────────────────────────┤
│ Nombre:                                          │
│ Widget de Fechas de Vencimiento                 │
│                                                  │
│ Clave:                                           │
│ widget.expiration_dates.enabled                 │
│                                                  │
│ Descripción:                                     │
│ Mostrar el widget de fechas de vencimiento...   │
│                                                  │
│ ┌─────────────────────────────────────────┐    │
│ │ Valor                                    │    │
│ │ Activado: [●─────] ON                    │    │
│ └─────────────────────────────────────────┘    │
│                                                  │
│ [Guardar]  [Cancelar]                           │
└─────────────────────────────────────────────────┘
```

---

## 🔄 Flujo de Funcionamiento

### Cuando el Admin Activa/Desactiva:

1. Admin cambia toggle en Configuraciones
2. Se guarda en base de datos
3. Cache se limpia automáticamente
4. Próxima vez que un usuario de institución carga la página:
   - Widget lee configuración
   - Si está activado → Muestra widget
   - Si está desactivado → Oculta widget

### Cache y Rendimiento:

- **Primera lectura:** Consulta base de datos → Guarda en cache (1 hora)
- **Lecturas siguientes:** Lee del cache (rápido)
- **Al modificar:** Limpia cache automáticamente
- **Después de 1 hora:** Se refresca cache automáticamente

---

## 📈 Extensibilidad

El sistema está diseñado para crecer. Ejemplos de configuraciones futuras:

```php
// Notificaciones
'notifications.email.enabled' => true,
'notifications.email.from_address' => 'noreply@example.com',

// Límites del sistema
'system.max_campaigns_per_strategy' => 10,
'system.max_file_upload_size' => 5242880, // 5MB

// Features toggles
'features.export_pdf.enabled' => true,
'features.bulk_actions.enabled' => true,

// Textos personalizables
'ui.welcome_message' => 'Bienvenido al sistema',
'ui.footer_text' => 'Sistema NSINC v1.0',
```

---

## 🧪 Testing

### Probar la Funcionalidad:

1. **Como super_admin:**
   ```
   1. Ir a Sistema → Configuraciones
   2. Editar "Widget de Fechas de Vencimiento"
   3. Desactivar toggle
   4. Guardar
   ```

2. **Como institution_user:**
   ```
   1. Ir a lista de Estrategias
   2. Verificar que el widget NO aparece
   ```

3. **Volver a activar:**
   ```
   1. Como super_admin, activar toggle
   2. Como institution_user, refrescar página
   3. Verificar que el widget SÍ aparece
   ```

---

## ⚙️ Comandos Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeder de configuraciones
php artisan db:seed --class=ConfigurationSeeder

# Limpiar cache de configuraciones (si es necesario)
php artisan cache:clear

# Ver todas las configuraciones en consola
php artisan tinker
>>> App\Models\Configuration::all()
```

---

## 📝 Notas Técnicas

### Conversión de Tipos:

El modelo convierte automáticamente los valores según su tipo:

```php
// boolean: '1' → true, '0' → false
// integer: '123' → 123
// float: '12.5' → 12.5
// json: '{"a":1}' → ['a' => 1]
// string: se mantiene como string
```

### Event Listeners:

El modelo tiene hooks automáticos:

```php
// Al guardar → Limpia cache
static::saved(function ($config) {
    Cache::forget("config_{$config->key}");
});

// Al eliminar → Limpia cache
static::deleted(function ($config) {
    Cache::forget("config_{$config->key}");
});
```

---

## ✅ Checklist de Implementación

- [x] Migración de tabla `configurations`
- [x] Modelo `Configuration` con métodos get/set
- [x] Cache automático en modelo
- [x] Resource de Filament `ConfigurationResource`
- [x] Permisos solo para `super_admin`
- [x] Formulario con Toggle para booleanos
- [x] Tabla con badges y estados visuales
- [x] Seeder con configuración inicial
- [x] Modificación de `ExpirationDatesWidget`
- [x] Migración ejecutada
- [x] Seeder ejecutado
- [x] Documentación completa

---

## 🎉 Resultado Final

### Antes:
- Widget de vencimientos siempre visible para instituciones
- No había forma de desactivarlo sin modificar código

### Ahora:
- Super admin puede activar/desactivar desde la UI
- Sin necesidad de tocar código
- Cambio inmediato y reversible
- Sistema extensible para futuras configuraciones

---

**Implementado por:** Claude Code
**Fecha:** 2025-10-16
**Versión:** 1.0.0
**Estado:** ✅ Productivo
