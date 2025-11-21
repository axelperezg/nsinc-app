<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Models\Campaign;
use App\Exports\CampaignsExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Campañas';

    protected static ?string $modelLabel = 'Campaña';

    protected static ?string $pluralModelLabel = 'Campañas';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Solo super administradores pueden ver el menú de Campañas
        return $user->role && $user->role->name === 'super_admin';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Campaña')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('temaEspecifco')
                            ->label('Tema Específico')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('objetivoComuicacion')
                            ->label('Objetivo de Comunicación')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\Select::make('campaign_type_id')
                            ->label('Tipo de Campaña')
                            ->relationship('campaignType', 'name')
                            ->required(),
                        Forms\Components\Select::make('estrategy.institution_id')
                            ->label('Institución')
                            ->relationship('estrategy.institution', 'name')
                            ->default(function () {
                                $user = Auth::user();
                                return $user && $user->role && $user->role->name !== 'super_admin' ? $user->institution_id : null;
                            })
                            ->disabled(fn () => Auth::user() && Auth::user()->role && Auth::user()->role->name !== 'super_admin')
                            ->required(),
                        
                    ])
                    ->columns(2),

                    Forms\Components\Section::make('Versiones')
                    ->schema([
                        Forms\Components\Repeater::make('versions')
                            ->label('Versiones de la Campaña')
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

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('coemisores')
                            ->label('Coemisores')
                            ->maxLength(65535),
                            Forms\Components\Select::make('sexo')
                            ->label('Sexo')
                            ->multiple()
                            ->options(Campaign::getSexoOptions())
                            ->required(),
                        Forms\Components\Select::make('edad')
                            ->label('Edad')
                            ->multiple()
                            ->options(Campaign::getEdadOptions())
                            ->required(),
                        Forms\Components\Select::make('poblacion')
                            ->label('Población')
                            ->multiple()
                            ->options(Campaign::getPoblacionOptions())
                            ->required(),
                        Forms\Components\Select::make('nse')
                            ->label('NSE')
                            ->multiple()
                            ->options(Campaign::getNseOptions())
                            ->required(),
                        Forms\Components\Textarea::make('caracEspecific')
                            ->label('Características Específicas')
                            ->maxLength(65535),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Medios Oficiales')
                    ->schema([
                        Forms\Components\Checkbox::make('tv_oficial')
                            ->label('TV Oficial'),
                        Forms\Components\Checkbox::make('radio_oficial')
                            ->label('Radio Oficial'),
                        Forms\Components\Checkbox::make('tv_comercial')
                            ->label('TV Comercial'),
                        Forms\Components\Checkbox::make('radio_comercial')
                            ->label('Radio Comercial'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Presupuestos')
                    ->schema([
                        Forms\Components\TextInput::make('televisoras')
                            ->label('Televisoras')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('radiodifusoras')
                            ->label('Radiodifusoras')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('cine')
                            ->label('Cine')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('decdmx')
                            ->label('DECDMX')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('deedos')
                            ->label('DEEDOS')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('deextr')
                            ->label('DEEXTR')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('revistas')
                            ->label('Revistas')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('mediosComplementarios')
                            ->label('Medios Complementarios')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('mediosDigitales')
                            ->label('Medios Digitales')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('preEstudios')
                            ->label('Pre Estudios')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('postEstudios')
                            ->label('Post Estudios')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('disenio')
                            ->label('Diseño')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('produccion')
                            ->label('Producción')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('preProduccion')
                            ->label('Pre Producción')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('postProduccion')
                            ->label('Post Producción')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('copiado')
                            ->label('Copiado')
                            ->numeric()
                            ->step(0.01),
                    ])
                    ->columns(4),

               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                
                if ($user && $user->role && $user->role->name !== 'super_admin' && $user->institution_id) {
                    $query->whereHas('estrategy', function ($q) use ($user) {
                        $q->where('institution_id', $user->institution_id);
                    });
                }
                // Si es super admin, no se aplica filtro
                
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('campaignType.name')
                    ->label('Tipo de Campaña')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estrategy.institution.name')
                    ->label('Institución')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => Auth::user() && Auth::user()->role && Auth::user()->role->name === 'super_admin'),
                Tables\Columns\TextColumn::make('temaEspecifco')
                    ->label('Tema Específico')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('tv_oficial')
                    ->label('TV Oficial')
                    ->boolean(),
                Tables\Columns\IconColumn::make('radio_oficial')
                    ->label('Radio Oficial')
                    ->boolean(),
                Tables\Columns\TextColumn::make('versions_count')
                    ->label('Versiones')
                    ->counts('versions'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('campaign_type_id')
                    ->label('Tipo de Campaña')
                    ->relationship('campaignType', 'name'),
                Tables\Filters\SelectFilter::make('estrategy.institution_id')
                    ->label('Institución')
                    ->relationship('estrategy.institution', 'name')
                    ->visible(fn () => Auth::user() && Auth::user()->role && Auth::user()->role->name === 'super_admin'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportar_excel')
                    ->label('Exportar a Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new CampaignsExport, 'campaigns_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
                    })
                    ->tooltip('Descargar todas las campañas en formato Excel'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
