<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpirationDateResource\Pages;
use App\Filament\Resources\ExpirationDateResource\RelationManagers;
use App\Models\ExpirationDate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\Select::make('concept')
                    ->label('Concepto')
                    ->options([
                       'Registro' => 'Registro',
                        'Modificación' => 'Modificación',
                        'Observación' => 'Observación',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(65535)
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
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
                //
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
            'index' => Pages\ListExpirationDates::route('/'),
            'create' => Pages\CreateExpirationDate::route('/create'),
            'edit' => Pages\EditExpirationDate::route('/{record}/edit'),
        ];
    }
}
