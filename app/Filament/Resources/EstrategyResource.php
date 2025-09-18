<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstrategyResource\Pages;
use App\Filament\Resources\EstrategyResource\RelationManagers;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class EstrategyResource extends Resource
{
    protected static ?string $model = Estrategy::class;

    /**
     * Helper para crear campos numéricos con formato de decimales
     * Guarda hasta 6 decimales en BD, muestra 2 decimales en interfaz
     */
    private static function createDecimalField(string $name, string $label, array $additionalConfig = []): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make($name)
            ->label($label)
            ->numeric()
            ->step(0.000001) // Permitir hasta 6 decimales
            ->prefix('$')
            ->reactive()
            ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '')
            ->dehydrateStateUsing(fn ($state) => $state ? round((float)$state, 6) : null)
            ->configure($additionalConfig);
    }

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Estrategias';

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
            'institution_admin',
            'sector_coordinator',
            'dgnc_user'
        ]);
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('anio')
                            ->label('Año')
                            ->disabled()
                            ->default(function () {
                                // Tomar el año del filtro activo o año actual
                                return request()->get('tableFilters.anio.anio', now()->year);
                            }),   
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
                            ->label('Responsable')
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
                                return 'No disponible';
                            }),
                        Forms\Components\DatePicker::make('fecha_elaboracion')
                            ->label('Fecha de Elaboración')
                            ->disabled()
                            ->default(now())
                            ->dehydrated(), // Esto asegura que el campo se incluya en el envío aunque esté deshabilitado
                        Forms\Components\TextInput::make('estado_estrategia')
                            ->label('Estado de la Estrategia')
                            ->disabled()
                            ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                            ->default(function () {
                                // Verificar si ya existe una estrategia para este año e institución
                                $anio = request()->get('tableFilters.anio.anio', now()->year);
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
                        Forms\Components\TextInput::make('concepto')
                            ->label('Solicitud')
                            ->disabled()
                            ->dehydrated() // Incluir en el envío aunque esté deshabilitado
                            ->default(function () {
                                // Verificar si ya existe una estrategia para este año e institución
                                $anio = request()->get('tableFilters.anio.anio', now()->year);
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
                        Forms\Components\TextInput::make('oficio_dgnc')
                            ->label('Oficio DGNC')
                            ->maxLength(255)
                            ->disabled(function () {
                                // Solo usuarios con rol dgnc_user pueden editar este campo
                                $user = Auth::user();
                                return !($user && $user->role && $user->role->name === 'dgnc_user');
                            })
                            ->dehydrated(), // Incluir en el envío aunque esté deshabilitado
                        Forms\Components\DatePicker::make('fecha_envio_dgnc')
                            ->label('Fecha de Envío DGNC')
                            ->disabled()
                            ->visible(function () {
                                // Solo mostrar si el estado es 'Enviada a DGNC' o posterior
                                $anio = request()->get('tableFilters.anio.anio', now()->year);
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
                                $anio = request()->get('tableFilters.anio.anio', now()->year);
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
                        return request()->get('tableFilters.anio.anio', now()->year);
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
                        $anio = request()->get('tableFilters.anio.anio', now()->year);
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
                        $anio = request()->get('tableFilters.anio.anio', now()->year);
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

                Forms\Components\Section::make('Información Institucional')
                    ->schema([
                        Forms\Components\Textarea::make('mision')
                            ->label('Misión')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('vision')
                            ->label('Visión')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('objetivo_institucional')
                            ->label('Objetivo Institucional')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('objetivo_estrategia')
                            ->label('Objetivo de la Estrategia')
                            ->required()
                            ->maxLength(65535),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Plan Nacional de Desarrollo')
                    ->schema([
                        Forms\Components\Section::make('Ejes Generales')
                            ->description('Selecciona los ejes generales que aplican')
                            ->schema([
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_1_gobernanza')
                                    ->label('Eje General 1: Gobernanza con justicia y participación ciudadana'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_2_desarrollo')
                                    ->label('Eje General 2: Desarrollo con bienestar y humanismo'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_3_economia')
                                    ->label('Eje General 3: Economía moral y trabajo'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_4_sustentable')
                                    ->label('Eje General 4: Desarrollo sustentable'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Ejes Transversales')
                            ->description('Selecciona los ejes transversales que aplican')
                            ->schema([
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_transversal_1_igualdad')
                                    ->label('Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_transversal_2_innovacion')
                                    ->label('Eje Transversal 2: Innovación pública para el desarrollo tecnológico nacional'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_transversal_3_derechos')
                                    ->label('Eje Transversal 3: Derechos de los pueblos y comunidades indígenas y afromexicanas'),
                            ])
                            ->columns(1),
                    ])
                    ->columns(1),

                    Forms\Components\Section::make('Presupuesto Anual')
                    ->schema([
                        
                    Forms\Components\TextInput::make('presupuesto')
                        ->label('Presupuesto')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('$')
                        ->reactive()
                        ->helperText('cifras en miles de pesos. Ejemplo: 1,000,000 = $1,000'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Campañas')
                    ->schema([
                        Forms\Components\Repeater::make('campaigns')
                            ->label('Ingresa la información de la Campaña')
                            ->relationship('campaigns')
                            ->schema([
                                Forms\Components\Section::make('Información General')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre de la Campaña')
                                            ->required(),
                                        Forms\Components\Select::make('campaign_type_id')
                                            ->label('Tipo de Campaña')
                                            ->relationship('campaignType', 'name')
                                            ->required(),
                                        Forms\Components\Textarea::make('temaEspecifco')
                                            ->label('Tema Específico')
                                            ->required(),
                                        Forms\Components\Textarea::make('objetivoComuicacion')
                                            ->label('Objetivo de Comunicación')
                                            ->required(),
                                        
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Versiones')
                                    ->schema([
                                        Forms\Components\Repeater::make('versions')
                                            ->label('Información de la Versión ó Versiones de la Campaña')
                                            ->relationship('versions')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nombre de la Versión')
                                                    ->required(),
                                                Forms\Components\DatePicker::make('fechaInicio')
                                                    ->label('Fecha de Inicio')
                                                    ->required(),
                                                Forms\Components\DatePicker::make('fechaFinal')
                                                    ->label('Fecha Final')
                                                    ->required(),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                    ]),

                                Forms\Components\Section::make('Público Objetivo')
                                    ->schema([
                                        Forms\Components\Select::make('sexo')
                                            ->label('Sexo')
                                            ->multiple()
                                            ->options([
                                                'Mujeres' => 'Mujeres',
                                                'Hombres' => 'Hombres',
                                            ])
                                            ->required(),
                                        Forms\Components\Select::make('edad')
                                            ->label('Edad')
                                            ->multiple()
                                            ->options([
                                                '18-24' => '18-24',
                                                '25-34' => '25-34',
                                                '35-44' => '35-44',
                                                '45-54' => '45-54',
                                                '55-64' => '55-64',
                                                '65+' => '65+',
                                            ])
                                            ->required(),
                                        Forms\Components\Select::make('poblacion')
                                            ->label('Población')
                                            ->multiple()
                                            ->options([
                                                'Urbana' => 'Urbana',
                                                'Rural' => 'Rural',
                                            ])
                                            ->required(),
                                        Forms\Components\Select::make('nse')
                                            ->label('NSE')
                                            ->multiple()
                                            ->options([
                                                'AB' => 'AB',
                                                'C+' => 'C+',
                                                'C-' => 'C-',
                                                'D+' => 'D+',
                                                'D' => 'D',
                                                'E' => 'E',
                                            ])
                                            ->required(),
                                        Forms\Components\Textarea::make('caracEspecific')
                                            ->label('Características Específicas')
                                            ->maxLength(65535),
                                    ])
                                    ->columns(2),

                                        Forms\Components\Section::make('Medios')
                                        ->schema([
                                            Forms\Components\Checkbox::make('tv_oficial')
                                                ->label('TV Oficial'),
                                            Forms\Components\Checkbox::make('radio_oficial')
                                                ->label('Radio Oficial'),
                                            Forms\Components\Checkbox::make('tv_comercial')
                                                ->label('TV Comercial')
                                                ->disabled()
                                                ->afterStateHydrated(function ($state, $set, $get) {
                                                    $televisoras = $get('televisoras') ?? 0;
                                                    $set('tv_comercial', $televisoras > 0);
                                                })
                                                ->helperText('Se marca automáticamente cuando hay presupuesto en Televisoras'),
                                            Forms\Components\Checkbox::make('radio_comercial')
                                                ->label('Radio Comercial')
                                                ->disabled()
                                                ->afterStateHydrated(function ($state, $set, $get) {
                                                    $radiodifusoras = $get('radiodifusoras') ?? 0;
                                                    $set('radio_comercial', $radiodifusoras > 0);
                                                })
                                                ->helperText('Se marca automáticamente cuando hay presupuesto en Radiodifusoras'),
                                        ])
                                        ->columns(2),

                                Forms\Components\Section::make('Presupuestos')
                                    ->schema([
                                        // Medios Electrónicos
                                        Forms\Components\Section::make('Medios Electrónicos')
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
                                                self::createDecimalField('mediosDigitales', 'Radios Comunitarias'),
                                            ])
                                            ->columns(3)
                                            ->collapsible(),

                                        // Medios Impresos
                                        Forms\Components\Section::make('Medios Impresos')
                                            ->schema([
                                                self::createDecimalField('decdmx', 'Diarios Editados en la CDMX'),
                                                self::createDecimalField('deedos', 'Diarios Editados en los Estados'),
                                                self::createDecimalField('revistas', 'Revistas'),
                                                self::createDecimalField('deextr', 'Medios Internacionales'),
                                            ])
                                            ->columns(2)
                                            ->collapsible(),

                                        // Medios Complementarios
                                        Forms\Components\Section::make('Medios Complementarios')
                                            ->schema([
                                                self::createDecimalField('mediosComplementarios', 'Medios Complementarios'),
                                                self::createDecimalField('cine', 'Cine'),
                                            ])
                                            ->columns(2)
                                            ->collapsible(),

                                        // Estudios
                                        Forms\Components\Section::make('Estudios')
                                            ->schema([
                                                self::createDecimalField('preEstudios', 'Pre-Estudios'),
                                                self::createDecimalField('postEstudios', 'Post-Estudios'),
                                            ])
                                            ->columns(2)
                                            ->collapsible(),

                                        // Diseño, Producción, Post-Producción
                                        Forms\Components\Section::make('Diseño, Producción, Post-Producción')
                                            ->schema([
                                                self::createDecimalField('disenio', 'Diseño'),
                                                self::createDecimalField('produccion', 'Producción'),
                                                self::createDecimalField('preProduccion', 'Pre-Producción'),
                                                self::createDecimalField('postProduccion', 'Post-Producción'),
                                                self::createDecimalField('copiado', 'Copiado'),
                                            ])
                                            ->columns(3)
                                            ->collapsible(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Resumen de Medios')
                                    ->schema([
                                        Forms\Components\Placeholder::make('suma_medios')
                                            ->label(function ($get) {
                                                $nombreCampaña = $get('name') ?? 'Campaña';
                                                return "Total Medios de: {$nombreCampaña}";
                                            })
                                            ->content(function ($get) {
                                                // Obtener valores de los 16 medios
                                                $televisoras = floatval($get('televisoras') ?? 0);
                                                $radiodifusoras = floatval($get('radiodifusoras') ?? 0);
                                                $cine = floatval($get('cine') ?? 0);
                                                $decdmx = floatval($get('decdmx') ?? 0);
                                                $deedos = floatval($get('deedos') ?? 0);
                                                $deextr = floatval($get('deextr') ?? 0);
                                                $revistas = floatval($get('revistas') ?? 0);
                                                $mediosComplementarios = floatval($get('mediosComplementarios') ?? 0);
                                                $mediosDigitales = floatval($get('mediosDigitales') ?? 0);
                                                $preEstudios = floatval($get('preEstudios') ?? 0);
                                                $postEstudios = floatval($get('postEstudios') ?? 0);
                                                $disenio = floatval($get('disenio') ?? 0);
                                                $produccion = floatval($get('produccion') ?? 0);
                                                $preProduccion = floatval($get('preProduccion') ?? 0);
                                                $postProduccion = floatval($get('postProduccion') ?? 0);
                                                $copiado = floatval($get('copiado') ?? 0);
                                                
                                                // Calcular suma total
                                                $suma = $televisoras + $radiodifusoras + $cine + $decdmx + $deedos + 
                                                        $deextr + $revistas + $mediosComplementarios + $mediosDigitales + 
                                                        $preEstudios + $postEstudios + $disenio + $produccion + 
                                                        $preProduccion + $postProduccion + $copiado;
                                                
                                                return '$' . number_format($suma, 2);
                                            })
                                            ->reactive()
                                            ->helperText('Suma automática de los 16 medios de esta campaña')
                                            ->extraAttributes(['class' => 'font-mono text-sm']),
                                    ])
                                    ->columns(1)
                                    ->collapsible(false)
                                    ->extraAttributes(['class' => 'border-green-500 bg-green-50']),


                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),

                Forms\Components\Section::make('Resumen Global del Presupuesto')
                    ->schema([
                        Forms\Components\Placeholder::make('total_campañas')
                            ->label('Total de Todas las Campañas')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalGeneral = 0;
                                
                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['televisoras'])) {
                                        $televisoras = floatval($campaign['televisoras'] ?? 0);
                                        $radiodifusoras = floatval($campaign['radiodifusoras'] ?? 0);
                                        $cine = floatval($campaign['cine'] ?? 0);
                                        $decdmx = floatval($campaign['decdmx'] ?? 0);
                                        $deedos = floatval($campaign['deedos'] ?? 0);
                                        $deextr = floatval($campaign['deextr'] ?? 0);
                                        $revistas = floatval($campaign['revistas'] ?? 0);
                                        $mediosComplementarios = floatval($campaign['mediosComplementarios'] ?? 0);
                                        $mediosDigitales = floatval($campaign['mediosDigitales'] ?? 0);
                                        $preEstudios = floatval($campaign['preEstudios'] ?? 0);
                                        $postEstudios = floatval($campaign['postEstudios'] ?? 0);
                                        $disenio = floatval($campaign['disenio'] ?? 0);
                                        $produccion = floatval($campaign['produccion'] ?? 0);
                                        $preProduccion = floatval($campaign['preProduccion'] ?? 0);
                                        $postProduccion = floatval($campaign['postProduccion'] ?? 0);
                                        $copiado = floatval($campaign['copiado'] ?? 0);
                                        
                                        $sumaCampaña = $televisoras + $radiodifusoras + $cine + $decdmx + $deedos + 
                                                      $deextr + $revistas + $mediosComplementarios + $mediosDigitales + 
                                                      $preEstudios + $postEstudios + $disenio + $produccion + 
                                                      $preProduccion + $postProduccion + $copiado;
                                        
                                        $totalGeneral += $sumaCampaña;
                                    }
                                }
                                
                                return '$' . number_format($totalGeneral, 2);
                            })
                            ->reactive()
                            ->helperText('Suma total de todas las campañas')
                            ->extraAttributes(['class' => 'font-mono text-lg font-bold']),
                        
                        Forms\Components\Placeholder::make('porcentaje_disponible')
                            ->label('Porcentaje Disponible')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalGeneral = 0;
                                
                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['televisoras'])) {
                                        $televisoras = floatval($campaign['televisoras'] ?? 0);
                                        $radiodifusoras = floatval($campaign['radiodifusoras'] ?? 0);
                                        $cine = floatval($campaign['cine'] ?? 0);
                                        $decdmx = floatval($campaign['decdmx'] ?? 0);
                                        $deedos = floatval($campaign['deedos'] ?? 0);
                                        $deextr = floatval($campaign['deextr'] ?? 0);
                                        $revistas = floatval($campaign['revistas'] ?? 0);
                                        $mediosComplementarios = floatval($campaign['mediosComplementarios'] ?? 0);
                                        $mediosDigitales = floatval($campaign['mediosDigitales'] ?? 0);
                                        $preEstudios = floatval($campaign['preEstudios'] ?? 0);
                                        $postEstudios = floatval($campaign['postEstudios'] ?? 0);
                                        $disenio = floatval($campaign['disenio'] ?? 0);
                                        $produccion = floatval($campaign['produccion'] ?? 0);
                                        $preProduccion = floatval($campaign['preProduccion'] ?? 0);
                                        $postProduccion = floatval($campaign['postProduccion'] ?? 0);
                                        $copiado = floatval($campaign['copiado'] ?? 0);
                                        
                                        $sumaCampaña = $televisoras + $radiodifusoras + $cine + $decdmx + $deedos + 
                                                      $deextr + $revistas + $mediosComplementarios + $mediosDigitales + 
                                                      $preEstudios + $postEstudios + $disenio + $produccion + 
                                                      $preProduccion + $postProduccion + $copiado;
                                        
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
                            ->helperText('Porcentaje del presupuesto total utilizado por todas las campañas'),
                        
                        Forms\Components\Placeholder::make('presupuesto_disponible_global')
                            ->label('Presupuesto Disponible')
                            ->content(function ($get) {
                                $campaigns = $get('campaigns') ?? [];
                                $totalGeneral = 0;
                                
                                foreach ($campaigns as $campaign) {
                                    if (isset($campaign['televisoras'])) {
                                        $televisoras = floatval($campaign['televisoras'] ?? 0);
                                        $radiodifusoras = floatval($campaign['radiodifusoras'] ?? 0);
                                        $cine = floatval($campaign['cine'] ?? 0);
                                        $decdmx = floatval($campaign['decdmx'] ?? 0);
                                        $deedos = floatval($campaign['deedos'] ?? 0);
                                        $deextr = floatval($campaign['deextr'] ?? 0);
                                        $revistas = floatval($campaign['revistas'] ?? 0);
                                        $mediosComplementarios = floatval($campaign['mediosComplementarios'] ?? 0);
                                        $mediosDigitales = floatval($campaign['mediosDigitales'] ?? 0);
                                        $preEstudios = floatval($campaign['preEstudios'] ?? 0);
                                        $postEstudios = floatval($campaign['postEstudios'] ?? 0);
                                        $disenio = floatval($campaign['disenio'] ?? 0);
                                        $produccion = floatval($campaign['produccion'] ?? 0);
                                        $preProduccion = floatval($campaign['preProduccion'] ?? 0);
                                        $postProduccion = floatval($campaign['postProduccion'] ?? 0);
                                        $copiado = floatval($campaign['copiado'] ?? 0);
                                        
                                        $sumaCampaña = $televisoras + $radiodifusoras + $cine + $decdmx + $deedos + 
                                                      $deextr + $revistas + $mediosComplementarios + $mediosDigitales + 
                                                      $preEstudios + $postEstudios + $disenio + $produccion + 
                                                      $preProduccion + $postProduccion + $copiado;
                                        
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
                            ->helperText('Presupuesto restante después de asignar a todas las campañas'),
                    ])
                    ->columns(3)
                    ->collapsible(false),

                // Botón para enviar a DGNC
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
                            return in_array($user->role->name, ['institution_admin', 'institution_user']) && 
                                   $record && $record->estado_estrategia === 'Creada';
                        })
                        ->action(function ($record, $data) {
                            // Cambiar estado a 'Enviado a CS'
                            $record->update(['estado_estrategia' => 'Enviado a CS']);
                            
                            // Mostrar notificación de éxito
                            Notification::make()
                                ->title('Estrategia Enviada')
                                ->body('La estrategia ha sido enviada a Coordinadora de Sector exitosamente.')
                                ->success()
                                ->send();
                            
                            // Redirigir a la lista
                            return redirect()->route('filament.admin.resources.estrategies.index');
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
                            return redirect()->route('filament.admin.resources.estrategies.index');
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Autorizar Estrategia')
                        ->modalDescription('¿Estás seguro de que quieres autorizar esta estrategia? Una vez autorizada, estará disponible para modificaciones.')
                        ->modalSubmitActionLabel('Sí, Autorizar')
                        ->modalCancelActionLabel('Cancelar'),
                ])
                ->alignment('center')
                ->columnSpanFull(),
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
                    case 'sector_coordinator':
                        // Coordinador de sector ve estrategias de su sector en estados 'Enviado a CS' y 'Aceptada CS'
                        if ($user->sector_id) {
                            $query->whereHas('institution', function ($q) use ($user) {
                                $q->where('sector_id', $user->sector_id);
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
                
                Tables\Columns\TextColumn::make('institution.sector.name')
                    ->label('Sector'),
                
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Institución'),
                    //->searchable()
                    //->visible(fn () => Auth::user() && Auth::user()->role && Auth::user()->role->name === 'super_admin'),
            
                
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable(),
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
                    ->color('primary'),
                Tables\Columns\TextColumn::make('presupuesto')
                    ->label('Presupuesto')
                    ->money('MXN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('campaigns_count')
                    ->label('Campañas')
                    ->counts('campaigns'),
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
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Institución')
                    ->relationship('institution', 'name')
                    ->visible(fn () => Auth::user() && Auth::user()->role && in_array(Auth::user()->role->name, ['super_admin', 'dgnc_user'])),
                
                Tables\Filters\Filter::make('anio')
                    ->form([
                        Forms\Components\TextInput::make('anio')
                            ->label('Año')
                            ->numeric()
                            ->default(now()->year)
                            ->placeholder('Ej: 2025'),
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
                    ->default(now()->year)
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['anio']) {
                            return 'Año: ' . $data['anio'];
                        }
                        return 'Año: ' . now()->year;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
                            case 'institution_admin':
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
                        return $user && $user->role && in_array($user->role->name, ['institution_admin', 'institution_user']) && 
                               in_array($record->estado_estrategia, ['Creada', 'Rechazada CS', 'Rechazada DGNC']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Enviar a CS')
                    ->modalDescription('¿Estás seguro de que quieres enviar esta estrategia a Coordinadora de Sector? Una vez enviada, no podrás editarla hasta que sea evaluada nuevamente.')
                    ->modalSubmitActionLabel('Sí, Enviar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
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
                            $user && $user->role && in_array($user->role->name, ['super_admin', 'institution_admin', 'institution_user'])) {
                            
                            // Si el concepto es "Cancelación" y el estado es "Autorizada", no mostrar para usuarios de institución
                            if ($record->concepto === 'Cancelación' && 
                                in_array($user->role->name, ['institution_admin', 'institution_user'])) {
                                return false;
                            }
                            
                            return true;
                        }
                        
                        return false;
                    })
                    ->action(function ($record) {
                        // Lógica para duplicar la estrategia
                        return redirect()->route('filament.admin.resources.estrategies.modificar', ['record' => $record->id]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Modificar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres crear una modificación de esta estrategia? Se duplicará con todos sus datos y campañas.')
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
                        return $user && $user->role && in_array($user->role->name, ['institution_admin', 'institution_user']) && 
                               $record->estado_estrategia === 'Observada DGNC';
                    })
                    ->action(function ($record) {
                        // Lógica para solventar la estrategia
                        return redirect()->route('filament.admin.resources.estrategies.solventar', ['record' => $record->id]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Solventar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres crear una solventación de esta estrategia? Se duplicará con todos sus datos y campañas.')
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
                        if ($user && $user->role && in_array($user->role->name, ['institution_admin', 'institution_user']) && 
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
                        return redirect()->route('filament.admin.resources.estrategies.cancelar', ['record' => $record->id]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Estrategia')
                    ->modalDescription('¿Estás seguro de que quieres crear una cancelación de esta estrategia? Se duplicará con todos sus datos y campañas.')
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
                        $record->update(['estado_estrategia' => $data['nuevo_estado']]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estrategia evaluada exitosamente')
                            ->body('El estado ha sido cambiado a: ' . $data['nuevo_estado'])
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
                        $record->update([
                            'estado_estrategia' => 'Enviada a DGNC',
                            'fecha_envio_dgnc' => now() // Actualizar la fecha de envío a DGNC
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
                    ->modalSubmitActionLabel('Guardar Cambios')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstrategies::route('/'),
            'create' => Pages\CreateEstrategy::route('/create'),
            'view' => Pages\ViewEstrategy::route('/{record}'),
            'edit' => Pages\EditEstrategy::route('/{record}/edit'),
            'modificar' => Pages\ModificarEstrategy::route('/{record}/modificar'),
            'solventar' => Pages\SolventarEstrategy::route('/{record}/solventar'),
            'cancelar' => Pages\CancelarEstrategy::route('/{record}/cancelar'),
        ];
    }
}
