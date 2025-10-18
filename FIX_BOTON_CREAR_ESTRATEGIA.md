# Fix: Botón "Crear Estrategia" No Visible en Lista

**Fecha:** 2025-10-16
**Problema reportado:** Al cambiar el filtro de año a 2026 en la lista, no aparece el botón "Crear Estrategia".

---

## 🐛 Problema Identificado

### Síntomas:
1. Usuario cambia el filtro de año a 2026 en la lista de estrategias
2. El botón "Crear Estrategia" **NO aparece** en el header de la lista
3. Aunque el usuario NO tiene una estrategia de 2026 para su institución

### Causa Raíz:

El código estaba verificando si existe **cualquier estrategia** de 2026 en toda la base de datos:

```php
// ❌ INCORRECTO - Busca en TODAS las instituciones
$estrategiaExistente = Estrategy::where('anio', $anio)->first();
```

**El problema:**
- Si **otra institución** ya creó una estrategia de 2026, el botón no aparece para NADIE
- No respeta el scope por institución del usuario
- Usuarios de otras instituciones sin estrategia no pueden crear

**Ejemplo:**
1. Institución A crea estrategia de 2026 ✅
2. Institución B (sin estrategia de 2026) intenta crear
3. El sistema busca estrategias de 2026 → Encuentra la de Institución A
4. Oculta el botón para Institución B ❌

---

## ✅ Solución Implementada

### Verificar Solo Estrategias de la Institución del Usuario

Modifiqué `getHeaderActions()` para filtrar por `institution_id`:

```php
protected function getHeaderActions(): array
{
    $actions = [];

    // Obtener el año del filtro actual
    $anio = $this->getFilteredYear();
    $user = Auth::user();

    // Verificar si ya existe una estrategia para este año
    // ✅ SOLO de la institución del usuario
    $estrategiaExistente = null;

    if ($user && $user->institution_id) {
        $estrategiaExistente = Estrategy::where('anio', $anio)
            ->where('institution_id', $user->institution_id)  // ← Filtro por institución
            ->first();
    }

    // Solo mostrar el botón si NO existe estrategia para el año filtrado
    if (!$estrategiaExistente && $user && $user->institution_id) {
        $actions[] = Actions\CreateAction::make()
            ->url(fn () => static::getResource()::getUrl('create', ['year' => $anio]));
    }

    return $actions;
}
```

**Validaciones agregadas:**
1. ✅ Verificar que el usuario existe (`$user`)
2. ✅ Verificar que el usuario tiene institución (`$user->institution_id`)
3. ✅ Buscar estrategias **solo de esa institución** (`where('institution_id', ...)`)
4. ✅ Pasar el año en la URL (`['year' => $anio]`)

---

## 📋 Flujo Corregido

### Escenario: Usuario de Institución B quiere crear estrategia de 2026

**Antes (Bug):**
1. Usuario B cambia filtro a 2026
2. Sistema busca: `Estrategy::where('anio', 2026)->first()`
3. Encuentra estrategia de Institución A
4. Oculta botón "Crear Estrategia" ❌
5. Usuario B no puede crear su estrategia

**Ahora (Corregido):**
1. Usuario B cambia filtro a 2026
2. Sistema busca: `Estrategy::where('anio', 2026)->where('institution_id', B)->first()`
3. No encuentra estrategia (Institución B no tiene)
4. Muestra botón "Crear Estrategia" ✅
5. Usuario B puede crear su estrategia

---

## 🎯 Lógica de Visibilidad del Botón

El botón "Crear Estrategia" aparece cuando:

1. ✅ El usuario está autenticado
2. ✅ El usuario tiene una institución asignada (`institution_id`)
3. ✅ **NO existe** una estrategia para el año filtrado **de su institución**
4. ✅ El usuario tiene permisos para crear (validación de Filament)

El botón **NO aparece** cuando:

1. ❌ El usuario no tiene institución
2. ❌ Ya existe una estrategia de ese año **para su institución**
3. ❌ El usuario no tiene permisos de creación

---

## 🧪 Casos de Prueba

### Test Case 1: Sin estrategia de 2026 en mi institución

**Pre-condiciones:**
- Usuario de Institución B
- NO existe estrategia de 2026 para Institución B
- Puede existir estrategia de 2026 en otras instituciones

**Pasos:**
1. Ir a lista de estrategias
2. Cambiar filtro de año a 2026

**Resultado esperado:**
- ✅ Botón "Crear Estrategia" es visible
- ✅ Al hacer clic va a `/admin/estrategies/create?year=2026`

---

### Test Case 2: Ya existe estrategia de 2026 en mi institución

