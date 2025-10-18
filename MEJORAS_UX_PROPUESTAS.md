# Propuestas de Mejoras UX para EstrategyResource

## Análisis Actual

Después de revisar el EstrategyResource, identifiqué los siguientes aspectos positivos y áreas de mejora:

### ✅ Fortalezas actuales:
- Sistema de validación de fechas implementado
- Cálculos automáticos de presupuesto en tiempo real
- Control de acceso basado en roles bien implementado
- Workflow de estados claro con acciones contextuales

### ⚠️ Áreas de oportunidad:
1. **Formulario muy largo y complejo** (1427 líneas)
2. **Navegación confusa** en el formulario con muchas secciones anidadas
3. **Falta feedback visual** durante procesos largos
4. **Experiencia móvil/tablet** no optimizada
5. **Sin persistencia de borradores** (pérdida de datos al recargar)
6. **Validaciones solo al guardar** (no en tiempo real)
7. **Sin ayudas contextuales** ni tooltips explicativos
8. **Resúmenes duplicados** de presupuesto

---

## 🎯 PROPUESTAS DE MEJORAS

### 1. **Wizard Multi-Paso** ⭐⭐⭐ ALTA PRIORIDAD

**Problema:** El formulario actual es abrumador con todas las secciones visibles al mismo tiempo.

**Solución:** Dividir en pasos lógicos con navegación clara.

```php
// app/Filament/Resources/EstrategyResource/Pages/CreateEstrategy.php
use Filament\Forms\Components\Wizard;

protected function getSteps(): array
{
    return [
        Wizard\Step::make('Información General')
            ->description('Datos básicos de la estrategia')
            ->icon('heroicon-o-information-circle')
            ->schema([
                // ... campos de información general
            ])
            ->afterValidation(function () {
                Notification::make()
                    ->title('Paso 1 completo')
                    ->success()
                    ->send();
            }),

        Wizard\Step::make('Información Institucional')
            ->description('Misión, visión y objetivos')
            ->icon('heroicon-o-building-office')
            ->schema([
                // ... campos institucionales
            ]),

        Wizard\Step::make('Plan Nacional')
            ->description('Ejes del Plan Nacional de Desarrollo')
            ->icon('heroicon-o-flag')
            ->schema([
                // ... campos de ejes
            ]),

        Wizard\Step::make('Presupuesto')
            ->description('Presupuesto total anual')
            ->icon('heroicon-o-currency-dollar')
            ->schema([
                // ... campo de presupuesto
            ]),

        Wizard\Step::make('Campañas')
            ->description('Campañas y distribución presupuestal')
            ->icon('heroicon-o-megaphone')
            ->schema([
                // ... repeater de campañas
            ]),

        Wizard\Step::make('Revisión')
            ->description('Revisa y envía')
            ->icon('heroicon-o-check-circle')
            ->schema([
                Section::make('Resumen de la Estrategia')
                    ->schema([
                        Placeholder::make('resumen')
                            ->content(fn ($get) => view('filament.estrategy-summary', [
                                'data' => $get('../../')
                            ])),
                    ]),
            ]),
    ];
}
```

**Beneficios:**
- ✅ Reduce carga cognitiva
- ✅ Guía al usuario paso a paso
- ✅ Permite validación incremental
- ✅ Mejor en móviles

---

### 2. **Auto-guardado de Borradores** ⭐⭐⭐ ALTA PRIORIDAD

**Problema:** Si el navegador se cierra o hay un error, se pierde todo el trabajo.

**Solución:** Implementar auto-guardado cada 30 segundos.

```php
// app/Models/EstrategDraft.php (nuevo modelo)
class StrategyDraft extends Model
{
    protected $fillable = ['user_id', 'year', 'draft_data', 'last_saved_at'];
    protected $casts = [
        'draft_data' => 'array',
        'last_saved_at' => 'datetime',
    ];
}

// En CreateEstrategy.php
use Livewire\Attributes\Reactive;

#[Reactive]
public $formData = [];

public function mount(): void
{
    parent::mount();

    // Cargar borrador si existe
    $draft = StrategyDraft::where('user_id', auth()->id())
        ->where('year', $this->getFilteredYear())
        ->latest()
        ->first();

    if ($draft) {
        $this->form->fill($draft->draft_data);

        Notification::make()
            ->title('Borrador recuperado')
            ->body("Última modificación: {$draft->last_saved_at->diffForHumans()}")
            ->info()
            ->persistent()
            ->actions([
                Action::make('eliminar')
                    ->button()
                    ->color('danger')
                    ->action(fn () => $draft->delete()),
            ])
            ->send();
    }
}

// Método para auto-guardar
public function saveDraft(): void
{
    StrategyDraft::updateOrCreate(
        [
            'user_id' => auth()->id(),
            'year' => $this->getFilteredYear(),
        ],
        [
            'draft_data' => $this->form->getState(),
            'last_saved_at' => now(),
        ]
    );
}

// En la vista, agregar Alpine.js para auto-guardar
protected function getFormStatePath(): string
{
    return 'data';
}
```

