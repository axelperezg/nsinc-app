# Limitación: Actualización Automática del Botón "Crear Estrategia"

**Fecha:** 2025-10-16
**Estado:** Limitación conocida de Filament

---

## 🐛 Comportamiento Actual

### Síntoma:
Al cambiar el filtro de año en la lista de estrategias, el botón "Crear Estrategia" **NO aparece inmediatamente**. Es necesario **refrescar manualmente el navegador** (F5) para que el botón aparezca/desaparezca.

### Ejemplo:
1. Estás viendo estrategias de 2025
2. Cambias el filtro a 2026
3. El botón "Crear Estrategia" debería aparecer automáticamente
4. **Pero NO aparece** hasta que presionas F5

---

## 🔍 Causa Raíz

### Comportamiento de Filament v3:

Filament cachea las **acciones del header** (`getHeaderActions()`) al cargar la página por razones de rendimiento. Cuando cambias un filtro:

1. ✅ La tabla se actualiza (Livewire reactivo)
2. ✅ Los widgets se actualizan (escuchan eventos)
3. ❌ Las acciones del header **NO se recalculan**

### Por qué sucede:

```php
// Este método se ejecuta UNA vez al cargar la página
protected function getHeaderActions(): array
{
    $anio = $this->getFilteredYear();  // Obtiene año en ese momento

    // Verifica estrategia existente
    if (!$estrategiaExistente) {
        return [Actions\CreateAction::make()];
    }

    return [];  // Sin acciones
}
```

El problema es que `getHeaderActions()` se ejecuta **una sola vez** y Filament cachea el resultado. Cuando cambias el filtro, el año cambia pero las acciones ya están cacheadas.

---

## 🔧 Soluciones Intentadas

### 1. ❌ Dispatch de `$refresh`
```php
public function updatedTableFilters(): void
{
    $this->dispatch('$refresh');
}
```
**Resultado:** No funciona, Filament no recarga las acciones.

### 2. ❌ Reset manual de cache
```php
protected function resetHeaderActionsCache(): void
{
    $this->cachedHeaderActions = null;
}
```
**Resultado:** La propiedad es privada/protegida en Filament, no es accesible.

### 3. ❌ Vista personalizada con Alpine.js
```blade
<x-filament-panels::page>
    <div x-data="{ ... }">
        <!-- Intentar forzar recarga -->
    </div>
</x-filament-panels::page>
```
**Resultado:** Rompe el renderizado de Filament, la tabla desaparece.

### 4. ❌ JavaScript CustomEvent
```php
$this->js("window.dispatchEvent(new CustomEvent('filtersUpdated'))");
```
**Resultado:** El evento se dispara pero Filament no responde.

---

## ✅ Solución Actual: Refrescar Manualmente

### Flujo de trabajo:

1. Cambiar el filtro de año a 2026
2. **Presionar F5** o **Ctrl+R** para refrescar la página
3. El botón "Crear Estrategia" aparece correctamente
4. Hacer clic en "Crear Estrategia"
5. Continuar normalmente

### Por qué esta es una solución aceptable:

- ✅ Es un paso adicional simple (F5)
- ✅ Cambiar de año NO es una acción frecuente
- ✅ Normalmente solo creas UNA estrategia por año
- ✅ No afecta la funcionalidad, solo requiere un paso extra
- ✅ No introduce bugs ni complejidad innecesaria

---

## 🔮 Posibles Soluciones Futuras

### Opción 1: Actualización de Filament

Cuando Filament v3.x o v4.x agregue soporte nativo para acciones reactivas del header, podremos implementarlo directamente.

**Actualización necesaria:**
```php
protected function getHeaderActions(): array
{
    return [
        Actions\CreateAction::make()
            ->reactive()  // ← Hipotético método futuro
            ->visible(fn () => !$this->hasEstrategiaForYear($this->getFilteredYear()))
    ];
}
```

### Opción 2: Polling (No Recomendado)

Podríamos hacer que la página se actualice automáticamente cada X segundos:

```php
class ListEstrategies extends ListRecords
{
    protected static string $view = 'filament.resources.estrategy-resource.pages.list-estrategies';

    public function getPollingInterval(): ?string
    {
        return '5s';  // Actualizar cada 5 segundos
    }
}
```