**Pre-condiciones:**
- Usuario de Institución B
- Ya existe estrategia de 2026 para Institución B

**Pasos:**
1. Ir a lista de estrategias
2. Cambiar filtro de año a 2026

**Resultado esperado:**
- ❌ Botón "Crear Estrategia" NO es visible
- ✅ Se ve la estrategia existente en la tabla

---

### Test Case 3: Otra institución tiene estrategia, yo no

**Pre-condiciones:**
- Usuario de Institución B
- Institución A ya tiene estrategia de 2026
- Institución B NO tiene estrategia de 2026

**Pasos:**
1. Ir a lista de estrategias (solo veo mi institución)
2. Cambiar filtro de año a 2026

**Resultado esperado:**
- ✅ Botón "Crear Estrategia" es visible (no me afecta que otra institución tenga)
- ✅ Puedo crear mi estrategia de 2026

---

### Test Case 4: Usuario sin institución

**Pre-condiciones:**
- Usuario sin `institution_id` (caso edge)

**Pasos:**
1. Ir a lista de estrategias
2. Cambiar filtro de año a 2026

**Resultado esperado:**
- ❌ Botón "Crear Estrategia" NO es visible
- ℹ️ Usuario debe tener institución para crear estrategias

---

## 📁 Archivos Modificados

### `app/Filament/Resources/EstrategyResource/Pages/ListEstrategies.php`

**Cambios:**
- ✅ Agregado filtro por `institution_id` en la búsqueda de estrategias existentes
- ✅ Agregado validación de usuario e institución
- ✅ Mantenido el paso de año en URL (`['year' => $anio]`)

**Líneas modificadas:**
- Línea 33: Agregado `$user = Auth::user();`
- Líneas 35-42: Nueva lógica con filtro por institución
- Línea 45: Agregada validación de usuario e institución

---

## 🔗 Relación con Otros Fixes

Este fix trabaja en conjunto con:

1. **FIX_BOTON_COPIAR_ANO_ANTERIOR.md**
   - Ambos usan el parámetro `?year=XXXX` en la URL
   - Aseguran que el año correcto se pase de lista → crear

2. **MEJORAS_UX_IMPLEMENTADAS.md**
   - El flujo completo es: Lista → Crear → Copiar (opcional) → Llenar → Guardar

---

## 🔍 Debugging

Si el botón "Crear Estrategia" no aparece cuando debería:

### 1. Verificar que tienes institución asignada

```php
// En ListEstrategies.php, método getHeaderActions():
dd([
    'user_id' => Auth::id(),
    'institution_id' => Auth::user()->institution_id,
    'year' => $this->getFilteredYear(),
]);
```

### 2. Verificar búsqueda de estrategias existentes

```php
$user = Auth::user();
$anio = $this->getFilteredYear();

$estrategiaExistente = Estrategy::where('anio', $anio)
    ->where('institution_id', $user->institution_id)
    ->first();

dd([
    'year' => $anio,
    'institution_id' => $user->institution_id,
    'estrategia_found' => $estrategiaExistente !== null,
    'estrategia' => $estrategiaExistente,
]);
```

### 3. Verificar permisos de Filament

```php
$createAction = Actions\CreateAction::make();
dd([
    'can_create' => $createAction->isVisible(),
    'authorization' => static::getResource()::canCreate(),
]);
```

### 4. Limpiar cache

```bash
php artisan cache:clear
php artisan view:clear
php artisan filament:cache-components
```

---

## ✅ Checklist de Verificación

- [x] Filtro por `institution_id` agregado
- [x] Validación de usuario agregada
- [x] Validación de institución agregada
- [x] Parámetro `year` se pasa en URL
- [x] Respeta scope de institución
- [x] No afecta a otras instituciones
- [x] Funciona con todos los roles
- [x] Documentación creada

---

## 🎯 Resumen

**Problema:** El botón "Crear Estrategia" verificaba si existía estrategia en CUALQUIER institución, no solo la del usuario.

**Solución:** Filtrar búsqueda de estrategias existentes por `institution_id` del usuario autenticado.

**Impacto:**
- ✅ Cada institución puede crear su propia estrategia independientemente
- ✅ No hay interferencia entre instituciones
- ✅ Respeta el modelo de multi-tenancy por institución
- ✅ Usuario ve el botón cuando corresponde

**Regla de negocio:**
> Cada institución puede tener **una estrategia por año**. La existencia de estrategias en otras instituciones no afecta la capacidad de crear la propia.

---

**Implementado por:** Claude Code
**Fecha:** 2025-10-16
**Versión:** 1.0.2
**Relacionado con:** FIX_BOTON_COPIAR_ANO_ANTERIOR.md
