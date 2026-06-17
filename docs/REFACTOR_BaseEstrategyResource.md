# Refactorización de BaseEstrategyResource en Traits

## Contexto

`BaseEstrategyResource.php` tiene ~2,514 líneas, lo que representa ~16% del proyecto completo.
Es el archivo más grande y concentra demasiadas responsabilidades: formulario, tabla, acciones, cálculos de presupuesto y filtros por rol.

**Sin tocar:** `ComunicacionSocialResource`, `PromocionPublicidadResource`, páginas Create/Edit/Modificar/Solventar/Cancelar, ni lógica de negocio. Solo reorganización.

---

## Estructura propuesta

### `BaseEstrategyResource.php` (~100 líneas)
Solo propiedades estáticas, `shouldRegisterNavigation()`, `canCreate()` y los `use` de cada trait.

```php
class BaseEstrategyResource extends Resource
{
    use HasEstrategyForm;
    use HasEstrategyTable;
    use HasEstrategyActions;
    use HasBudgetCalculations;
    // ...
}
```

---

### `HasEstrategyForm` (~800 líneas)
**Archivo:** `app/Filament/Resources/Base/Concerns/HasEstrategyForm.php`

Contiene:
- Método `form(Form $form)`
- Wizard con todos los Steps
- Campos de Información General
- Repeaters de campañas
- Helper `createDecimalField()`

---

### `HasEstrategyTable` (~400 líneas)
**Archivo:** `app/Filament/Resources/Base/Concerns/HasEstrategyTable.php`

Contiene:
- Método `table(Table $table)`
- Definición de columnas
- Filtros de año
- `modifyQueryUsing()` con filtrado por rol/sector/institución

---

### `HasEstrategyActions` (~900 líneas)
**Archivo:** `app/Filament/Resources/Base/Concerns/HasEstrategyActions.php`

Contiene todas las `Tables\Actions`:
- `enviar_cs`
- `evaluar_estrategia`
- `enviar_dgnc`
- `autorizar_dgnc`
- `rechazar_dgnc`
- `observar_dgnc`
- `editar_campos_criticos`
- Acciones de PDF

---

### `HasBudgetCalculations` (~200 líneas)
**Archivo:** `app/Filament/Resources/Base/Concerns/HasBudgetCalculations.php`

Contiene:
- Cálculos de presupuesto por campaña
- Placeholders de resumen de medios
- Resumen global y porcentaje de utilización

---

## Beneficio

| Antes | Después |
|-------|---------|
| 1 archivo de 2,514 líneas | 5 archivos de ~100-900 líneas |
| Buscar una acción = navegar 2,500 líneas | Abrir `HasEstrategyActions.php` directamente |
| Riesgo de conflictos en PR | Cambios aislados por responsabilidad |

Comportamiento **idéntico** al actual. PHP resuelve los traits en compilación, Filament no nota diferencia.

---

## Notas para la implementación

- Crear carpeta `app/Filament/Resources/Base/Concerns/`
- Cada trait en su propio namespace: `App\Filament\Resources\Base\Concerns`
- Los traits pueden necesitar los mismos `use` (imports) que tiene el archivo actual — copiarlos a cada trait según lo que usen
- Orden sugerido: primero `HasBudgetCalculations` (sin dependencias), luego `HasEstrategyForm`, `HasEstrategyTable`, `HasEstrategyActions`
