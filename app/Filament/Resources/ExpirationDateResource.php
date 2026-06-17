<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpirationDateResource\Pages;
use App\Filament\Resources\ExpirationDateResource\RelationManagers;
use App\Models\ExpirationDate;
use App\Models\Institution;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpirationDateResource extends Resource
{
    protected static ?string $model = ExpirationDate::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Fechas de Vencimiento';

    protected static ?string $modelLabel = 'Fecha de Vencimiento';

    protected static ?string $pluralModelLabel = 'Fechas de Vencimiento';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    public static function shouldRegisterNavigation(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && $user->role && $user->role->name === 'super_admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('anio')
                    ->label('Año')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('concept')
                    ->label('Concepto')
                    ->options([
                        'Registro'     => 'Registro',
                        'Modificación' => 'Modificación',
                        'Observación'  => 'Observación',
                        'Cancelación'  => 'Cancelación',
                    ])
                    ->required(),
                Forms\Components\Select::make('target_role')
                    ->label('Aplica a')
                    ->helperText('Define qué rol debe respetar este plazo. Vacío = aplica a todos.')
                    ->options([
                        'institution_user'  => 'Dependencias (Usuarios de Institución)',
                        'sector_coordinator' => 'Coordinadoras de Sector',
                    ])
                    ->nullable()
                    ->live(),
                Forms\Components\DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_diaPrevio')
                    ->label('Fecha Día Previo')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_limite')
                    ->label('Fecha Límite')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_restrictiva')
                    ->label('Fecha Restrictiva')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('exempted_institution_ids')
                    ->label('Instituciones Exceptuadas')
                    ->helperText('Estas instituciones pueden operar aunque la fecha haya vencido.')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => Institution::orderBy('name')->pluck('name', 'id')->toArray())
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('target_role'), [null, '', 'institution_user'])),
                Forms\Components\Select::make('exempted_coordinator_ids')
                    ->label('Coordinadoras de Sector Exceptuadas')
                    ->helperText('Estas coordinadoras pueden operar aunque la fecha haya vencido.')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => User::whereHas('role', fn ($q) => $q->where('name', 'sector_coordinator'))
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn ($u) => [$u->id => "{$u->name} ({$u->email})"])
                        ->toArray())
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('target_role'), [null, '', 'sector_coordinator'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anio')
                    ->label('Año')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('concept')
                    ->label('Concepto')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('target_role')
                    ->label('Aplica a')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'institution_user'   => 'Dependencias',
                        'sector_coordinator' => 'Coordinadoras CS',
                        default              => 'Todos',
                    })
                    ->colors([
                        'info'    => 'institution_user',
                        'warning' => 'sector_coordinator',
                        'gray'    => fn ($state) => $state === null,
                    ]),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(60)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_limite')
                    ->label('Fecha Límite')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpirationDates::route('/'),
            'create' => Pages\CreateExpirationDate::route('/create'),
            'edit'   => Pages\EditExpirationDate::route('/{record}/edit'),
        ];
    }
}
