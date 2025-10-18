# Sistema de Validación de Fechas de Vencimiento

## Descripción General

Este sistema implementa validaciones automáticas de fechas límite para controlar cuándo los usuarios pueden crear, editar o modificar estrategias según el concepto y año.

## Conceptos de Vencimiento

El sistema maneja 3 conceptos principales:

1. **Registro** - Controla la creación de nuevas estrategias
2. **Modificación** - Controla modificaciones y cancelaciones de estrategias autorizadas
3. **Observación** - Controla las solventaciones de estrategias observadas por DGNC

## Estructura de Fechas

Cada fecha de vencimiento tiene 4 fechas clave:

- **fecha_inicio**: Fecha desde la cual se puede realizar la acción
- **fecha_diaPrevio**: Fecha que activa advertencias (quedan pocos días)
- **fecha_limite**: Última fecha permitida para realizar la acción
- **fecha_restrictiva**: Fecha de corte absoluto (después de esta nadie puede hacer nada)

## Cómo Funciona

### 1. Estados de Validación

El sistema evalúa la fecha actual contra las fechas configuradas y retorna uno de estos estados:

- **not_started**: Aún no ha iniciado el período (antes de `fecha_inicio`)
- **active**: Período activo, se puede realizar la acción sin restricciones
- **warning**: Período de advertencia (después de `fecha_diaPrevio`, quedan pocos días)
- **expired**: Período vencido (después de `fecha_limite`)
- **restricted**: Totalmente bloqueado (después de `fecha_restrictiva`)

### 2. Validación Automática

Las validaciones se aplican automáticamente en:

- **Crear estrategia** (`CreateEstrategy`) - Valida "Registro"
- **Editar estrategia** (`EditEstrategy`) - Valida según el concepto de la estrategia
- **Modificar estrategia** (`ModificarEstrategy`) - Valida "Modificación"
- **Solventar estrategia** (`SolventarEstrategy`) - Valida "Observación"
- **Cancelar estrategia** (`CancelarEstrategy`) - Valida "Modificación"

### 3. Notificaciones

El sistema muestra notificaciones automáticas:

- ✅ **Verde (Success)**: Acción permitida, muestra días restantes
- ⚠️ **Amarillo (Warning)**: Acción permitida pero quedan pocos días
- 🚫 **Rojo (Danger)**: Acción bloqueada, fecha vencida
- ℹ️ **Azul (Info)**: Aún no inicia el período

## Configuración

### Crear Fechas de Vencimiento

1. Ir a **Administración del Sistema > Fechas de Vencimiento**
2. Hacer clic en **Crear**
3. Llenar el formulario:
   - **Año**: 2025, 2026, etc.
   - **Concepto**: Registro, Modificación u Observación
   - **Fechas**: fecha_inicio, fecha_diaPrevio, fecha_limite, fecha_restrictiva
   - **Descripción**: Texto descriptivo para referencia

### Ejemplo de Configuración

```
Año: 2025
Concepto: Registro
fecha_inicio: 2025-01-15
fecha_diaPrevio: 2025-03-01
fecha_limite: 2025-03-15
fecha_restrictiva: 2025-03-20
Descripción: Período de registro de estrategias 2025
```

## Mapeo de Conceptos

El helper `ExpirationDateHelper` mapea automáticamente los conceptos de estrategia a conceptos de vencimiento:

| Concepto de Estrategia | Concepto de Vencimiento |
|------------------------|-------------------------|
| Registro               | Registro                |
| Modificación/Modificacion | Modificación        |
| Cancelación/Cancelacion | Modificación           |
| Solventación/Solventacion | Observación          |

## Widget Visual

El sistema incluye un widget que se muestra en la página de lista de estrategias para usuarios de institución:

- Muestra el estado de las 3 fechas de vencimiento del año actual
- Código de colores según el estado (verde/amarillo/rojo)
- Detalle de todas las fechas configuradas
- Indicador de "Permitido" o "Bloqueado"

## Archivos Principales

### Modelo
- `app/Models/ExpirationDate.php` - Modelo con métodos para verificar estados

### Helper
- `app/Helpers/ExpirationDateHelper.php` - Lógica centralizada de validación

### Páginas con Validación
- `app/Filament/Resources/EstrategyResource/Pages/CreateEstrategy.php`
- `app/Filament/Resources/EstrategyResource/Pages/EditEstrategy.php`
- `app/Filament/Resources/EstrategyResource/Pages/ModificarEstrategy.php`
- `app/Filament/Resources/EstrategyResource/Pages/SolventarEstrategy.php`
- `app/Filament/Resources/EstrategyResource/Pages/CancelarEstrategy.php`