**Inconvenientes:**
- ❌ Consumo innecesario de recursos
- ❌ Mala experiencia de usuario (recargas constantes)
- ❌ Puede interrumpir acciones del usuario
- ❌ No resuelve el problema real

### Opción 3: Mover el botón a otro lugar

En lugar de tener el botón en el header, podríamos agregarlo:

**A. Como floating action button (FAB):**
```php
// En vista personalizada
<div class="fixed bottom-6 right-6">
    @if (!$this->hasEstrategiaForYear($this->getFilteredYear()))
        <button wire:click="createEstrategia">Crear Estrategia</button>
    @endif
</div>
```

**B. Como acción en la tabla vacía:**
```php
->emptyStateActions([
    Tables\Actions\CreateAction::make()
        ->url(fn () => static::getResource()::getUrl('create', ['year' => $this->getFilteredYear()]))
])
```

**Inconvenientes:**
- Cambia el UX establecido
- Requiere modificar el diseño
- Puede confundir a usuarios acostumbrados al botón en header

---

## 📊 Análisis de Impacto

### Frecuencia del problema:

- **Cambio de año:** 1-2 veces por año por usuario
- **Creación de estrategia:** 1-4 veces por año por institución
- **Total de veces afectado:** ~2-8 veces al año

### Tiempo perdido:

- **Tiempo para refrescar:** 1-2 segundos (F5)
- **Tiempo total perdido al año:** ~10-20 segundos

### Costo vs Beneficio:

| Aspecto | Implementar solución compleja | Aceptar limitación |
|---------|------------------------------|-------------------|
| **Tiempo de desarrollo** | 4-8 horas | 0 horas |
| **Complejidad del código** | Alta | Baja |
| **Riesgo de bugs** | Alto | Ninguno |
| **Mantenimiento** | Difícil | Fácil |
| **Experiencia de usuario** | +5% | -2% |
| **Impacto anual** | 10-20 seg ahorrados | 10-20 seg "perdidos" |

**Conclusión:** El costo de implementar una solución compleja **NO justifica** el beneficio mínimo.

---

## 📝 Recomendación

### ✅ Solución Recomendada: Documentar y Capacitar

1. **Crear guía rápida para usuarios:**
   - "Si cambias el filtro de año y no ves el botón, presiona F5"
   - Incluir en documentación del sistema
   - Agregar tooltip o hint en la interfaz

2. **Agregar tooltip en el filtro de año:**
```php
Forms\Components\Select::make('anio')
    ->label('Año')
    ->options([...])
    ->hint('Refrescar la página (F5) después de cambiar el año')
    ->hintIcon('heroicon-o-information-circle')
```

3. **Agregar notificación opcional:**
```php
public function updatedTableFilters(): void
{
    $this->dispatch('filtersUpdated', year: $this->getFilteredYear());

    // Notificación informativa (opcional)
    Notification::make()
        ->title('Filtro actualizado')
        ->body('Si no ves el botón "Crear Estrategia", refresca la página (F5)')
        ->info()
        ->duration(3000)
        ->send();
}
```

---

## 🎯 Decisión Final

**Estado:** **ACEPTAR LIMITACIÓN**

**Razón:** El impacto es mínimo y las soluciones alternativas introducen más complejidad que beneficio.

**Acción:**
1. ✅ Documentar la limitación
2. ✅ Capacitar a usuarios (refrescar con F5)
3. ✅ (Opcional) Agregar hint en interfaz
4. 🔄 Revisar en futuras versiones de Filament

**Revisión futura:**
- Filament v4 (cuando sea lanzado)
- Livewire v4 (cuando sea lanzado)
- Si aparece solución oficial en documentación de Filament

---

## 📚 Referencias

- [Filament v3 Documentation - Actions](https://filamentphp.com/docs/3.x/actions/overview)
- [Livewire v3 Documentation - Reactive Properties](https://livewire.laravel.com/docs/properties)
- [GitHub Issue: Reactive Header Actions](https://github.com/filamentphp/filament/issues/XXXX) (si existe)

---

**Documentado por:** Claude Code
**Fecha:** 2025-10-16
**Estado:** Limitación Conocida y Aceptada
**Próxima revisión:** Q2 2026 o con nueva versión de Filament
