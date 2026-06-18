<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanNacionalDesarrolloResource\Pages;
use App\Models\PlanNacionalDesarrollo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PlanNacionalDesarrolloResource extends Resource
{
    protected static ?string $model = PlanNacionalDesarrollo::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Plan Nacional de Desarrollo';

    protected static ?string $modelLabel = 'Plan Nacional de Desarrollo';

    protected static ?string $pluralModelLabel = 'Planes Nacionales de Desarrollo';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user && $user->role && $user->role->name === 'super_admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos del Plan Nacional de Desarrollo')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del PND')
                            ->required()
                            ->maxLength(255)
                            ->placeholder(fn ($get) => $get('sin_plan_publicado') ? 'Sin Plan Nacional publicado' : 'Plan Nacional de Desarrollo 2025-2030')
                            ->helperText(fn ($get) => $get('sin_plan_publicado') ? 'Etiqueta identificadora para este período sin plan publicado' : 'Nombre completo del Plan Nacional de Desarrollo')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('sin_plan_publicado')
                            ->label('Período sin Plan Nacional publicado')
                            ->helperText('Activa esta opción para registrar un período en el que el Plan Nacional de Desarrollo aún no ha sido publicado.')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_inicio')
                                    ->label('Fecha de Inicio')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->placeholder('01/01/2025')
                                    ->helperText('Fecha de inicio del período'),

                                Forms\Components\DatePicker::make('fecha_fin')
                                    ->label('Fecha de Fin')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->placeholder('31/12/2030')
                                    ->helperText('Fecha de finalización del período')
                                    ->after('fecha_inicio'),
                            ]),

                        Forms\Components\Toggle::make('activo')
                            ->label('PND Activo')
                            ->helperText('Solo un PND puede estar activo a la vez. Al activar este, se desactivarán automáticamente los demás.')
                            ->default(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label(fn ($get) => $get('sin_plan_publicado') ? 'Mensaje para mostrar a los usuarios' : 'Descripción')
                            ->rows(3)
                            ->maxLength(65535)
                            ->placeholder(fn ($get) => $get('sin_plan_publicado')
                                ? 'El Plan Nacional de Desarrollo aún no ha sido publicado para este período.'
                                : 'Descripción general del Plan Nacional de Desarrollo')
                            ->helperText(fn ($get) => $get('sin_plan_publicado')
                                ? 'Este texto se mostrará en las estrategias creadas durante este período.'
                                : null)
                            ->required(fn ($get) => (bool) $get('sin_plan_publicado'))
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Configuración de Nombres de Secciones')
                    ->description('Personaliza los nombres de las secciones de ejes')
                    ->icon('heroicon-o-tag')
                    ->hidden(fn ($get) => (bool) $get('sin_plan_publicado'))
                    ->schema([
                        Forms\Components\TextInput::make('nombre_ejes_generales')
                            ->label('Nombre de la Sección de Ejes Generales')
                            ->required()
                            ->maxLength(255)
                            ->default('Ejes Generales')
                            ->placeholder('Ejes Generales, Pilares Estratégicos, etc.')
                            ->helperText('Este nombre se mostrará en los formularios y PDFs')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('nombre_ejes_transversales')
                            ->label('Nombre de la Sección de Ejes Transversales')
                            ->required()
                            ->maxLength(255)
                            ->default('Ejes Transversales')
                            ->placeholder('Ejes Transversales, Líneas Transversales, etc.')
                            ->helperText('Este nombre se mostrará en los formularios y PDFs')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make(fn ($get) => $get('nombre_ejes_generales') ?: 'Ejes Generales')
                    ->description('Define los ejes generales del Plan Nacional de Desarrollo')
                    ->icon('heroicon-o-chart-bar')
                    ->hidden(fn ($get) => (bool) $get('sin_plan_publicado'))
                    ->schema([
                        Forms\Components\Repeater::make('ejes_generales')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('key')
                                            ->label('Clave Única')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('eje_general_1_gobernanza')
                                            ->helperText('Identificador único (solo letras minúsculas, números y guiones bajos)')
                                            ->regex('/^[a-z0-9_]+$/')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('orden')
                                            ->label('Orden')
                                            ->numeric()
                                            ->default(fn ($get, $state) => $state ?? 1)
                                            ->minValue(1)
                                            ->helperText('Número de orden para visualización')
                                            ->columnSpan(1),
                                    ]),

                                Forms\Components\TextInput::make('label')
                                    ->label('Título del Eje')
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder('Eje General 1: Gobernanza con justicia y participación ciudadana')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('description')
                                    ->label('Descripción Breve')
                                    ->maxLength(255)
                                    ->placeholder('Fortalecimiento democrático')
                                    ->helperText('Descripción corta para mostrar como hint en formularios')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Eje General')
                            ->reorderable()
                            ->orderColumn('orden')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Nuevo Eje General')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make(fn ($get) => $get('nombre_ejes_transversales') ?: 'Ejes Transversales')
                    ->description('Define los ejes transversales del Plan Nacional de Desarrollo')
                    ->icon('heroicon-o-arrow-path')
                    ->hidden(fn ($get) => (bool) $get('sin_plan_publicado'))
                    ->schema([
                        Forms\Components\Repeater::make('ejes_transversales')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('key')
                                            ->label('Clave Única')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('eje_transversal_1_igualdad')
                                            ->helperText('Identificador único (solo letras minúsculas, números y guiones bajos)')
                                            ->regex('/^[a-z0-9_]+$/')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('orden')
                                            ->label('Orden')
                                            ->numeric()
                                            ->default(fn ($get, $state) => $state ?? 1)
                                            ->minValue(1)
                                            ->helperText('Número de orden para visualización')
                                            ->columnSpan(1),
                                    ]),

                                Forms\Components\TextInput::make('label')
                                    ->label('Título del Eje')
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder('Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('description')
                                    ->label('Descripción Breve')
                                    ->maxLength(255)
                                    ->placeholder('Igualdad de género')
                                    ->helperText('Descripción corta para mostrar como hint en formularios')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Eje Transversal')
                            ->reorderable()
                            ->orderColumn('orden')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Nuevo Eje Transversal')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre del PND')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Período')
                    ->formatStateUsing(fn ($record) => $record->fecha_inicio->format('d/m/Y').' - '.$record->fecha_fin->format('d/m/Y'))
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->sin_plan_publicado ? 'warning' : 'info'),

                Tables\Columns\IconColumn::make('sin_plan_publicado')
                    ->label('Sin PND publicado')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ejes_generales_count')
                    ->label('Ejes Generales')
                    ->formatStateUsing(fn ($record) => count($record->ejes_generales ?? []))
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('ejes_transversales_count')
                    ->label('Ejes Transversales')
                    ->formatStateUsing(fn ($record) => count($record->ejes_transversales ?? []))
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record, Tables\Actions\DeleteAction $action) {
                        $count = $record->estrategies()->count();
                        if ($count > 0) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body("Este PND está siendo utilizado por {$count} estrategia(s). Considera desactivarlo en lugar de eliminarlo.")
                                ->persistent()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_inicio', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanNacionalDesarrollos::route('/'),
            'create' => Pages\CreatePlanNacionalDesarrollo::route('/create'),
            'edit' => Pages\EditPlanNacionalDesarrollo::route('/{record}/edit'),
        ];
    }
}