```blade
{{-- resources/views/filament/pages/create-estrategy.blade.php --}}
<div x-data="{
    lastSaved: null,
    autoSave() {
        $wire.saveDraft()
        this.lastSaved = new Date()
    }
}"
x-init="setInterval(() => autoSave(), 30000)">

    <div class="text-sm text-gray-500 mb-4" x-show="lastSaved">
        💾 Guardado automáticamente <span x-text="moment(lastSaved).fromNow()"></span>
    </div>

    {{ $this->form }}
</div>
```

**Beneficios:**
- ✅ Previene pérdida de datos
- ✅ Tranquilidad para el usuario
- ✅ Permite continuar más tarde

---

### 3. **Validaciones en Tiempo Real** ⭐⭐ MEDIA PRIORIDAD

**Problema:** El usuario solo descubre errores al intentar guardar.

**Solución:** Validación inmediata con feedback visual.

```php
// En el campo de presupuesto
Forms\Components\TextInput::make('presupuesto')
    ->label('Presupuesto Total Anual')
    ->numeric()
    ->prefix('$')
    ->reactive()
    ->required()
    ->minValue(1)
    ->maxValue(999999999)
    ->helperText('Cifras en miles de pesos')
    ->live(onBlur: true)
    ->afterStateUpdated(function ($state, $set, $get) {
        // Validar en tiempo real
        if ($state && $state < 100000) {
            Notification::make()
                ->warning()
                ->title('Presupuesto bajo')
                ->body('El presupuesto parece bajo. ¿Es correcto?')
                ->send();
        }
    })
    ->suffixIcon('heroicon-o-information-circle')
    ->suffixAction(
        Action::make('info')
            ->icon('heroicon-o-question-mark-circle')
            ->tooltip('Ingresa el presupuesto total en miles de pesos. Ejemplo: 1,000,000 = $1,000')
    ),
```

**Para campos de campaña:**

```php
Forms\Components\TextInput::make('name')
    ->label('Nombre de la Campaña')
    ->required()
    ->minLength(10)
    ->maxLength(200)
    ->live(debounce: 500)
    ->afterStateUpdated(function ($state, $set) {
        // Sugerir formato si no cumple
        if ($state && strlen($state) < 10) {
            $set('name_hint', '⚠️ El nombre debe ser más descriptivo (mínimo 10 caracteres)');
        } else {
            $set('name_hint', null);
        }
    })
    ->helperText(fn ($get) => $get('name_hint')),
```

**Beneficios:**
- ✅ Feedback inmediato
- ✅ Reduce errores
- ✅ Mejor UX

---

### 4. **Tooltips y Ayuda Contextual** ⭐⭐ MEDIA PRIORIDAD

**Problema:** Los usuarios no entienden qué significa cada campo.

**Solución:** Agregar tooltips explicativos.

```php
use Filament\Support\Enums\IconPosition;

Forms\Components\Textarea::make('mision')
    ->label('Misión Institucional')
    ->required()
    ->maxLength(65535)
    ->rows(4)
    ->hint('¿Qué hace tu institución?')
    ->hintIcon('heroicon-o-question-mark-circle', IconPosition::Before)
    ->hintColor('info')
    ->helperText('Describe la razón de ser de tu institución, su propósito fundamental y a quién sirve.')
    ->placeholder('Ejemplo: Garantizar el acceso universal a servicios de salud de calidad...')
    ->extraInputAttributes([
        'title' => 'La misión debe ser clara, concisa y enfocada en el propósito central de la institución',
    ]),

// Para los Ejes del Plan Nacional
Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_1_gobernanza')
    ->label('Eje General 1: Gobernanza con justicia y participación ciudadana')
    ->hint('¿Tu estrategia incluye este eje?')
    ->hintIcon('heroicon-o-information-circle')
    ->helperText('Selecciona si tu estrategia contribuye a fortalecer la gobernanza democrática y la participación ciudadana.')
    ->columnSpan(2),
```

**Vista personalizada para ayuda:**

```blade
{{-- resources/views/components/field-help.blade.php --}}
<div class="rounded-md bg-blue-50 p-4 my-2">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm text-blue-700">
                {{ $slot }}
            </p>
        </div>
    </div>
</div>
```

**Beneficios:**
- ✅ Reduce confusión
- ✅ Menos errores de llenado
- ✅ Acelera el proceso

---

### 5. **Progress Tracker Visual** ⭐⭐ MEDIA PRIORIDAD

