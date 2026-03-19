<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RamoResource\Pages;
use App\Filament\Resources\RamoResource\RelationManagers;
use App\Models\Ramo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RamoResource extends Resource
{
    protected static ?string $model = Ramo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Ramos';

    protected static ?string $modelLabel = 'Ramo';

    protected static ?string $pluralModelLabel = 'Ramos';

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
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('institutions_count')
                    ->label('Instituciones')
                    ->counts('institutions')
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
            'index' => Pages\ListRamos::route('/'),
            'create' => Pages\CreateRamo::route('/create'),
            'edit' => Pages\EditRamo::route('/{record}/edit'),
        ];
    }
}
