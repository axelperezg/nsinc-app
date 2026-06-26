<?php

namespace App\Filament\Resources\Base;

use App\Filament\Resources\EstrategyResource\Actions\CargarOficioDgncAction;
use App\Filament\Resources\EstrategyResource\Actions\VerOficiosDgncAction;
use App\Models\Estrategy;
use App\Models\Campaign;
use App\Models\Version;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

abstract class BaseEstrategyResource extends Resource
{
    protected static ?string $model = Estrategy::class;

    /**
     * Partida presupuestal: '36101' para Comunicación Social, '36201' para Promoción y Publicidad
     * Las clases hijas deben definir este valor
     */
    protected static string $partidaPresupuestal;

    /**
     * Título para el PDF (será definido por las clases hijas)
     */
    protected static string $tituloPDF;

    /**
     * Helper para crear campos numéricos con formato de decimales
     * Guarda hasta 6 decimales en BD, muestra 2 decimales en interfaz
     */
    private static function createDecimalField(string $name, string $label, array $additionalConfig = []): Forms\Components\TextInput
    {
        $field = Forms\Components\TextInput::make($name)
            ->label($label)
            ->numeric()
            ->step(0.000001) // Permitir hasta 6 decimales
            ->prefix('$')
            ->live(onBlur: true)
            ->formatStateUsing(fn ($state) => $state ? number_format($state, 2, '.', '') : '')
            ->dehydrateStateUsing(fn ($state) => $state ? round((float)$state, 6) : null);

        // Aplicar configuración adicional
        if (isset($additionalConfig['afterStateUpdated'])) {
            $field->afterStateUpdated($additionalConfig['afterStateUpdated']);
        }

        return $field;
    }

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Estrategias Comunicación Social';

    protected static ?string $modelLabel = 'Estrategia';

    protected static ?string $pluralModelLabel = 'Estrategias';