**Problema:** El usuario no sabe cuánto le falta por completar.

**Solución:** Indicador de progreso global.

```php
// En ListEstrategies, agregar widget de progreso
class StrategyProgressWidget extends Widget
{
    protected static string $view = 'filament.widgets.strategy-progress';

    public function getViewData(): array
    {
        $user = Auth::user();
        $year = request()->get('tableFilters.anio.anio', now()->year);

        $strategy = Estrategy::where('institution_id', $user->institution_id)
            ->where('anio', $year)
            ->latest()
            ->first();

        if (!$strategy) {
            return ['progress' => 0, 'status' => 'not_started'];
        }

        // Calcular progreso basado en campos completados
        $totalFields = 10; // Número de campos críticos
        $completedFields = 0;

        if ($strategy->mision) $completedFields++;
        if ($strategy->vision) $completedFields++;
        if ($strategy->objetivo_institucional) $completedFields++;
        if ($strategy->objetivo_estrategia) $completedFields++;
        if ($strategy->presupuesto) $completedFields++;
        if ($strategy->ejes_plan_nacional) $completedFields++;
        if ($strategy->campaigns()->count() > 0) $completedFields++;

        $progress = ($completedFields / $totalFields) * 100;

        return [
            'progress' => round($progress),
            'status' => $strategy->estado_estrategia,
            'strategy' => $strategy,
        ];
    }
}
```

```blade
{{-- resources/views/filament/widgets/strategy-progress.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Progreso de tu Estrategia {{ $this->getViewData()['strategy']->anio ?? now()->year }}</h3>
                <span class="text-2xl font-bold text-primary-600">
                    {{ $this->getViewData()['progress'] }}%
                </span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-primary-600 h-4 rounded-full transition-all duration-500"
                     style="width: {{ $this->getViewData()['progress'] }}%"></div>
            </div>

            @if($this->getViewData()['progress'] < 100)
                <p class="text-sm text-gray-600">
                    Completa todos los campos para poder enviar tu estrategia
                </p>
            @else
                <p class="text-sm text-green-600 font-medium">
                    ✅ Tu estrategia está completa y lista para enviar
                </p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
```

**Beneficios:**
- ✅ Motivación visual
- ✅ Claridad del progreso
- ✅ Gamificación sutil

---

### 6. **Duplicación Inteligente de Estrategias** ⭐⭐ MEDIA PRIORIDAD

**Problema:** Al crear estrategia del año siguiente, empiezan de cero.

**Solución:** Ofrecer copiar del año anterior.

```php
// En CreateEstrategy.php
protected function getHeaderActions(): array
{
    return [
        Action::make('copiarAnioAnterior')
            ->label('Copiar del Año Anterior')
            ->icon('heroicon-o-document-duplicate')
            ->color('info')
            ->visible(fn () => $this->canCopyFromPreviousYear())
            ->action(function () {
                $previousYear = $this->getFilteredYear() - 1;
                $previousStrategy = Estrategy::where('institution_id', auth()->user()->institution_id)
                    ->where('anio', $previousYear)
                    ->where('estado_estrategia', 'Autorizada')
                    ->latest()
                    ->first();

                if (!$previousStrategy) {
                    Notification::make()
                        ->warning()
                        ->title('No hay estrategia del año anterior')
                        ->body("No se encontró una estrategia autorizada para {$previousYear}")
                        ->send();
                    return;
                }

                // Copiar datos
                $this->form->fill([
                    'mision' => $previousStrategy->mision,
                    'vision' => $previousStrategy->vision,
                    'objetivo_institucional' => $previousStrategy->objetivo_institucional,
                    // NO copiar objetivo_estrategia ni presupuesto
                    'ejes_plan_nacional' => $previousStrategy->ejes_plan_nacional,
                ]);

                Notification::make()
                    ->success()
                    ->title('Datos copiados')
                    ->body("Se han copiado los datos de la estrategia {$previousYear}")
                    ->send();
            })
            ->requiresConfirmation()
            ->modalHeading('Copiar Estrategia Anterior')
            ->modalDescription('Se copiarán misión, visión, objetivo institucional y ejes del Plan Nacional. Los demás campos deberás actualizarlos.')
            ->modalSubmitActionLabel('Sí, copiar')
            ->modalCancelActionLabel('Cancelar'),
    ];
}

protected function canCopyFromPreviousYear(): bool
{
    $previousYear = $this->getFilteredYear() - 1;
    return Estrategy::where('institution_id', auth()->user()->institution_id)
        ->where('anio', $previousYear)
        ->where('estado_estrategia', 'Autorizada')
        ->exists();
}
```

**Beneficios:**
- ✅ Ahorra tiempo
- ✅ Consistencia año a año
- ✅ Menos errores