### Widget
- `app/Filament/Widgets/ExpirationDatesWidget.php`
- `resources/views/filament/widgets/expiration-dates-widget.blade.php`

## Uso Programático

### Validar una acción

```php
use App\Helpers\ExpirationDateHelper;

// Validar si se puede realizar una acción
$validation = ExpirationDateHelper::canPerformAction('Registro', 2025);

if ($validation['allowed']) {
    // Acción permitida
    echo $validation['message']; // "Puede realizar Registro. Fecha límite: 15/03/2025 (45 días restantes)"
} else {
    // Acción bloqueada
    echo $validation['message']; // "No se puede realizar Registro. La fecha límite ha vencido."
}

// Nivel de severidad: 'success', 'warning', 'danger', 'info'
$level = $validation['level'];

// Objeto ExpirationDate o null
$expiration = $validation['expiration'];
```

### Validar concepto de estrategia

```php
// Valida automáticamente según el mapeo de conceptos
$validation = ExpirationDateHelper::validateEstrategyConcept('Modificación', 2025);
```

### Obtener todos los estados

```php
// Obtener estado de todos los conceptos para un año
$statuses = ExpirationDateHelper::getAllExpirationStatuses(2025);

// Retorna:
// [
//     'Registro' => ['allowed' => true, 'message' => '...', ...],
//     'Modificación' => ['allowed' => false, 'message' => '...', ...],
//     'Observación' => ['allowed' => true, 'message' => '...', ...],
// ]
```

## Comportamiento Especial

### Sin fechas configuradas

Si no hay fechas de vencimiento configuradas para un año/concepto:
- La acción se **permite por defecto**
- Se muestra una notificación informativa
- Se registra un warning en los logs

### Super Admin

Los super administradores ven el ExpirationDateResource pero **no** ven el widget de fechas (no lo necesitan ya que tienen acceso completo).

### Validación en múltiples puntos

Las validaciones se ejecutan en:
1. `mount()` - Al cargar la página (previene acceso)
2. `beforeCreate()/beforeSave()` - Antes de guardar (validación final)
3. `afterCreate()/afterSave()` - Después de guardar (notificación con recordatorio)

Esto garantiza que incluso si alguien intenta burlar la validación del frontend, será bloqueado en el backend.

## Mantenimiento

### Crear fechas para nuevo año

1. Duplicar las fechas del año anterior
2. Cambiar el campo "Año" al nuevo año
3. Ajustar las fechas según calendario
4. Guardar

### Extender período de vencimiento

1. Editar la fecha de vencimiento correspondiente
2. Modificar `fecha_limite` y/o `fecha_restrictiva`
3. Guardar - los cambios aplican inmediatamente

### Agregar nuevo concepto

1. Agregar concepto en `ExpirationDateResource` (select options)
2. Agregar mapeo en `ExpirationDateHelper::validateEstrategyConcept()`
3. Crear fechas de vencimiento para el nuevo concepto
4. Agregar validación en las páginas correspondientes

## Logs

El sistema registra en logs cuando:
- No se encuentra una fecha de vencimiento configurada
- Se intenta acceder fuera del período permitido

Ver logs en: `storage/logs/laravel.log`

## Testing

Para probar el sistema:

1. Crear fechas de vencimiento de prueba con rangos cortos
2. Cambiar la fecha del sistema o ajustar las fechas de vencimiento
3. Intentar crear/editar estrategias
4. Verificar notificaciones y bloqueos

## Troubleshooting

### "No hay fechas de vencimiento configuradas"

**Causa**: No existe registro en `expiration_dates` para ese concepto/año

**Solución**: Crear fecha de vencimiento desde el panel admin

### Widget no se muestra

**Causa**: Usuario es super_admin o no tiene rol institution_user/institution_admin

**Solución**: Esto es comportamiento esperado, solo usuarios de institución ven el widget

### Validación no bloquea

**Causa**:
- Fechas mal configuradas (fecha_limite después de fecha_restrictiva)
- Año incorrecto en la validación
- Cache de usuario

**Solución**:
- Verificar fechas en la base de datos
- Limpiar cache: `php artisan cache:clear`
- Cerrar sesión y volver a iniciar

## Mejoras Futuras

Posibles extensiones del sistema:

1. **Cache**: Cachear validaciones para mejorar performance
2. **Notificaciones proactivas**: Enviar emails X días antes del vencimiento
3. **Historial**: Registrar intentos de acceso bloqueados
4. **Excepciones**: Permitir a super_admin otorgar extensiones temporales
5. **Dashboard**: Métricas de cumplimiento de fechas límite