    protected static ?string $navigationGroup = 'Gestión de Estrategias';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super admin, usuarios de institución, coordinadores de sector y usuarios DGNC pueden ver Estrategias
        return $user->role && in_array($user->role->name, [
            'super_admin',
            'institution_user',
            'sector_coordinator',
            'dgnc_user'
        ]);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        // El rol consulta no puede crear estrategias
        return $user && !$user->isConsulta();
    }



    public static function form(Form $form): Form
    {
        $partida = static::$partidaPresupuestal;

        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Información General')
                        ->description('Datos básicos de la estrategia')
                        ->icon('heroicon-o-document-text')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                        Forms\Components\Section::make('Información General')
                            ->description('Datos básicos de la estrategia (generados automáticamente)')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                        Forms\Components\TextInput::make('anio')
                            ->label('Año')
                            ->disabled()
                            ->default(function () {
                                // Tomar el año del filtro activo o año actual
                                return request()->get('year', now()->year);
                            }),
                        Forms\Components\TextInput::make('concepto')
                            ->label('Solicitud')
                            ->disabled()
                            ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                            ->default(function () {
                                // Verificar si ya existe una estrategia para este año e institución
                                $anio = request()->get('year', now()->year);
                                $user = Auth::user();
                                
                                if ($user && $user->institution_id) {
                                    $estrategiaExistente = \App\Models\Estrategy::where('anio', $anio)
                                        ->where('institution_id', $user->institution_id)
                                        ->first();
                                    
                                    if ($estrategiaExistente) {
                                        return $estrategiaExistente->concepto;
                                    }
                                }
                                
                                return 'Registro';
                            }), 
                        Forms\Components\TextInput::make('estado_estrategia')
                            ->label('Estado de la Estrategia')
                            ->disabled()
                            ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                            ->default(function () {
                                // Verificar si ya existe una estrategia para este año e institución
                                $anio = request()->get('year', now()->year);
                                $user = Auth::user();
                                
                                if ($user && $user->institution_id) {
                                    $estrategiaExistente = \App\Models\Estrategy::where('anio', $anio)
                                        ->where('institution_id', $user->institution_id)
                                        ->first();
                                    
                                    if ($estrategiaExistente) {
                                        return $estrategiaExistente->estado_estrategia;
                                    }
                                }
                                
                                return 'Creada';
                            }),
                        Forms\Components\Hidden::make('juridical_nature_id')
                            ->default(function () {
                                $user = Auth::user();
                                if ($user && $user->institution_id) {
                                    $institution = \App\Models\Institution::find($user->institution_id);
                                    return $institution ? $institution->juridical_nature_id : null;
                                }
                                return null;
                            }),
                         Forms\Components\TextInput::make('juridical_nature_name')
                            ->label('Naturaleza Jurídica')
                            ->disabled()
                            ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                            ->default(function () {
                                $user = Auth::user();
                                if ($user && $user->institution_id) {
                                    $institution = \App\Models\Institution::with('juridicalNature')->find($user->institution_id);
                                    if ($institution && $institution->juridicalNature) {
                                        return $institution->juridicalNature->name;
                                    }
                                }
                                return 'No disponible';
                            }),
                        
                        Forms\Components\DatePicker::make('fecha_elaboracion')
                            ->label('Fecha de Elaboración')
                            ->disabled()
                            ->default(now())
                            ->hidden()
                            ->dehydrated(), // Esto asegura que el campo se incluya en el envío aunque esté deshabilitado
                       
                        
                        Forms\Components\TextInput::make('oficio_dgnc')
                            ->label('Oficio DGNC')
                            ->maxLength(255)
                            ->disabled(function () {
                                // Solo usuarios con rol dgnc_user pueden editar este campo
                                $user = Auth::user();
                                return !($user && $user->role && $user->role->name === 'dgnc_user');
                            })
                            ->dehydrated()
                            ->hidden(), // Incluir en el envío aunque esté deshabilitado
                        Forms\Components\Hidden::make('institution_id')
                            ->default(function () {
                                $user = Auth::user();
                                return $user ? $user->institution_id : null;
                            }),
                        Forms\Components\TextInput::make('institution_name')
                            ->label('Institución')
                            ->disabled()
                            ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                            ->default(function () {
                                $user = Auth::user();
                                if ($user && $user->institution_id) {
                                    $institution = \App\Models\Institution::find($user->institution_id);
                                    if ($institution) {
                                        return $institution->name;
                                    }
                                }
                                return 'No disponible';
                            })->columnSpan(2),
                        Forms\Components\DatePicker::make('fecha_envio_dgnc')
                            ->label('Fecha de Envío DGNC')
                            ->disabled()
                            ->hidden()
                            ->visible(function () {
                                // Solo mostrar si el estado es 'Enviada a DGNC' o posterior
                                $anio = request()->get('year', now()->year);
                                $user = Auth::user();
                                
                                if ($user && $user->institution_id) {
                                    $estrategiaExistente = \App\Models\Estrategy::where('anio', $anio)
                                        ->where('institution_id', $user->institution_id)
                                        ->first();
                                    
                                    if ($estrategiaExistente) {
                                        return in_array($estrategiaExistente->estado_estrategia, [
                                            'Enviada a DGNC', 
                                            'Autorizada', 
                                            'Rechazada DGNC', 
                                            'Observada DGNC'
                                        ]);
                                    }
                                }
                                
                                return false;
                            })
                            ->default(function () {
                                $anio = request()->get('year', now()->year);
                                $user = Auth::user();
                                
                                if ($user && $user->institution_id) {
                                    $estrategiaExistente = \App\Models\Estrategy::where('anio', $anio)
                                        ->where('institution_id', $user->institution_id)
                                        ->first();
                                    
                                    if ($estrategiaExistente && $estrategiaExistente->fecha_envio_dgnc) {
                                        return $estrategiaExistente->fecha_envio_dgnc;
                                    }
                                }
                                
                                return null;
                            }),
                    ])
                    ->columns(3),

                            // Campos ocultos para guardar los IDs correctos
                            Forms\Components\Hidden::make('anio')
                                ->default(function () {
                                    return request()->get('year', now()->year);
                                }),
                            Forms\Components\Hidden::make('institution_id')
                                ->default(function () {
                                    $user = Auth::user();
                                    return $user && $user->institution_id ? $user->institution_id : null;
                                }),
                            Forms\Components\Hidden::make('juridical_nature_id')
                                ->default(function () {
                                    $user = Auth::user();
                                    if ($user && $user->institution_id) {
                                        $institution = \App\Models\Institution::find($user->institution_id);
                                        if ($institution && $institution->juridical_nature_id) {
                                            return $institution->juridical_nature_id;
                                        }
                                    }
                                    return null;
                                }),
                            Forms\Components\Hidden::make('estado_estrategia')
                                ->default(function () {
                                    $anio = request()->get('year', now()->year);
                                    $user = Auth::user();
                                    
                                    if ($user && $user->institution_id) {
                                        $estrategiaExistente = \App\Models\Estrategy::where('anio', $anio)
                                            ->where('institution_id', $user->institution_id)
                                            ->first();
                                        
                                        if ($estrategiaExistente) {
                                            return $estrategiaExistente->estado_estrategia;
                                        }
                                    }
                                    
                                    return 'Creada';
                                }),
                            Forms\Components\Hidden::make('concepto')
                                ->default(function () {
                                    $anio = request()->get('year', now()->year);
                                    $user = Auth::user();
                                    
                                    if ($user && $user->institution_id) {
                                        $estrategiaExistente = \App\Models\Estrategy::where('anio', $anio)
                                            ->where('institution_id', $user->institution_id)
                                            ->first();
                                        
                                        if ($estrategiaExistente) {
                                            return $estrategiaExistente->concepto;
                                        }
                                    }
                                    
                                    return 'Registro';
                                }),
                           
                        ]),

                    Wizard\Step::make('Información Institucional')
                        ->description('Misión, visión y objetivos')
                        ->icon('heroicon-o-building-office-2')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                    Forms\Components\Section::make('Información Institucional')
                    ->description('Describe la misión, visión y objetivos de tu institución')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Textarea::make('mision')
                            ->label('Misión')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->hint('¿Qué hace tu institución?')
                            ->hintIcon('heroicon-o-question-mark-circle')
                            ->hintColor('info')
                            ->helperText('Introduce la misión oficial actualizada de la institución. Debe ser la misma que la que se encuentra en la página oficial de la institución.')
                            ->placeholder('Ejemplo: Garantizar el acceso universal a servicios de salud de calidad...'),
                        Forms\Components\Textarea::make('vision')
                            ->label('Visión')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->hint('¿Hacia dónde va tu institución?')
                            ->hintIcon('heroicon-o-question-mark-circle')
                            ->hintColor('info')
                            ->helperText('Introduce la visión oficial actualizadade la institución. Debe ser la misma que la que se encuentra en la página oficial de la institución')
                            ->placeholder('Ejemplo: Ser reconocida como líder en servicios de salud innovadores...'),
                        Forms\Components\Textarea::make('objetivo_institucional')
                            ->label('Objetivo Institucional')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->hint('¿Qué busca lograr tu institución?')
                            ->hintIcon('heroicon-o-question-mark-circle')
                            ->hintColor('info')
                            ->helperText('Define los objetivos estratégicos generales que persigue tu institución.')
                            ->placeholder('Ejemplo: Mejorar la calidad de atención médica en un 30%...'),
                        Forms\Components\Textarea::make('objetivo_estrategia')
                            ->label('Objetivo de la Estrategia')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->hint('¿Qué quieres lograr con esta estrategia de comunicación?')
                            ->hintIcon('heroicon-o-question-mark-circle')
                            ->hintColor('info')
                            ->helperText('Especifica los objetivos de comunicación que buscas alcanzar este año.')
                            ->placeholder('Ejemplo: Informar a la población sobre nuevos programas de prevención...'),
                    ])
                    ->columns(2),
                        ]),

                    // Step específico por partida (PND para 36101, Entorno/Metas para 36201)
                    static::getEspecificosWizardStep(),

                    Wizard\Step::make('Presupuesto Anual')
                        ->description('Define el presupuesto total')
                        ->icon('heroicon-o-currency-dollar')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Section::make('Presupuesto Anual')
                    ->description('Define el presupuesto total para tu estrategia de comunicación')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([

                    Forms\Components\TextInput::make('presupuesto')
                        ->label('Presupuesto Total Anual')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('$')
                        ->reactive()
                        ->required()
                        ->minValue(1)
                        ->maxValue(999999999)
                        ->hint('Ingresa el monto total')
                        ->hintIcon('heroicon-o-question-mark-circle')
                        ->hintColor('info')
                        ->helperText('Cifras en miles de pesos. Ejemplo: 1,000,000 = $1,000. Este presupuesto se distribuirá entre todas las campañas.')
                        ->placeholder('Ejemplo: 1000')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set, Forms\Set $setForm) {
                            // Validación en tiempo real del presupuesto
                            if ($state) {
                                $value = floatval($state);

                                // Advertencia si el presupuesto es muy bajo
                                if ($value > 0 && $value < 100000) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Presupuesto bajo')
                                        ->body('El presupuesto ingresado ($' . number_format($value, 2) . ') es correcto? Si es correcto, continúa con el proceso.')
                                        ->duration(5000)
                                        ->send();
                                }

                                // Advertencia si el presupuesto es muy alto
                                if ($value > 500000000) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Presupuesto muy alto')
                                        ->body('El presupuesto ingresado ($' . number_format($value, 2) . ') es correcto? Si es correcto, continúa con el proceso.')
                                        ->duration(5000)
                                        ->send();
                                }
                            }
                        })
                        ->suffixAction(
                            \Filament\Forms\Components\Actions\Action::make('info_presupuesto')
                                ->icon('heroicon-o-information-circle')
                                ->color('info')
                                ->tooltip('El presupuesto debe incluir todos los gastos de medios, producción y estudios para el año completo.')
                        ),
                    ])
                    ->columns(3),
                        ]),

                    Wizard\Step::make('Campañas')
                        ->description('Agrega tus campañas')
                        ->icon('heroicon-o-megaphone')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Section::make('Campañas')
                    ->description('Agrega las campañas que ejecutarás durante el año')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Forms\Components\Repeater::make('campaigns')
                            ->label('Programa: Campaña adicional')
                            ->relationship('campaigns')
                            ->schema([
                                Forms\Components\Section::make('Información General')
                                    ->description('Datos básicos de la campaña')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre de la Campaña')
                                            ->required()
                                            ->maxLength(200)
                                            ->hint('Nombre claro y descriptivo')
                                            ->hintIcon('heroicon-o-question-mark-circle')
                                            ->hintColor('info')
                                            ->helperText('Ingresa un nombre descriptivo que identifique claramente la campaña (mínimo 10 caracteres).')
                                            ->placeholder('Ejemplo: Campaña de Prevención de Enfermedades Respiratorias 2026')
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                if ($state) {
                                                    $length = strlen($state);

                                                    
                                                    // Validar que el nombre de la campaña no coincida con ninguna versión
                                                    $versions = $get('../versions') ?? [];
                                                    if (is_array($versions)) {
                                                        foreach ($versions as $version) {
                                                            if (isset($version['name']) && trim(strtolower($version['name'])) === trim(strtolower($state))) {
                                                                Notification::make()
                                                                    ->danger()
                                                                    ->title('Nombre duplicado')
                                                                    ->body('El nombre de la campaña no puede ser igual al nombre de ninguna de sus versiones. Por favor, cambia el nombre de la campaña o de la versión.')
                                                                    ->duration(5000)
                                                                    ->send();
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            })
                                            ->rule(function ($get) {
                                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                    if (!$value) return;

                                                    // Validar que el nombre de la campaña no coincida con ninguna versión
                                                    $versions = $get('../versions') ?? [];
                                                    if (is_array($versions)) {
                                                        foreach ($versions as $version) {
                                                            if (isset($version['name']) && trim(strtolower($version['name'])) === trim(strtolower($value))) {
                                                                $fail('El nombre de la campaña no puede ser igual al nombre de ninguna de sus versiones. Por favor, cambia el nombre de la campaña o de la versión.');
                                                                return;
                                                            }
                                                        }
                                                    }
                                                };
                                            }),
                                        Forms\Components\Select::make('campaign_type_id')
                                            ->label('Tipo de Campaña')
                                            ->relationship('campaignType', 'name')
                                            ->required($partida === '36101')
                                            ->visible($partida === '36101')
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn ($state) => $partida === '36101' ? $state : null)
                                            ->hint('Selecciona el tipo')
                                            ->hintIcon('heroicon-o-question-mark-circle')
                                            ->hintColor('info')
                                            ->helperText('Elige el tipo de campaña según su naturaleza y objetivos.')
                                            ->preload(),
                                        Forms\Components\TextInput::make('meta_campana')
                                            ->label('Meta de Campaña')
                                            ->required($partida === '36201')
                                            ->visible($partida === '36201')
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn ($state) => $partida === '36201' ? ($state ?? '') : 'No Aplica')
                                            ->maxLength(500)
                                            ->hint('Meta específica de la campaña')
                                            ->hintIcon('heroicon-o-question-mark-circle')
                                            ->hintColor('info')
                                            ->helperText('Especifica la meta cuantificable de esta campaña de promoción y publicidad.')
                                            ->placeholder('Ejemplo: Incrementar ventas en 20% durante el primer trimestre...'),
                                        Forms\Components\Textarea::make('temaEspecifico')
                                            ->label('Tema Específico')
                                            ->required()
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->hint('¿De qué trata la campaña?')
                                            ->hintIcon('heroicon-o-question-mark-circle')
                                            ->hintColor('info')
                                            ->helperText('Describe el tema central que abordará la campaña de manera específica y concreta.')
                                            ->placeholder('Ejemplo: Promoción de vacunación contra influenza en población vulnerable...'),
                                        Forms\Components\Textarea::make('objetivoComunicacion')
                                            ->label('Objetivo de Comunicación')
                                            ->required()
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->hint('¿Qué quieres lograr?')
                                            ->hintIcon('heroicon-o-question-mark-circle')
                                            ->hintColor('info')
                                            ->helperText('Define qué deseas que la audiencia conozca, sienta o haga después de ver la campaña.')
                                            ->placeholder('Ejemplo: Incrementar en 40% la vacunación en adultos mayores...'),

                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Versiones')
                                    ->description('Define las versiones de tu campaña y sus periodos de difusión')
                                    ->icon('heroicon-o-calendar-days')
                                    ->schema([
                                        Forms\Components\Repeater::make('versions')
                                            ->label('Información de la Versión ó Versiones de la Campaña')
                                            ->relationship('versions')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nombre de la Versión')
                                                    ->required()
                                                    ->hint('Identifica esta versión')
                                                    ->hintIcon('heroicon-o-question-mark-circle')
                                                    ->helperText('Por ejemplo: "Versión 1 - Lanzamiento", "Versión 2 - Refuerzo"')
                                                    ->placeholder('Ejemplo: Versión 1 - Primavera')
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        if ($state) {
                                                            // Validar que el nombre de la versión no coincida con el nombre de la campaña
                                                            $campaignName = $get('../../name') ?? '';
                                                            if ($campaignName && trim(strtolower($campaignName)) === trim(strtolower($state))) {
                                                                Notification::make()
                                                                    ->danger()
                                                                    ->title('Nombre duplicado')
                                                                    ->body('El nombre de la versión no puede ser igual al nombre de la campaña. Por favor, cambia el nombre de la versión o el nombre de la campaña.')
                                                                    ->duration(5000)
                                                                    ->send();
                                                            }
                                                        }
                                                    })
                                                    ->rule(function ($get) {
                                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                            if (!$value) return;

                                                            // Validar que el nombre de la versión no coincida con el nombre de la campaña
                                                            $campaignName = $get('../../name') ?? '';
                                                            if ($campaignName && trim(strtolower($campaignName)) === trim(strtolower($value))) {
                                                                $fail('El nombre de la versión no puede ser igual al nombre de la campaña. Por favor, cambia el nombre de la versión o el nombre de la campaña.');
                                                                return;
                                                            }
                                                        };
                                                    }),
                                                Forms\Components\DatePicker::make('fechaInicio')
                                                    ->label('Fecha de Inicio')
                                                    ->required()
                                                    ->hint('Inicio de difusión')
                                                    ->hintIcon('heroicon-o-calendar')
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        // Validar que no sea en el pasado
                                                        if ($state) {
                                                            $fechaInicio = \Carbon\Carbon::parse($state);
                                                            $hoy = \Carbon\Carbon::today();

                                                            if ($fechaInicio->lt($hoy)) {
                                                                Notification::make()
                                                                    ->warning()
                                                                    ->title('Fecha inicio en el pasado')
                                                                    ->body('La fecha de inicio se ha registrado. Verifica que es correcta.')
                                                                    ->duration(4000)
                                                                    ->send();
                                                            }

                                                            // Limpiar fecha final si es anterior a la nueva fecha inicio
                                                            $fechaFinal = $get('fechaFinal');
                                                            if ($fechaFinal && \Carbon\Carbon::parse($fechaFinal)->lte($fechaInicio)) {
                                                                $set('fechaFinal', null);
                                                                Notification::make()
                                                                    ->info()
                                                                    ->title('Fecha final ajustada')
                                                                    ->body('La fecha final se limpió porque debe ser posterior a la fecha de inicio.')
                                                                    ->duration(3000)
                                                                    ->send();
                                                            }
                                                        }
                                                    })
                                                    ->rule(function ($get) {
                                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                            if (!$value) return;

                                                            $anio = $get('../../anio');
                                                            if (!$anio) return;

                                                            $fechaInicio = \Carbon\Carbon::parse($value);
                                                            $anioFecha = $fechaInicio->year;

                                                            if ($anioFecha != $anio) {
                                                                $fail("La fecha de inicio debe estar dentro del año {$anio} de la estrategia.");
                                                            }
                                                        };
                                                    }),
                                                Forms\Components\DatePicker::make('fechaFinal')
                                                    ->label('Fecha Final')
                                                    ->required()
                                                    ->hint('Fin de difusión')
                                                    ->hintIcon('heroicon-o-calendar')
                                                    ->native(false)
                                                    ->after('fechaInicio')
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, $get) {
                                                        if ($state) {
                                                            $fechaFinal = \Carbon\Carbon::parse($state);
                                                            $fechaInicio = $get('fechaInicio');

                                                            if ($fechaInicio) {
                                                                $inicio = \Carbon\Carbon::parse($fechaInicio);
                                                                $duracion = $inicio->diffInDays($fechaFinal);

                                                                // Advertencia si la campaña es muy corta
                                                                if ($duracion < 7) {
                                                                    Notification::make()
                                                                        ->warning()
                                                                        ->title('Campaña muy corta')
                                                                        ->body("La campaña durará solo {$duracion} días. ¿Es suficiente?")
                                                                        ->duration(4000)
                                                                        ->send();
                                                                }

                                                                // Advertencia si la campaña es muy larga
                                                                if ($duracion > 365) {
                                                                    Notification::make()
                                                                        ->warning()
                                                                        ->title('Campaña muy larga')
                                                                        ->body("La campaña durará {$duracion} días (más de un año). Verifica si es correcto.")
                                                                        ->duration(4000)
                                                                        ->send();
                                                                }

                                                                // Información de duración
                                                                if ($duracion >= 7 && $duracion <= 365) {
                                                                    Notification::make()
                                                                        ->success()
                                                                        ->title('Duración de campaña')
                                                                        ->body("La campaña durará {$duracion} días.")
                                                                        ->duration(3000)
                                                                        ->send();
                                                                }
                                                            }
                                                        }
                                                    })
                                                    ->rules([
                                                        function ($get) {
                                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                                if (!$value) return;

                                                                // Validación 1: Fecha final no puede ser anterior a fecha inicial (puede ser el mismo día)
                                                                $fechaInicio = $get('fechaInicio');
                                                                if ($fechaInicio) {
                                                                    $inicio = \Carbon\Carbon::parse($fechaInicio);
                                                                    $final = \Carbon\Carbon::parse($value);

                                                                    if ($final->lt($inicio)) {
                                                                        $fail('La fecha final no puede ser anterior a la fecha de inicio.');
                                                                    }
                                                                }

                                                                // Validación 2: Fecha final debe estar dentro del año de la estrategia
                                                                $anio = $get('../../anio');
                                                                if ($anio) {
                                                                    $fechaFinal = \Carbon\Carbon::parse($value);
                                                                    $anioFecha = $fechaFinal->year;

                                                                    if ($anioFecha != $anio) {
                                                                        $fail("La fecha final debe estar dentro del año {$anio} de la estrategia.");
                                                                    }
                                                                }
                                                            };
                                                        }
                                                    ]),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(1)
                                            ->minItems(1)
                                            ->required()
                                            ->reorderable(false)
                                            ->addActionLabel('Agregar Versión')
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                    ]),

                                Forms\Components\Section::make('Coemisores')
                                    ->description('Indica si hay coemisores en esta campaña')
                                    ->icon('heroicon-o-building-office-2')
                                    ->schema([
                                        Forms\Components\Textarea::make('coemisores_acronyms')
                                            ->label('Coemisores')
                                            ->rows(2)
                                            ->maxLength(500)
                                            ->hint('Entidades que coemiten la campaña')
                                            ->hintIcon('heroicon-o-question-mark-circle')
                                            ->hintColor('info')
                                            ->helperText('Menciona las dependencias o entidades que participan como coemisores en esta campaña (en caso de ser Coemitida).')
                                            ->placeholder('Ejemplo: Secretaría de Salud, IMSS, ISSSTE'),
                                    ]),

                                Forms\Components\Section::make('Público Objetivo')
                                    ->description('Define a quién va dirigida tu campaña')
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        Forms\Components\Select::make('sexo')
                                            ->label('Sexo')
                                            ->multiple()
                                            ->options([
                                                'Mujeres' => 'Mujeres',
                                                'Hombres' => 'Hombres',
                                            ])
                                            ->required()
                                            ->hint('Género del público')
                                            ->hintIcon('heroicon-o-user')
                                            ->helperText('Selecciona uno o ambos según tu audiencia objetivo.'),
                                        Forms\Components\Select::make('edad')
                                            ->label('Edad')
                                            ->multiple()
                                            ->options([
                                                '00-12' => '00-12',
                                                '13-18' => '13-18',
                                                '19-24' => '19-24',
                                                '25-34' => '25-34',
                                                '35-44' => '35-44',
                                                '45-54' => '45-54',
                                                '55-64' => '55-64',
                                                '65+' => '65+',
                                            ])
                                            ->required()
                                            ->hint('Rango etario')
                                            ->hintIcon('heroicon-o-user')
                                            ->helperText('Puedes seleccionar múltiples rangos de edad.'),
                                        Forms\Components\Select::make('poblacion')
                                            ->label('Población')
                                            ->multiple()
                                            ->options([
                                                'Urbana' => 'Urbana',
                                                'Rural' => 'Rural',
                                            ])
                                            ->required()
                                            ->hint('Tipo de población')
                                            ->hintIcon('heroicon-o-home')
                                            ->helperText('Selecciona el tipo de población objetivo.')
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $state = is_array($state) ? $state : [];
                                                $nse = is_array($get('nse')) ? $get('nse') : [];
                                                if (in_array('Rural', $state)) {
                                                    $set('nse', array_values(array_unique([...$nse, 'E'])));
                                                } else {
                                                    $set('nse', array_values(array_filter($nse, fn($v) => $v !== 'E')));
                                                }
                                            }),
                                        Forms\Components\Select::make('nse')
                                            ->label('NSE (Nivel Socioeconómico)')
                                            ->multiple()
                                            ->options([
                                                'AB' => 'AB - Alto',
                                                'C+' => 'C+ - Medio Alto',
                                                'C-' => 'C- - Medio Bajo',
                                                'D+' => 'D+ - Bajo',
                                                'D' => 'D - Muy Bajo',
                                                'E' => 'E - Marginal',
                                            ])
                                            ->required()
                                            ->hint('Nivel socioeconómico')
                                            ->hintIcon('heroicon-o-banknotes')
                                            ->helperText('Selecciona los niveles socioeconómicos de tu audiencia.')
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $state = is_array($state) ? $state : [];
                                                $poblacion = is_array($get('poblacion')) ? $get('poblacion') : [];
                                                if (in_array('E', $state)) {
                                                    $set('poblacion', array_values(array_unique([...$poblacion, 'Rural'])));
                                                } else {
                                                    $set('poblacion', array_values(array_filter($poblacion, fn($v) => $v !== 'Rural')));
                                                }
                                            }),
                                        Forms\Components\Textarea::make('caracEspecific')
                                            ->label('Características Específicas')
                                            ->maxLength(65535)
                                            ->rows(3)
                                            ->hint('Detalles adicionales')
                                            ->hintIcon('heroicon-o-pencil-square')
                                            ->helperText('Describe características específicas de tu público (ocupación, intereses, comportamientos, etc.).')
                                            ->placeholder('Ejemplo: Madres de familia trabajadoras, interesadas en salud preventiva...'),
                                    ])
                                    ->columns(2),

                                        Forms\Components\Section::make('Medios')
                                        ->description('Indica los medios de comunicación que utilizarás')
                                        ->icon('heroicon-o-tv')
                                        ->schema([
                                            Forms\Components\Checkbox::make('tv_oficial')
                                                ->label('TV Tiempos Oficiales')
                                                ->hint('Tiempos oficiales')
                                                ->hintIcon('heroicon-o-tv')
                                                ->helperText('Marca si usarás tiempos oficiales en televisión.'),
                                            Forms\Components\Checkbox::make('radio_oficial')
                                                ->label('Radio Tiempos Oficiales')
                                                ->hint('Tiempos oficiales')
                                                ->hintIcon('heroicon-o-radio')
                                                ->helperText('Marca si usarás tiempos oficiales en radio.'),
                                            Forms\Components\Checkbox::make('tv_comercial')
                                                ->label('TV Comercial')
                                                ->disabled()
                                                ->dehydrated()
                                                ->afterStateHydrated(function ($state, $set, $get) {
                                                    $televisoras = $get('televisoras') ?? 0;
                                                    $set('tv_comercial', $televisoras > 0);
                                                })
                                                ->hint('Automático')
                                                ->hintIcon('heroicon-o-check-circle')
                                                ->helperText('Se marca automáticamente cuando hay presupuesto en Televisoras'),
                                            Forms\Components\Checkbox::make('radio_comercial')
                                                ->label('Radio Comercial')
                                                ->disabled()
                                                ->dehydrated()
                                                ->afterStateHydrated(function ($state, $set, $get) {
                                                    $radiodifusoras = $get('radiodifusoras') ?? 0;
                                                    $set('radio_comercial', $radiodifusoras > 0);
                                                })
                                                ->hint('Automático')
                                                ->hintIcon('heroicon-o-check-circle')
                                                ->helperText('Se marca automáticamente cuando hay presupuesto en Radiodifusoras'),
                                        ])
                                        ->columns(2),

                                Forms\Components\Section::make('Presupuestos')
                                    ->description('Distribuye el presupuesto de tu campaña entre los diferentes medios y conceptos (todos los campos son opcionales)')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        self::createDecimalField('televisoras', 'Televisoras', [
                                            'afterStateUpdated' => function ($state, $set, $get) {
                                                if ($state > 0) {
                                                    $set('tv_comercial', true);
                                                } else {
                                                    $set('tv_comercial', false);
                                                }
                                            }
                                        ]),
                                        self::createDecimalField('radiodifusoras', 'Radiodifusoras', [
                                            'afterStateUpdated' => function ($state, $set, $get) {
                                                if ($state > 0) {
                                                    $set('radio_comercial', true);
                                                } else {
                                                    $set('radio_comercial', false);
                                                }
                                            }
                                        ]),
                                        self::createDecimalField('radio_comunitaria', 'Radio Comunitaria'),
                                        self::createDecimalField('mediosDigitales', 'Radios Comunitarias'),
                                        self::createDecimalField('mediosDigitalesInternet', 'Medios Digitales'),
                                        self::createDecimalField('decdmx', 'Diarios Editados en la CDMX'),
                                        self::createDecimalField('deedos', 'Diarios Editados en los Estados'),
                                        self::createDecimalField('deextr', 'Medios Internacionales'),
                                        self::createDecimalField('revistas', 'Revistas'),
                                        self::createDecimalField('cine', 'Cine'),
                                        self::createDecimalField('mediosComplementarios', 'Medios Complementarios'),
                                        self::createDecimalField('preEstudios', 'Pre-Estudios'),
                                        self::createDecimalField('postEstudios', 'Post-Estudios'),
                                        self::createDecimalField('disenio', 'Diseño'),
                                        self::createDecimalField('produccion', 'Producción'),
                                        self::createDecimalField('preProduccion', 'Pre-Producción'),
                                        self::createDecimalField('postProduccion', 'Post-Producción'),
                                        self::createDecimalField('copiado', 'Copiado'),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Resumen de Medios')
                                    ->schema([
                                        Forms\Components\Placeholder::make('presupuesto_anual_info')
                                            ->label('Presupuesto Anual')
                                            ->content(function ($get) {
                                                $presupuestoAnual = floatval($get('../../presupuesto') ?? 0);
                                                return '$' . number_format($presupuestoAnual, 2);
                                            })
                                            ->reactive()
                                            ->extraAttributes(['class' => 'font-mono text-sm font-bold']),

                                        Forms\Components\Placeholder::make('suma_medios')
                                            ->label(function ($get) {
                                                $nombreCampaña = $get('name') ?? 'Campaña';
                                                return "Total Medios de: {$nombreCampaña}";
                                            })
                                            ->content(function ($get) {
                                                // Obtener valores de los 18 medios
                                                $televisoras = floatval($get('televisoras') ?? 0);
                                                $radiodifusoras = floatval($get('radiodifusoras') ?? 0);
                                                $radioComunitaria = floatval($get('radio_comunitaria') ?? 0);
                                                $cine = floatval($get('cine') ?? 0);
                                                $decdmx = floatval($get('decdmx') ?? 0);
                                                $deedos = floatval($get('deedos') ?? 0);
                                                $deextr = floatval($get('deextr') ?? 0);
                                                $revistas = floatval($get('revistas') ?? 0);
                                                $mediosComplementarios = floatval($get('mediosComplementarios') ?? 0);
                                                $mediosDigitales = floatval($get('mediosDigitales') ?? 0);
                                                $mediosDigitalesInternet = floatval($get('mediosDigitalesInternet') ?? 0);
                                                $preEstudios = floatval($get('preEstudios') ?? 0);
                                                $postEstudios = floatval($get('postEstudios') ?? 0);
                                                $disenio = floatval($get('disenio') ?? 0);
                                                $produccion = floatval($get('produccion') ?? 0);
                                                $preProduccion = floatval($get('preProduccion') ?? 0);
                                                $postProduccion = floatval($get('postProduccion') ?? 0);
                                                $copiado = floatval($get('copiado') ?? 0);

                                                // Calcular suma total
                                                $suma = $televisoras + $radiodifusoras + $radioComunitaria + $cine + $decdmx + $deedos +
                                                        $deextr + $revistas + $mediosComplementarios + $mediosDigitales +
                                                        $mediosDigitalesInternet + $preEstudios + $postEstudios + $disenio +
                                                        $produccion + $preProduccion + $postProduccion + $copiado;

                                                return '$' . number_format($suma, 2);
                                            })
                                            ->reactive()
                                            ->helperText('Suma automática de los 18 medios de esta campaña')
                                            ->extraAttributes(['class' => 'font-mono text-sm']),

                                        Forms\Components\Placeholder::make('porcentaje_campaña')
                                            ->label('Porcentaje del Presupuesto Anual')
                                            ->content(function ($get) {
                                                $presupuestoAnual = floatval($get('../../presupuesto') ?? 0);

                                                $televisoras = floatval($get('televisoras') ?? 0);
                                                $radiodifusoras = floatval($get('radiodifusoras') ?? 0);
                                                $radioComunitaria = floatval($get('radio_comunitaria') ?? 0);
                                                $cine = floatval($get('cine') ?? 0);
                                                $decdmx = floatval($get('decdmx') ?? 0);
                                                $deedos = floatval($get('deedos') ?? 0);
                                                $deextr = floatval($get('deextr') ?? 0);
                                                $revistas = floatval($get('revistas') ?? 0);
                                                $mediosComplementarios = floatval($get('mediosComplementarios') ?? 0);
                                                $mediosDigitales = floatval($get('mediosDigitales') ?? 0);
                                                $mediosDigitalesInternet = floatval($get('mediosDigitalesInternet') ?? 0);
                                                $preEstudios = floatval($get('preEstudios') ?? 0);
                                                $postEstudios = floatval($get('postEstudios') ?? 0);
                                                $disenio = floatval($get('disenio') ?? 0);
                                                $produccion = floatval($get('produccion') ?? 0);
                                                $preProduccion = floatval($get('preProduccion') ?? 0);
                                                $postProduccion = floatval($get('postProduccion') ?? 0);
                                                $copiado = floatval($get('copiado') ?? 0);

                                                $sumaCampaña = $televisoras + $radiodifusoras + $radioComunitaria + $cine + $decdmx + $deedos +
                                                              $deextr + $revistas + $mediosComplementarios + $mediosDigitales +
                                                              $mediosDigitalesInternet + $preEstudios + $postEstudios + $disenio +
                                                              $produccion + $preProduccion + $postProduccion + $copiado;

                                                $porcentaje = $presupuestoAnual > 0 ? ($sumaCampaña / $presupuestoAnual) * 100 : 0;

                                                return number_format($porcentaje, 2) . '%';
                                            })
                                            ->reactive()
                                            ->helperText('Porcentaje que representa esta campaña del presupuesto anual')
                                            ->extraAttributes(['class' => 'font-mono text-sm font-bold']),

                                        Forms\Components\Placeholder::make('porcentaje_radio_comunitaria')
                                            ->label('% Radio Comunitaria')
                                            ->content(function ($get) {
                                                $radioComunitaria = floatval($get('radio_comunitaria') ?? 0);

                                                $televisoras = floatval($get('televisoras') ?? 0);
                                                $radiodifusoras = floatval($get('radiodifusoras') ?? 0);
                                                $cine = floatval($get('cine') ?? 0);
                                                $decdmx = floatval($get('decdmx') ?? 0);
                                                $deedos = floatval($get('deedos') ?? 0);
                                                $deextr = floatval($get('deextr') ?? 0);
                                                $revistas = floatval($get('revistas') ?? 0);
                                                $mediosComplementarios = floatval($get('mediosComplementarios') ?? 0);
                                                $mediosDigitales = floatval($get('mediosDigitales') ?? 0);
                                                $mediosDigitalesInternet = floatval($get('mediosDigitalesInternet') ?? 0);
                                                $preEstudios = floatval($get('preEstudios') ?? 0);
                                                $postEstudios = floatval($get('postEstudios') ?? 0);
                                                $disenio = floatval($get('disenio') ?? 0);
                                                $produccion = floatval($get('produccion') ?? 0);
                                                $preProduccion = floatval($get('preProduccion') ?? 0);
                                                $postProduccion = floatval($get('postProduccion') ?? 0);
                                                $copiado = floatval($get('copiado') ?? 0);

                                                $sumaCampaña = $televisoras + $radiodifusoras + $radioComunitaria + $cine + $decdmx + $deedos +
                                                              $deextr + $revistas + $mediosComplementarios + $mediosDigitales +
                                                              $mediosDigitalesInternet + $preEstudios + $postEstudios + $disenio +
                                                              $produccion + $preProduccion + $postProduccion + $copiado;

                                                $porcentaje = $sumaCampaña > 0 ? ($radioComunitaria / $sumaCampaña) * 100 : 0;

                                                return number_format($porcentaje, 2) . '%';
                                            })
                                            ->reactive()
                                            ->helperText('% que representa Radio Comunitaria sobre el total de la campaña')
                                            ->extraAttributes(['class' => 'font-mono text-sm']),
                                    ])
                                    ->columns(3)
                                    ->collapsible(false)
                                    ->extraAttributes(['class' => 'border-green-500 bg-green-50']),


                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
                        ]),

                    Wizard\Step::make('Resumen y Envío')
                        ->description('Revisa y envía tu estrategia')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->completedIcon('heroicon-o-check-badge')
                        ->schema([
                            Forms\Components\Section::make('Resumen Global del Presupuesto')
                    ->schema([
                        Forms\Components\Placeholder::make('total_campañas')
                            ->label('Total de Campañas')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalGeneral = 0;
                                
                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['televisoras'])) {
                                        $televisoras = floatval($campaign['televisoras'] ?? 0);
                                        $radiodifusoras = floatval($campaign['radiodifusoras'] ?? 0);
                                        $radioComunitaria = floatval($campaign['radio_comunitaria'] ?? 0);
                                        $cine = floatval($campaign['cine'] ?? 0);
                                        $decdmx = floatval($campaign['decdmx'] ?? 0);
                                        $deedos = floatval($campaign['deedos'] ?? 0);
                                        $deextr = floatval($campaign['deextr'] ?? 0);
                                        $revistas = floatval($campaign['revistas'] ?? 0);
                                        $mediosComplementarios = floatval($campaign['mediosComplementarios'] ?? 0);
                                        $mediosDigitales = floatval($campaign['mediosDigitales'] ?? 0);
                                        $mediosDigitalesInternet = floatval($campaign['mediosDigitalesInternet'] ?? 0);
                                        $preEstudios = floatval($campaign['preEstudios'] ?? 0);
                                        $postEstudios = floatval($campaign['postEstudios'] ?? 0);
                                        $disenio = floatval($campaign['disenio'] ?? 0);
                                        $produccion = floatval($campaign['produccion'] ?? 0);
                                        $preProduccion = floatval($campaign['preProduccion'] ?? 0);
                                        $postProduccion = floatval($campaign['postProduccion'] ?? 0);
                                        $copiado = floatval($campaign['copiado'] ?? 0);

                                        $sumaCampaña = $televisoras + $radiodifusoras + $radioComunitaria + $cine + $decdmx + $deedos +
                                                      $deextr + $revistas + $mediosComplementarios + $mediosDigitales +
                                                      $mediosDigitalesInternet + $preEstudios + $postEstudios + $disenio +
                                                      $produccion + $preProduccion + $postProduccion + $copiado;

                                        $totalGeneral += $sumaCampaña;
                                    }
                                }

                                return '$' . number_format($totalGeneral, 2);
                            })
                            ->reactive()
                            ->helperText('Suma total de todas las campañas')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),
                        
                        Forms\Components\Placeholder::make('porcentaje_disponible')
                            ->label('Porcentaje Utilizado')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalGeneral = 0;

                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['televisoras'])) {
                                        $televisoras = floatval($campaign['televisoras'] ?? 0);
                                        $radiodifusoras = floatval($campaign['radiodifusoras'] ?? 0);
                                        $radioComunitaria = floatval($campaign['radio_comunitaria'] ?? 0);
                                        $cine = floatval($campaign['cine'] ?? 0);
                                        $decdmx = floatval($campaign['decdmx'] ?? 0);
                                        $deedos = floatval($campaign['deedos'] ?? 0);
                                        $deextr = floatval($campaign['deextr'] ?? 0);
                                        $revistas = floatval($campaign['revistas'] ?? 0);
                                        $mediosComplementarios = floatval($campaign['mediosComplementarios'] ?? 0);
                                        $mediosDigitales = floatval($campaign['mediosDigitales'] ?? 0);
                                        $mediosDigitalesInternet = floatval($campaign['mediosDigitalesInternet'] ?? 0);
                                        $preEstudios = floatval($campaign['preEstudios'] ?? 0);
                                        $postEstudios = floatval($campaign['postEstudios'] ?? 0);
                                        $disenio = floatval($campaign['disenio'] ?? 0);
                                        $produccion = floatval($campaign['produccion'] ?? 0);
                                        $preProduccion = floatval($campaign['preProduccion'] ?? 0);
                                        $postProduccion = floatval($campaign['postProduccion'] ?? 0);
                                        $copiado = floatval($campaign['copiado'] ?? 0);

                                        $sumaCampaña = $televisoras + $radiodifusoras + $radioComunitaria + $cine + $decdmx + $deedos +
                                                      $deextr + $revistas + $mediosComplementarios + $mediosDigitales +
                                                      $mediosDigitalesInternet + $preEstudios + $postEstudios + $disenio +
                                                      $produccion + $preProduccion + $postProduccion + $copiado;

                                        $totalGeneral += $sumaCampaña;
                                    }
                                }

                                $presupuesto = floatval($get('presupuesto') ?? 0);
                                
                                if ($presupuesto > 0) {
                                    $porcentaje = ($totalGeneral / $presupuesto) * 100;
                                    
                                    // Determinar color basado en el porcentaje
                                    if ($porcentaje > 100) {
                                        $color = 'text-red-600';
                                        $icono = '⚠️ ';
                                    } elseif ($porcentaje > 80) {
                                        $color = 'text-orange-600';
                                        $icono = '⚡ ';
                                    } else {
                                        $color = 'text-green-600';
                                        $icono = '✅ ';
                                    }
                                    
                                    return view('components.porcentaje-presupuesto', [
                                        'porcentaje' => $porcentaje,
                                        'color' => $color,
                                        'icono' => $icono
                                    ]);
                                }
                                
                                return view('components.porcentaje-presupuesto', [
                                    'porcentaje' => 0,
                                    'color' => 'text-gray-600',
                                    'icono' => '📊 '
                                ]);
                            })
                            ->reactive()
                            ->helperText('Porcentaje del presupuesto total utilizado por todas las campañas')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),
                        
                        Forms\Components\Placeholder::make('presupuesto_anual_total')
                            ->label('Presupuesto Anual')
                            ->content(function ($get) {
                                $presupuesto = floatval($get('presupuesto') ?? 0);
                                return '$' . number_format($presupuesto, 2);
                            })
                            ->reactive()
                            ->helperText('Presupuesto total definido para el año')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),
                        
                        Forms\Components\Placeholder::make('presupuesto_disponible_global')
                            ->label('Presupuesto Disponible')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalGeneral = 0;

                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['televisoras'])) {
                                        $televisoras = floatval($campaign['televisoras'] ?? 0);
                                        $radiodifusoras = floatval($campaign['radiodifusoras'] ?? 0);
                                        $radioComunitaria = floatval($campaign['radio_comunitaria'] ?? 0);
                                        $cine = floatval($campaign['cine'] ?? 0);
                                        $decdmx = floatval($campaign['decdmx'] ?? 0);
                                        $deedos = floatval($campaign['deedos'] ?? 0);
                                        $deextr = floatval($campaign['deextr'] ?? 0);
                                        $revistas = floatval($campaign['revistas'] ?? 0);
                                        $mediosComplementarios = floatval($campaign['mediosComplementarios'] ?? 0);
                                        $mediosDigitales = floatval($campaign['mediosDigitales'] ?? 0);
                                        $mediosDigitalesInternet = floatval($campaign['mediosDigitalesInternet'] ?? 0);
                                        $preEstudios = floatval($campaign['preEstudios'] ?? 0);
                                        $postEstudios = floatval($campaign['postEstudios'] ?? 0);
                                        $disenio = floatval($campaign['disenio'] ?? 0);
                                        $produccion = floatval($campaign['produccion'] ?? 0);
                                        $preProduccion = floatval($campaign['preProduccion'] ?? 0);
                                        $postProduccion = floatval($campaign['postProduccion'] ?? 0);
                                        $copiado = floatval($campaign['copiado'] ?? 0);

                                        $sumaCampaña = $televisoras + $radiodifusoras + $radioComunitaria + $cine + $decdmx + $deedos +
                                                      $deextr + $revistas + $mediosComplementarios + $mediosDigitales +
                                                      $mediosDigitalesInternet + $preEstudios + $postEstudios + $disenio +
                                                      $produccion + $preProduccion + $postProduccion + $copiado;

                                        $totalGeneral += $sumaCampaña;
                                    }
                                }

                                $presupuesto = floatval($get('presupuesto') ?? 0);
                                $disponible = $presupuesto - $totalGeneral;

                                $color = $disponible < 0 ? 'text-red-600' : 'text-green-600';
                                $icono = $disponible < 0 ? '⚠️ ' : '✅ ';

                                return view('components.presupuesto-disponible', [
                                    'monto' => $disponible,
                                    'color' => $color,
                                    'icono' => $icono
                                ]);
                            })
                            ->reactive()
                            ->helperText('Presupuesto restante después de asignar a todas las campañas')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),

                        Forms\Components\Placeholder::make('porcentaje_radio_comunitaria_global')
                            ->label('% Radio Comunitaria Global')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalRadioComunitaria = 0;

                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['radio_comunitaria'])) {
                                        $totalRadioComunitaria += floatval($campaign['radio_comunitaria'] ?? 0);
                                    }
                                }

                                $presupuesto = floatval($get('presupuesto') ?? 0);
                                $porcentaje = $presupuesto > 0 ? ($totalRadioComunitaria / $presupuesto) * 100 : 0;

                                $cumple = $porcentaje >= 1;
                                $color  = $cumple ? 'color: #16a34a; font-weight: bold;' : 'color: #dc2626; font-weight: bold;';
                                $icono  = $cumple ? '✅ ' : '⚠️ ';

                                return new \Illuminate\Support\HtmlString(
                                    "<span style=\"{$color}\">{$icono}" . number_format($porcentaje, 2) . "%</span>"
                                );
                            })
                            ->reactive()
                            ->helperText('Suma de Radio Comunitaria de todas las campañas vs presupuesto (mínimo requerido: 1%)')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),
                    ])
                    ->columns(4)
                    ->collapsible(false),

                    Forms\Components\Section::make('Detalle de Campañas')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Forms\Components\Placeholder::make('campañas_detalle')
                            ->label('')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];

                                if (empty($campaigns)) {
                                    return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-sm">No hay campañas registradas.</p>');
                                }

                                $rows = '';
                                foreach ($campaigns as $campaign) {
                                    $nombre = $campaign['name'] ?? '(Sin nombre)';
                                    $total = 0;

                                    if (isset($campaign['televisoras'])) {
                                        $total = floatval($campaign['televisoras'] ?? 0)
                                            + floatval($campaign['radiodifusoras'] ?? 0)
                                            + floatval($campaign['cine'] ?? 0)
                                            + floatval($campaign['decdmx'] ?? 0)
                                            + floatval($campaign['deedos'] ?? 0)
                                            + floatval($campaign['deextr'] ?? 0)
                                            + floatval($campaign['revistas'] ?? 0)
                                            + floatval($campaign['mediosComplementarios'] ?? 0)
                                            + floatval($campaign['mediosDigitales'] ?? 0)
                                            + floatval($campaign['mediosDigitalesInternet'] ?? 0)
                                            + floatval($campaign['preEstudios'] ?? 0)
                                            + floatval($campaign['postEstudios'] ?? 0)
                                            + floatval($campaign['disenio'] ?? 0)
                                            + floatval($campaign['produccion'] ?? 0)
                                            + floatval($campaign['preProduccion'] ?? 0)
                                            + floatval($campaign['postProduccion'] ?? 0)
                                            + floatval($campaign['copiado'] ?? 0);
                                    }

                                    $rows .= '<tr class="border-b last:border-0">'
                                        . '<td class="py-2 px-4 font-medium text-gray-800">' . e($nombre) . '</td>'
                                        . '<td class="py-2 px-4 text-right font-mono text-gray-800">$' . number_format($total, 2) . '</td>'
                                        . '</tr>';
                                }

                                return new \Illuminate\Support\HtmlString(
                                    '<table class="w-full text-sm">'
                                    . '<thead><tr class="border-b bg-gray-50"><th class="py-2 px-4 text-left font-semibold text-gray-600">Campaña</th><th class="py-2 px-4 text-right font-semibold text-gray-600">Presupuesto Asignado</th></tr></thead>'
                                    . '<tbody>' . $rows . '</tbody>'
                                    . '</table>'
                                );
                            })
                            ->reactive(),
                    ])
                    ->collapsible(false),

                    Forms\Components\Section::make('Justificación de Estudios')
                    ->description(function ($get) {
                        $campaigns = $get('campaigns') ?? [];
                        $totalEstudios = 0;

                        foreach ($campaigns as $campaign) {
                            $totalEstudios += floatval($campaign['preEstudios'] ?? 0) + floatval($campaign['postEstudios'] ?? 0);
                        }

                        if ($totalEstudios == 0) {
                            return 'Seleccionar una justificación para la no presentación de Estudio de Pertinencia y Efectividad';
                        }

                        return 'NO es necesario añadir justificación';
                    })
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Placeholder::make('total_estudios_info')
                            ->label('Total de Estudios')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalEstudios = 0;

                                foreach ($campaigns as $campaign) {
                                    $totalEstudios += floatval($campaign['preEstudios'] ?? 0) + floatval($campaign['postEstudios'] ?? 0);
                                }

                                if ($totalEstudios == 0) {
                                    return '⚠️ $0.00 - Se requiere justificación';
                                }

                                return '✅ $' . number_format($totalEstudios, 2);
                            })
                            ->reactive()
                            ->helperText('Suma de Pre-Estudios y Post-Estudios de todas las campañas')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),

                        Forms\Components\Select::make('justificacion_estudios')
                            ->label('Justificación por no realizar estudios')
                            ->options([
                                'Insuficiencia de recursos o poca disponibilidad presupuestal' => ' Insuficiencia de recursos o poca disponibilidad presupuestal',
                                'Cancelación de Programa' => 'Cancelación de Programa',
                                'Cancelación de Campaña' => ' Cancelación de Campaña',
                                'Cuando la dependencia o entidad cuente con un área de estudios, que haya realizado uno en otra
                                Campaña o lo tenga programado' => 'Cuando la dependencia o entidad cuente con un área de estudios, que haya realizado uno en otra
                                Campaña o lo tenga programado',
                                'Cuando la dependencia o entidad haya realizado estudios de mercado, con énfasis en el análisis de
                                ventas o de prestación de servicios' => 'Cuando la dependencia o entidad haya realizado estudios de mercado, con énfasis en el análisis de
                                ventas o de prestación de servicios',
                                'Cuando la dependencia o entidad haya coemitido Campaña, para la cual se haya previsto la aplicación
                                de estudio por alguna entidad coemisora' => 'Cuando la dependencia o entidad haya coemitido Campaña, para la cual se haya previsto la aplicación
                                de estudio por alguna entidad coemisora',
                            ])
                            ->placeholder('Selecciona una justificación')
                            ->helperText('Este campo es obligatorio si la suma total de Pre-Estudios y Post-Estudios es igual a $0.00')
                            ->live(onBlur: true)
                            ->required(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalEstudios = 0;

                                foreach ($campaigns as $campaign) {
                                    $totalEstudios += floatval($campaign['preEstudios'] ?? 0) + floatval($campaign['postEstudios'] ?? 0);
                                }

                                return $totalEstudios == 0;
                            })
                            ->visible(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalEstudios = 0;

                                foreach ($campaigns as $campaign) {
                                    $totalEstudios += floatval($campaign['preEstudios'] ?? 0) + floatval($campaign['postEstudios'] ?? 0);
                                }

                                return $totalEstudios == 0;
                            }),
                    ]),

                    Forms\Components\Section::make('Responsables')
                        ->schema([
                            Forms\Components\Hidden::make('responsable_id')
                                ->default(function () {
                                    $user = Auth::user();
                                    if ($user && $user->institution_id) {
                                        $responsable = \App\Models\Responsable::where('institution_id', $user->institution_id)->first();
                                        return $responsable ? $responsable->id : null;
                                    }
                                    return null;
                                }),
                            Forms\Components\TextInput::make('responsable_name')
                                ->label('Responsable Institución')
                                ->disabled()
                                ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                                ->default(function () {
                                    $user = Auth::user();
                                    if ($user && $user->institution_id) {
                                        $responsable = \App\Models\Responsable::where('institution_id', $user->institution_id)->first();
                                        if ($responsable) {
                                            return $responsable->name;
                                        }
                                    }
                                    return 'No aplica';
                                }),
                            Forms\Components\TextInput::make('NombreSectorResponsable')
                                ->label('Responsable del Sector')
                                ->disabled()
                                ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                                ->default(function () {
                                    $user = Auth::user();
                                    if ($user && $user->institution_id) {
                                        $institution = \App\Models\Institution::with('sector')->find($user->institution_id);
                                        if ($institution && $institution->sector) {
                                            return $institution->sector->ResponsableSector ?? 'No disponible';
                                        }
                                    }
                                    return 'No disponible';
                                }),
                        ])
                        ->columns(2),

                // Acciones del formulario
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('enviar_cs')
                        ->label('Enviar a CS')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->size('lg')
                        ->visible(function ($record) {
                            $user = Auth::user();
                            if (!$user || !$user->role) return false;

                            // Solo usuarios de institución pueden enviar a CS
                            return $user->role->name === 'institution_user' &&
                                   $record && $record->estado_estrategia === 'Creada';
                        })
                        ->action(function ($record, $data) {
                            // Validar que la suma de campañas sea igual al presupuesto anual
                            $totalCampanas = 0;
                            $totalRadiosComunitarias = 0;

                            foreach ($record->campaigns as $campaign) {
                                $sumaCampaña = ($campaign->televisoras ?? 0) + ($campaign->radiodifusoras ?? 0) +
                                             ($campaign->mediosDigitales ?? 0) + ($campaign->mediosDigitalesInternet ?? 0) +
                                             ($campaign->decdmx ?? 0) + ($campaign->deedos ?? 0) + ($campaign->deextr ?? 0) +
                                             ($campaign->revistas ?? 0) + ($campaign->cine ?? 0) +
                                             ($campaign->mediosComplementarios ?? 0) + ($campaign->preEstudios ?? 0) +
                                             ($campaign->postEstudios ?? 0) + ($campaign->disenio ?? 0) +
                                             ($campaign->produccion ?? 0) + ($campaign->preProduccion ?? 0) +
                                             ($campaign->postProduccion ?? 0) + ($campaign->copiado ?? 0);

                                $totalCampanas += $sumaCampaña;
                                $totalRadiosComunitarias += ($campaign->mediosDigitales ?? 0);
                            }

                            // Validación obligatoria: Suma de campañas debe ser igual al presupuesto
                            if (abs($totalCampanas - $record->presupuesto) > 0.01) {
                                Notification::make()
                                    ->title('Error de validación')
                                    ->body('La suma total de las campañas ($' . number_format($totalCampanas, 2) . ') debe ser igual al presupuesto anual ($' . number_format($record->presupuesto, 2) . ').')
                                    ->danger()
                                    ->duration(10000)
                                    ->send();
                                return;
                            }

                            // Notificación informativa: Radios Comunitarias < 1%
                            $porcentajeRadios = $record->presupuesto > 0 ? ($totalRadiosComunitarias / $record->presupuesto) * 100 : 0;
                            if ($porcentajeRadios < 1) {
                                Notification::make()
                                    ->title('Advertencia: Radios Comunitarias')
                                    ->body('Las Radios Comunitarias representan solo el ' . number_format($porcentajeRadios, 2) . '% del presupuesto total. Se recomienda que cubran al menos el 1% del presupuesto anual.')
                                    ->warning()
                                    ->duration(8000)
                                    ->send();
                            }

                            // Cambiar estado a 'Enviado a CS'
                            $record->update(['estado_estrategia' => 'Enviado a CS']);

                            // Mostrar notificación de éxito
                            Notification::make()
                                ->title('Estrategia Enviada')
                                ->body('La estrategia ha sido enviada a Coordinadora de Sector exitosamente.')
                                ->success()
                                ->send();

                            // Redirigir a la lista
                            return redirect(static::getUrl('index'));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Enviar a CS')
                        ->modalDescription('¿Estás seguro de que quieres enviar esta estrategia a Coordinadora de Sector? Una vez enviada, no podrás editarla.')
                        ->modalSubmitActionLabel('Sí, Enviar')
                        ->modalCancelActionLabel('Cancelar'),
                    
                    // Forms\Components\Actions\Action::make('aceptar_cs')
                    //     ->label('Aceptar CS')
                    //     ->icon('heroicon-o-check-circle')
                    //     ->color('success')
                    //     ->size('lg')
                    //     ->visible(function ($record) {
                    //         $user = Auth::user();
                    //         if (!$user || !$user->role) return false;
                            
                    //         // Solo coordinadores de sector pueden aceptar CS
                    //         return $user->role->name === 'sector_coordinator' && 
                    //                $record && $record->estado_estrategia === 'Enviado a CS';
                    //     })
                    //     ->action(function ($record, $data) {
                    //         // Cambiar estado a 'Aceptada CS'
                    //         $record->update(['estado_estrategia' => 'Aceptada CS']);
                            
                    //         // Mostrar notificación de éxito
                    //         Notification::make()
                    //             ->title('Estrategia Aceptada')
                    //             ->body('La estrategia ha sido aceptada por la Coordinadora de Sector exitosamente.')
                    //             ->success()
                    //             ->send();
                            
                    //         // Redirigir a la lista
                    //         return redirect()->route('filament.admin.resources.estrategies.index');
                    //     })
                    //     ->requiresConfirmation()
                    //     ->modalHeading('Aceptar Estrategia')
                    //     ->modalDescription('¿Estás seguro de que quieres aceptar esta estrategia? Una vez aceptada, pasará a DGNC para autorización.')
                    //     ->modalSubmitActionLabel('Sí, Aceptar')
                    //     ->modalCancelActionLabel('Cancelar'),
                    
                    Forms\Components\Actions\Action::make('autorizar_dgnc')
                        ->label('Autorizar DGNC')
                        ->icon('heroicon-o-shield-check')
                        ->color('success')
                        ->size('lg')
                        ->visible(function ($record) {
                            $user = Auth::user();
                            if (!$user || !$user->role) return false;
                            
                            // Solo usuarios DGNC pueden autorizar
                            return $user->role->name === 'dgnc_user' && 
                                   $record && $record->estado_estrategia === 'Enviada a DGNC';
                        })
                        ->action(function ($record, $data) {
                            // Cambiar estado a 'Autorizada'
                            $record->update(['estado_estrategia' => 'Autorizada']);
                            
                            // Mostrar notificación de éxito
                            Notification::make()
                                ->title('Estrategia Autorizada')
                                ->body('La estrategia ha sido autorizada por DGNC exitosamente.')
                                ->success()
                                ->send();
                            
                            // Redirigir a la lista
                            return redirect(static::getUrl('index'));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Autorizar Estrategia')
                        ->modalDescription('¿Estás seguro de que quieres autorizar esta estrategia? Una vez autorizada, estará disponible para modificaciones.')
                        ->modalSubmitActionLabel('Sí, Autorizar')
                        ->modalCancelActionLabel('Cancelar'),

                    Forms\Components\Actions\Action::make('regresar_tabla')
                        ->label('Regresar a la tabla')
                        ->icon('heroicon-o-arrow-left')
                        ->color('gray')
                        ->size('lg')
                        ->url(fn () => static::getUrl('index')),
                    ])
                    ->alignment('center')
                    ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                
                if (!$user || !$user->role) {
                    return $query;
                }
                
                switch ($user->role->name) {
                    case 'super_admin':
                        // Super admin ve todas las estrategias
                        break;
                    case 'dgnc_user':
                        // Usuario DGNC ve todas las estrategias
                        break;
                    case 'consulta':
                        // Usuario de consulta ve todas las estrategias (solo lectura)
                        break;
                    case 'sector_coordinator':
                        // Coordinador de sector ve estrategias de su sector en estados 'Enviado a CS' y 'Aceptada CS'
                        if ($user->sector_id) {
                            $query->whereHas('institution', function ($q) use ($user) {
                                $q->where('sector_id', $user->sector_id);
                            });
                        }
                        break;
                    case 'usuarios_segob':
                        // Usuarios Segob ven solo estrategias del sector Secretaría de Gobernación
                        $segobSector = \App\Models\Sector::where('name', 'SECRETARIA DE GOBERNACIÓN')->value('id');
                        if ($segobSector) {
                            $query->whereHas('institution', function ($q) use ($segobSector) {
                                $q->where('sector_id', $segobSector);
                            });
                        }
                        break;
                    default:
                        // Usuarios de institución ven solo su institución
                        if ($user->institution_id) {
                            $query->where('institution_id', $user->institution_id);
                        }
                        break;
                }
                
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('anio')
                    ->label('Año')
                    ->sortable(),

                Tables\Columns\TextColumn::make('partida_presupuestal')
                    ->label('Partida'),
                
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Institución'),
                    //->searchable()
                    //->visible(fn () => Auth::user() && Auth::user()->role && Auth::user()->role->name === 'super_admin'),

                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Registro' => 'info',
                        'Modificación' => 'warning',
                        'Observación' => 'danger',
                        'Cancelación' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Registro' => 'heroicon-o-document-plus',
                        'Modificación' => 'heroicon-o-pencil-square',
                        'Observación' => 'heroicon-o-eye',
                        'Cancelación' => 'heroicon-o-x-mark',
                        default => 'heroicon-o-document',
                    }),
                Tables\Columns\TextColumn::make('oficio_dgnc')
                    ->label('Oficio DGNC')
                    ->searchable()
                    ->placeholder('Sin oficio')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('estado_estrategia')
                    ->label('Estado')
                    ->searchable()
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
                        'Enviado a CS' => 'heroicon-o-paper-airplane',
                        'Aceptada CS' => 'heroicon-o-check-circle',
                        'Rechazada CS' => 'heroicon-o-x-circle',
                        'Enviada a DGNC' => 'heroicon-o-arrow-up-tray',
                        'Autorizada' => 'heroicon-o-check-badge',
                        'Rechazada DGNC' => 'heroicon-o-x-circle',
                        'Observada DGNC' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                Tables\Columns\TextColumn::make('presupuesto')
                    ->label('Presupuesto')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',', decimalSeparator: '.')
                    ->tooltip('Miles de pesos')
                    ->sortable(),
                //Tables\Columns\TextColumn::make('campaigns_count')
                  //  ->label('Campañas')
                  //  ->counts('campaigns'),
                Tables\Columns\TextColumn::make('oficio_dgnc_documents_count')
                    ->label('Oficios DGNC')
                    ->counts('oficioDgncDocuments')
                    ->badge()
                    ->color('warning')
                    ->visible(function () {
                        // Solo mostrar para usuarios que pueden ver oficios DGNC
                        $user = Auth::user();
                        return $user && $user->role && in_array($user->role->name, ['super_admin', 'dgnc_user']);
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Elaboración')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('sector_id')
                    ->label('Sector')
                    ->columnSpan(1)
                    ->form([
                        Forms\Components\Select::make('sector_id')
                            ->label('Sector')
                            ->options(fn () => \App\Models\Sector::query()->pluck('name', 'id'))
                            ->default(function () {
                                $user = Auth::user();
                                if ($user && $user->role && $user->role->name === 'dgnc_user') {
                                    return \App\Models\Sector::where('name', 'SECRETARIA DE GOBERNACIÓN')->value('id');
                                }
                                return null;
                            })
                            ->live(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['sector_id'],
                            fn (Builder $query, $sectorId): Builder => $query->whereHas('institution', function ($q) use ($sectorId) {
                                $q->where('sector_id', $sectorId);
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['sector_id']) {
                            $sector = \App\Models\Sector::find($data['sector_id']);
                            return $sector ? 'Sector: ' . $sector->name : null;
                        }
                        return null;
                    })
                    ->visible(fn () => Auth::user() && Auth::user()->role && in_array(Auth::user()->role->name, ['super_admin', 'dgnc_user', 'usuarios_segob'])),

                Tables\Filters\Filter::make('institution_id')
                    ->label('Institución')
                    ->columnSpan(1)
                    ->form([
                        Forms\Components\Select::make('institution_id')
                            ->label('Institución')
                            ->options(function ($get, $livewire) {
                                // Obtener el sector_id del estado de los filtros de la tabla
                                $sectorId = null;
                                if (isset($livewire->tableFilters['sector_id']) && is_array($livewire->tableFilters['sector_id'])) {
                                    $sectorId = $livewire->tableFilters['sector_id']['sector_id'] ?? null;
                                }
                                
                                $query = \App\Models\Institution::query();
                                
                                if ($sectorId) {
                                    $query->where('sector_id', $sectorId);
                                }
                                
                                return $query->pluck('name', 'id');
                            })
                            ->live(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['institution_id'],
                            fn (Builder $query, $institutionId): Builder => $query->where('institution_id', $institutionId)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['institution_id']) {
                            $institution = \App\Models\Institution::find($data['institution_id']);
                            return $institution ? 'Institución: ' . $institution->name : null;
                        }
                        return null;
                    })
                    ->visible(fn () => Auth::user() && Auth::user()->role && in_array(Auth::user()->role->name, ['super_admin', 'dgnc_user'])),
                    
                Tables\Filters\Filter::make('anio')
                    ->form([
                        Forms\Components\TextInput::make('anio')
                            ->label('Año')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->default(now()->year)
                            ->placeholder((string) now()->year),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['anio'],
                                fn (Builder $query, $anio): Builder => $query->where('anio', $anio),
                            )
                            ->when(
                                !$data['anio'],
                                fn (Builder $query): Builder => $query->where('anio', now()->year),
                            );
                    })
                    ->default(['anio' => now()->year])
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['anio']) {
                            return 'Año: ' . $data['anio'];
                        }
                        return 'Año: ' . now()->year;
                    }),
            ])
            ->filtersFormColumns(2)
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                //  Tables\Actions\Action::make('exportar_pdf')
                //      ->label('Exportar PDF')
                //      ->icon('heroicon-o-document-arrow-down')
                //      ->color('info')
                //      ->action(function ($record) {
                //          // Cargar la estrategia con todas sus relaciones
                //          $estrategy = Estrategy::with([
                //              'institution.sector',
                //              'juridicalNature',
                //              'responsable',
                //              'campaigns.campaignType',
                //              'campaigns.versions'
                //          ])->find($record->id);

                //          // Obtener los logos del PDF desde configuraciones
                //          $logoPath = \App\Models\Configuration::get('pdf.logo_path');
                //          $logoRightPath = \App\Models\Configuration::get('pdf.logo_right_path');

                //          // Función helper para extraer la ruta del archivo desde JSON o string
                //          $extractFilePath = function($value) {
                //              if (empty($value)) {
                //                  return null;
                //              }

                //              // Si es un JSON (array de archivos de Filament)
                //              if (is_string($value) && str_starts_with($value, '{')) {
                //                  $decoded = json_decode($value, true);
                //                  if (is_array($decoded) && count($decoded) > 0) {
                //                      // Obtener el primer valor del array
                //                      return reset($decoded);
                //                  }
                //              }

                //              // Si es un string simple
                //              return $value;
                //          };

                //             // Convertir la ruta del logo izquierdo a ruta absoluta si existe
                //             $logoAbsolutePath = null;
                //             $logoPathExtracted = $extractFilePath($logoPath);
                //             if ($logoPathExtracted) {
                //                 $logoAbsolutePath = storage_path('app/public/' . $logoPathExtracted);
                //                 // Verificar si el archivo existe
                //                 if (!file_exists($logoAbsolutePath)) {
                //                     $logoAbsolutePath = null;
                //                 }
                //             }

                //     // Convertir la ruta del logo derecho a ruta absoluta si existe
                //     $logoRightAbsolutePath = null;
                //     $logoRightPathExtracted = $extractFilePath($logoRightPath);
                //     if ($logoRightPathExtracted) {
                //         $logoRightAbsolutePath = storage_path('app/public/' . $logoRightPathExtracted);
                //         // Verificar si el archivo existe
                //         if (!file_exists($logoRightAbsolutePath)) {
                //             $logoRightAbsolutePath = null;
                //         }
                //     }

                //          // Generar el PDF
                //          $pdf = Pdf::loadView('pdf.estrategy.main', [
                //              'estrategy' => $estrategy,
                //              'logoPath' => $logoAbsolutePath,
                //              'logoRightPath' => $logoRightAbsolutePath
                //          ]);

                //          // Configurar opciones del PDF
                //          $pdf->setPaper('letter', 'portrait');

                //          // Configurar opciones de DomPDF para respetar los márgenes
                //          $pdf->setOption('isHtml5ParserEnabled', true);
                //          $pdf->setOption('isRemoteEnabled', true);
                //          $pdf->setOption('dpi', 96);

                //          // Nombre del archivo
                //          $filename = 'Estrategia_' . $estrategy->institution_name . '_' . $estrategy->anio . '.pdf';
                //          $filename = str_replace(' ', '_', $filename);

                //          // Descargar el PDF
                //          return response()->streamDownload(function () use ($pdf) {
                //              echo $pdf->stream();
                //          }, $filename);
                //      })
                //      ->tooltip('Descargar estrategia en PDF'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('exportar_pdf_horizontal')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        // Cargar la estrategia con todas sus relaciones
                        $estrategy = Estrategy::with([
                            'institution.sector',
                            'juridicalNature',
                            'responsable',
                            'campaigns.campaignType',
                            'campaigns.versions',
                            'planNacionalDesarrollo'
                        ])->find($record->id);

                        // Obtener los logos del PDF desde configuraciones
                        $logoPath = \App\Models\Configuration::get('pdf.logo_path');
                        $logoRightPath = \App\Models\Configuration::get('pdf.logo_right_path');

                        // Función helper para extraer la ruta del archivo desde JSON o string
                        $extractFilePath = function($value) {
                            if (empty($value)) {
                                return null;
                            }

                            // Si es un JSON (array de archivos de Filament)
                            if (is_string($value) && str_starts_with($value, '{')) {
                                $decoded = json_decode($value, true);
                                if (is_array($decoded) && count($decoded) > 0) {
                                    // Obtener el primer valor del array
                                    return reset($decoded);
                                }
                            }

                            // Si es un string simple
                            return $value;
                        };

                        // Convertir la ruta del logo izquierdo a ruta absoluta si existe
                        $logoAbsolutePath = null;
                        $logoPathExtracted = $extractFilePath($logoPath);
                        if ($logoPathExtracted) {
                            $logoAbsolutePath = storage_path('app/public/' . $logoPathExtracted);
                            // Verificar si el archivo existe
                            if (!file_exists($logoAbsolutePath)) {
                                $logoAbsolutePath = null;
                            }
                        }

                        // Convertir la ruta del logo derecho a ruta absoluta si existe
                        $logoRightAbsolutePath = null;
                        $logoRightPathExtracted = $extractFilePath($logoRightPath);
                        if ($logoRightPathExtracted) {
                            $logoRightAbsolutePath = storage_path('app/public/' . $logoRightPathExtracted);
                            // Verificar si el archivo existe
                            if (!file_exists($logoRightAbsolutePath)) {
                                $logoRightAbsolutePath = null;
                            }
                        }

                        // Generar el PDF Horizontal
                        $pdf = Pdf::loadView('pdf.estrategy_horizontal.main', [
                            'estrategy' => $estrategy,
                            'logoPath' => $logoAbsolutePath,
                            'logoRightPath' => $logoRightAbsolutePath
                        ]);

                        // Configurar opciones del PDF - LANDSCAPE carta
                        $pdf->setPaper('letter', 'landscape');

                        // Configurar opciones de DomPDF
                        $pdf->setOption('isHtml5ParserEnabled', true);
                        $pdf->setOption('isRemoteEnabled', true);
                        $pdf->setOption('dpi', 96);

                        // Nombre del archivo
                        $filename = 'EyPA_' . $estrategy->partida_presupuestal . '_' . str_replace(' ', '_', $estrategy->institution->acronym) . '_' . $estrategy->anio . '.pdf';

                        // Descargar el PDF
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, $filename);
                    })
                    ->tooltip('Descargar estrategia PDF'),
                
                Tables\Actions\EditAction::make()
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if (!$user || !$user->role) return false;
                        
                        // Solo mostrar acciones en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        switch ($user->role->name) {
                            case 'super_admin':
                                // Super admin NO puede editar (solo puede ver y eliminar)
                                return false;
                            case 'institution_user':
                                // Usuarios de institución pueden editar si está en estado 'Creada', 'Rechazada CS' o 'Rechazada DGNC'
                                return in_array($record->estado_estrategia, ['Creada', 'Rechazada CS', 'Rechazada DGNC']);
                            case 'sector_coordinator':
                            case 'dgnc_user':
                                // Coordinadores de sector y usuarios DGNC no pueden editar
                                return false;
                            default:
                                return false;
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo super administradores pueden eliminar y solo en la última estrategia
                        return $user && $user->role && $user->role->name === 'super_admin' && 
                               $record->isLatestForInstitutionAndYear();
                    }),
                Tables\Actions\Action::make('enviar_cs')
                    ->label('Enviar a CS')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;

                        // Solo usuarios de institución pueden enviar a CS si está en estado 'Creada', 'Rechazada CS' o 'Rechazada DGNC'
                        return $user && $user->role && $user->role->name === 'institution_user' &&
                               in_array($record->estado_estrategia, ['Creada', 'Rechazada CS', 'Rechazada DGNC']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Enviar a CS')
                    ->modalDescription('¿Estás seguro de que quieres enviar esta estrategia a Coordinadora de Sector? Una vez enviada, no podrás editarla hasta que sea evaluada nuevamente.')
                    ->modalSubmitActionLabel('Sí, Enviar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        // Validar que la suma de campañas sea igual al presupuesto anual
                        $totalCampanas = 0;
                        $totalRadiosComunitarias = 0;

                        foreach ($record->campaigns as $campaign) {
                            $sumaCampaña = ($campaign->televisoras ?? 0) + ($campaign->radiodifusoras ?? 0) +
                                         ($campaign->mediosDigitales ?? 0) + ($campaign->mediosDigitalesInternet ?? 0) +
                                         ($campaign->decdmx ?? 0) + ($campaign->deedos ?? 0) + ($campaign->deextr ?? 0) +
                                         ($campaign->revistas ?? 0) + ($campaign->cine ?? 0) +
                                         ($campaign->mediosComplementarios ?? 0) + ($campaign->preEstudios ?? 0) +
                                         ($campaign->postEstudios ?? 0) + ($campaign->disenio ?? 0) +
                                         ($campaign->produccion ?? 0) + ($campaign->preProduccion ?? 0) +
                                         ($campaign->postProduccion ?? 0) + ($campaign->copiado ?? 0);

                            $totalCampanas += $sumaCampaña;
                            $totalRadiosComunitarias += ($campaign->mediosDigitales ?? 0);
                        }

                        // Validación obligatoria: Suma de campañas debe ser igual al presupuesto
                        if (abs($totalCampanas - $record->presupuesto) > 0.01) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error de validación')
                                ->body('La suma total de las campañas ($' . number_format($totalCampanas, 2) . ') debe ser igual al presupuesto anual ($' . number_format($record->presupuesto, 2) . ').')
                                ->danger()
                                ->duration(10000)
                                ->send();
                            return;
                        }

                        // Notificación informativa: Radios Comunitarias < 1%
                        $porcentajeRadios = $record->presupuesto > 0 ? ($totalRadiosComunitarias / $record->presupuesto) * 100 : 0;
                        if ($porcentajeRadios < 1) {
                            \Filament\Notifications\Notification::make()
                                ->title('Advertencia: Radios Comunitarias')
                                ->body('Las Radios Comunitarias representan solo el ' . number_format($porcentajeRadios, 2) . '% del presupuesto total. Se recomienda que cubran al menos el 1% del presupuesto anual.')
                                ->warning()
                                ->duration(8000)
                                ->send();
                        }

                        // Cambiar estado a 'Enviado a CS'
                        $record->update(['estado_estrategia' => 'Enviado a CS']);

                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia enviada exitosamente')
                            ->body('La estrategia ha sido enviada a Coordinadora de Sector')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('autorizar_dgnc')
                    ->label('Autorizar DGNC')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo usuarios DGNC pueden autorizar si está en estado 'Enviada a DGNC'
                        return $user && $user->role && $user->role->name === 'dgnc_user' && 
                               $record->estado_estrategia === 'Enviada a DGNC';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Autorizar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres autorizar esta estrategia? Una vez autorizada, estará disponible para modificaciones.')
                    ->modalSubmitActionLabel('Sí, Autorizar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $record->update(['estado_estrategia' => 'Autorizada']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia autorizada exitosamente')
                            ->body('La estrategia ha sido autorizada por DGNC')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('rechazar_dgnc')
                    ->label('Rechazar DGNC')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo usuarios DGNC pueden rechazar si está en estado 'Enviada a DGNC'
                        return $user && $user->role && $user->role->name === 'dgnc_user' && 
                               $record->estado_estrategia === 'Enviada a DGNC';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres rechazar esta estrategia? Una vez rechazada, volverá a ser editable por la institución.')
                    ->modalSubmitActionLabel('Sí, Rechazar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $record->update(['estado_estrategia' => 'Rechazada DGNC']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia rechazada')
                            ->body('La estrategia ha sido rechazada por DGNC y vuelve a ser editable')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('observar_dgnc')
                    ->label('Observar DGNC')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo usuarios DGNC pueden observar si está en estado 'Enviada a DGNC'
                        return $user && $user->role && $user->role->name === 'dgnc_user' && 
                               $record->estado_estrategia === 'Enviada a DGNC';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Observar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres marcar esta estrategia como observada? Una vez observada, la institución podrá solventarla.')
                    ->modalSubmitActionLabel('Sí, Observar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $record->update(['estado_estrategia' => 'Observada DGNC']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia observada')
                            ->body('La estrategia ha sido marcada como observada por DGNC')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('modificar_estrategia')
                    ->label('Modificar Estrategia')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo mostrar si la estrategia está autorizada y el usuario puede modificar
                        if ($record->estado_estrategia === 'Autorizada' && 
                            $user && $user->role && in_array($user->role->name, ['institution_user'])) {
                            
                            // Si el concepto es "Cancelación" y el estado es "Autorizada", no mostrar para usuarios de institución
                            if ($record->concepto === 'Cancelación' && 
                                in_array($user->role->name, ['institution_user'])) {
                                return false;
                            }
                            
                            return true;
                        }
                        
                        return false;
                    })
                    ->action(function ($record) {
                        // Lógica para duplicar la estrategia
                        return redirect(static::getUrl('modificar', ['record' => $record->id]));
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Modificar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres crear una modificación de esta estrategia?')
                    ->modalSubmitActionLabel('Sí, Modificar')
                    ->modalCancelActionLabel('Cancelar'),

                Tables\Actions\Action::make('solventar_estrategia')
                    ->label('Solventar Estrategia')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo usuarios de institución pueden solventar si está observada
                        return $user && $user->role && $user->role->name === 'institution_user' &&
                               $record->estado_estrategia === 'Observada DGNC';
                    })
                    ->action(function ($record) {
                        // Lógica para solventar la estrategia
                        return redirect(static::getUrl('solventar', ['record' => $record->id]));
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Solventar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres crear una solventación de esta estrategia?')
                    ->modalSubmitActionLabel('Sí, Solventar')
                    ->modalCancelActionLabel('Cancelar'),

                Tables\Actions\Action::make('cancelar_estrategia')
                    ->label('Cancelar Estrategia')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo usuarios de institución pueden cancelar si está autorizada
                        if ($user && $user->role && $user->role->name === 'institution_user' &&
                            $record->estado_estrategia === 'Autorizada') {
                            
                            // Si el concepto es "Cancelación" y el estado es "Autorizada", no mostrar para usuarios de institución
                            if ($record->concepto === 'Cancelación') {
                                return false;
                            }
                            
                            return true;
                        }
                        
                        return false;
                    })
                    ->action(function ($record) {
                        // Lógica para cancelar la estrategia
                        return redirect(static::getUrl('cancelar', ['record' => $record->id]));
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres crear una cancelación de esta estrategia?')
                    ->modalSubmitActionLabel('Sí, Cancelar')
                    ->modalCancelActionLabel('Cancelar'),
                
                Tables\Actions\Action::make('evaluar_estrategia')
                    ->label('Evaluar Estrategia')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('info')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo coordinadores de sector pueden evaluar estrategias
                        return $user && $user->role && $user->role->name === 'sector_coordinator' && 
                               $record->estado_estrategia === 'Enviado a CS';
                    })
                    ->form([
                        \Filament\Forms\Components\Section::make('Información de la Estrategia')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('anio')
                                    ->label('Año')
                                    ->default(fn ($record) => $record->anio)
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('institution.name')
                                    ->label('Institución')
                                    ->default(fn ($record) => $record->institution->name)
                                    ->columnSpanFull()
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('estado_estrategia')
                                    ->label('Estado Actual')
                                    ->default(fn ($record) => $record->estado_estrategia)
                                    ->disabled(),
                            ])
                            ->columns(3),
                        \Filament\Forms\Components\Section::make('Evaluación')
                            ->schema([
                                \Filament\Forms\Components\Select::make('nuevo_estado')
                                    ->label('Cambiar Estado a')
                                    ->options([
                                        'Aceptada CS' => 'Aceptada CS',
                                        'Rechazada CS' => 'Rechazada CS',
                                    ])
                                    ->required()
                                    ->default('Aceptada CS'),
                            ])
                            ->columns(1),
                    ])
                    ->action(function (array $data, $record) {
                        $validation = \App\Helpers\ExpirationDateHelper::validateEstrategyConcept(
                            $record->concepto,
                            $record->anio,
                            null,
                            'sector_coordinator',
                            Auth::id()
                        );

                        if (!$validation['allowed']) {
                            \Filament\Notifications\Notification::make()
                                ->title('No se puede evaluar la estrategia')
                                ->body($validation['message'])
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        $nuevoEstado = $data['nuevo_estado'] === 'Aceptada CS'
                            ? 'Enviada a DGNC'
                            : $data['nuevo_estado'];

                        $campos = ['estado_estrategia' => $nuevoEstado];
                        if ($nuevoEstado === 'Enviada a DGNC') {
                            $campos['fecha_envio_dgnc'] = now();
                        }

                        $record->update($campos);

                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia evaluada exitosamente')
                            ->body('El estado de la estrategia ha sido actualizado a: ' . $nuevoEstado)
                            ->success()
                            ->send();
                    })
                    ->modalWidth('4xl')
                    ->modalSubmitActionLabel('Evaluar')
                    ->modalCancelActionLabel('Cancelar'),

                Tables\Actions\Action::make('enviar_dgnc')
                    ->label('Enviar a DGNC')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo mostrar en la última estrategia
                        if (!$record->isLatestForInstitutionAndYear()) return false;
                        
                        // Solo coordinadores de sector pueden enviar a DGNC si está en estado 'Aceptada CS'
                        return $user && $user->role && $user->role->name === 'sector_coordinator' && 
                               $record->estado_estrategia === 'Aceptada CS';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Enviar a DGNC')
                    ->modalDescription('¿Estás seguro de que quieres enviar esta estrategia a DGNC? Una vez enviada, pasará a revisión de DGNC.')
                    ->modalSubmitActionLabel('Sí, Enviar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $validation = \App\Helpers\ExpirationDateHelper::validateEstrategyConcept(
                            $record->concepto,
                            $record->anio,
                            null,
                            'sector_coordinator',
                            Auth::id()
                        );

                        if (!$validation['allowed']) {
                            \Filament\Notifications\Notification::make()
                                ->title('No se puede enviar a DGNC')
                                ->body($validation['message'])
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        $record->update([
                            'estado_estrategia' => 'Enviada a DGNC',
                            'fecha_envio_dgnc'  => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia enviada exitosamente')
                            ->body('La estrategia ha sido enviada a DGNC para revisión')
                            ->success()
                            ->send();
                    }),

                // Acción para Super Admin - Editar campos críticos
                Tables\Actions\Action::make('editar_campos_criticos')
                    ->label('Cambios Estrategía')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('warning')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        // Solo Super Admin puede ver esta acción
                        return $user && $user->role && $user->role->name === 'super_admin';
                    })
                    ->form([
                        Forms\Components\Section::make('Cambios Estrategía')
                            ->description('Solo Super Admin puede modificar estos campos')
                            ->schema([
                                Forms\Components\TextInput::make('responsable_name')
                                    ->label('Nombre del Responsable')
                                    ->default(fn ($record) => $record->responsable_name),

                                Forms\Components\TextInput::make('NombreSectorResponsable')
                                    ->label('Nombre del Responsable del Sector')
                                    ->default(fn ($record) => $record->NombreSectorResponsable),
                                
                                Forms\Components\DatePicker::make('fecha_elaboracion')
                                    ->label('Fecha de Elaboración')
                                    ->required()
                                    ->default(fn ($record) => $record->fecha_elaboracion),
                                
                                Forms\Components\DatePicker::make('fecha_envio_dgnc')
                                    ->label('Fecha de Envío DGNC')
                                    ->default(fn ($record) => $record->fecha_envio_dgnc),
                                
                                Forms\Components\Select::make('estado_estrategia')
                                    ->label('Estado de la Estrategia')
                                    ->required()
                                    ->options([
                                        'Creada' => 'Creada',
                                        'Enviado a CS' => 'Enviado a CS',
                                        'Aceptada CS' => 'Aceptada CS',
                                        'Rechazada CS' => 'Rechazada CS',
                                        'Enviada a DGNC' => 'Enviada a DGNC',
                                        'Autorizada' => 'Autorizada',
                                        'Rechazada DGNC' => 'Rechazada DGNC',
                                        'Observada DGNC' => 'Observada DGNC',
                                    ])
                                    ->default(fn ($record) => $record->estado_estrategia),
                            ])
                            ->columns(1),
                    ])
                    ->action(function (array $data, $record) {
                        // Actualizar solo los campos específicos
                        $record->update([
                            'responsable_name' => $data['responsable_name'],
                            'NombreSectorResponsable' => $data['NombreSectorResponsable'],
                            'fecha_elaboracion' => $data['fecha_elaboracion'],
                            'fecha_envio_dgnc' => $data['fecha_envio_dgnc'],
                            'estado_estrategia' => $data['estado_estrategia'],
                        ]);
                        
                        Notification::make()
                            ->title('Campos Actualizados')
                            ->body('Los campos críticos han sido actualizados exitosamente.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Editar Campos Críticos')
                    ->modalDescription('Modifica los campos críticos de la estrategia. Ten cuidado con los cambios de estado.')
                    ->modalSubmitActionLabel('Guardar cambios')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl'),

                // Acciones para Oficios DGNC
                CargarOficioDgncAction::make(),
                VerOficiosDgncAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function () {
                            // Solo super administradores pueden eliminar en masa
                            $user = Auth::user();
                            return $user && $user->role && $user->role->name === 'super_admin';
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Las clases hijas deben sobrescribir este método para definir sus páginas
     * Este método existe solo como recordatorio - las clases hijas deben implementarlo
     */
    public static function getPages(): array
    {
        // Este método debe ser sobrescrito por las clases hijas
        throw new \Exception('Las clases hijas de BaseEstrategyResource deben implementar getPages()');
    }

    /**
     * Obtiene el query filtrado por partida presupuestal
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (isset(static::$partidaPresupuestal)) {
            $query->where('partida_presupuestal', static::$partidaPresupuestal);
        }

        return $query;
    }

    /**
     * Hook para que las clases hijas agreguen/modifiquen steps del wizard
     * Por defecto retorna null (no agrega nada)
     */
    protected static function getEspecificosWizardStep(): ?Wizard\Step
    {
        return null;
    }

    /**
     * Verifica si ya existe una Modificacion o Solventacion activa (estado 'Creada')
     * derivada de la misma estrategia original. Previene duplicados por doble clic o doble pestaña.
     * Retorna el registro activo si hay conflicto, null si se puede proceder.
     */
    public static function findActiveDerivada(int $originalId, string $concepto): ?Estrategy
    {
        return Estrategy::withoutGlobalScopes()
            ->where('estrategia_original_id', $originalId)
            ->where('concepto', $concepto)
            ->where('estado_estrategia', 'Creada')
            ->first();
    }

    /**
     * Verifica unicidad de conceptos que no admiten duplicados (Registro, Cancelacion).
     * Retorna el registro existente si hay conflicto, null si se puede proceder.
     */
    public static function findDuplicateUniqueConcept(
        int $institutionId,
        int $year,
        string $partida,
        string $concepto
    ): ?Estrategy {
        if (!in_array($concepto, ['Registro', 'Cancelacion'])) {
            return null;
        }

        return Estrategy::withoutGlobalScopes()
            ->where('institution_id', $institutionId)
            ->where('anio', $year)
            ->where('partida_presupuestal', $partida)
            ->where('concepto', $concepto)
            ->first();
    }
}