---

### 7. **Exportación de Estrategias** ⭐ BAJA PRIORIDAD

**Problema:** No hay forma de exportar/imprimir la estrategia.

**Solución:** Botones de exportación PDF/Excel.

```php
// En ViewEstrategy.php
protected function getHeaderActions(): array
{
    return [
        Action::make('exportarPDF')
            ->label('Exportar PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('danger')
            ->action(function () {
                return response()->streamDownload(function () {
                    echo Pdf::loadView('pdf.estrategy', ['estrategy' => $this->record])
                        ->output();
                }, "estrategia-{$this->record->anio}-{$this->record->institution->name}.pdf");
            }),

        Action::make('exportarExcel')
            ->label('Exportar Excel')
            ->icon('heroicon-o-table-cells')
            ->color('success')
            ->action(function () {
                return Excel::download(
                    new StrategyExport($this->record),
                    "estrategia-{$this->record->anio}.xlsx"
                );
            }),
    ];
}
```

---

### 8. **Comparador de Versiones** ⭐ BAJA PRIORIDAD

**Problema:** No se puede ver qué cambió entre modificaciones.

**Solución:** Vista de comparación lado a lado.

```php
// Acción en la tabla
Tables\Actions\Action::make('compararVersiones')
    ->label('Comparar con Anterior')
    ->icon('heroicon-o-arrows-right-left')
    ->visible(fn ($record) => $record->estrategia_original_id !== null)
    ->modalContent(fn ($record) => view('filament.modals.compare-strategies', [
        'original' => $record->estrategiaOriginal,
        'modified' => $record,
    ]))
    ->modalWidth('7xl'),
```

---

## 🎨 MEJORAS VISUALES RÁPIDAS

### 9. **Badges con Colores por Estado**

```php
Tables\Columns\TextColumn::make('estado_estrategia')
    ->label('Estado')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'Creada' => 'gray',
        'Enviado a CS' => 'info',
        'Aceptada CS' => 'success',
        'Rechazada CS' => 'danger',
        'Enviada a DGNC' => 'warning',
        'Autorizada' => 'success',
        'Rechazada DGNC' => 'danger',
        'Observada DGNC' => 'warning',
        default => 'gray',
    })
    ->icon(fn (string $state): string => match ($state) {
        'Creada' => 'heroicon-o-pencil',
        'Autorizada' => 'heroicon-o-check-badge',
        'Rechazada CS', 'Rechazada DGNC' => 'heroicon-o-x-circle',
        default => 'heroicon-o-clock',
    }),
```

### 10. **Iconos en Secciones**

```php
Forms\Components\Section::make('Información General')
    ->description('Datos básicos de identificación')
    ->icon('heroicon-o-information-circle')
    ->collapsible()
    ->schema([...]),

Forms\Components\Section::make('Presupuesto Anual')
    ->description('Monto total asignado')
    ->icon('heroicon-o-currency-dollar')
    ->collapsed()
    ->schema([...]),
```

---

## ⚡ PRIORIZACIÓN RECOMENDADA

### Fase 1 (Inmediato - 1 semana):
1. ✅ Tooltips y ayuda contextual
2. ✅ Validaciones en tiempo real básicas
3. ✅ Badges con colores por estado
4. ✅ Iconos en secciones

### Fase 2 (Corto plazo - 2-3 semanas):
5. ✅ Auto-guardado de borradores
6. ✅ Progress tracker visual
7. ✅ Duplicación de año anterior

### Fase 3 (Mediano plazo - 1-2 meses):
8. ✅ Wizard multi-paso
9. ✅ Exportación PDF/Excel
10. ✅ Comparador de versiones

---

## 📊 IMPACTO ESPERADO

| Mejora | Reducción tiempo | Satisfacción usuario | Dificultad |
|--------|------------------|---------------------|------------|
| Wizard | 30% | ⭐⭐⭐⭐⭐ | Media |
| Auto-guardado | 5% | ⭐⭐⭐⭐⭐ | Baja |
| Validación real-time | 20% | ⭐⭐⭐⭐ | Baja |
| Tooltips | 15% | ⭐⭐⭐⭐ | Muy baja |
| Progress tracker | 5% | ⭐⭐⭐ | Baja |
| Copiar año anterior | 40% | ⭐⭐⭐⭐⭐ | Baja |

---

## 🛠️ ¿Quieres que implemente alguna?

Puedo implementar cualquiera de estas mejoras. Las más fáciles de implementar son:
1. **Tooltips y ayuda contextual** (30 minutos)
2. **Validaciones en tiempo real** (1 hora)
3. **Copiar del año anterior** (1 hora)
4. **Auto-guardado de borradores** (2 horas)

¿Cuál te gustaría que empiece a implementar?
