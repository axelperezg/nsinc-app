<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitutionResource\Pages;
use App\Filament\Resources\InstitutionResource\RelationManagers;
use App\Models\Institution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Instituciones';

    protected static ?string $modelLabel = 'Institución';

    protected static ?string $pluralModelLabel = 'Instituciones';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    public static function shouldRegisterNavigation(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Solo super administradores pueden ver el menú de Instituciones
        return $user->role && $user->role->name === 'super_admin';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sector_id')
                    ->label('Sector')
                    ->relationship('sector', 'name')
                    ->required()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('acronym')
                    ->label('Acrónimo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('juridical_nature_id')
                    ->label('Naturaleza Jurídica')
                    ->relationship('juridicalNature', 'name')
                    ->required()
                    ->preload(),
                Forms\Components\Toggle::make('isSector')
                    ->label('Es Sector')
                    ->default(false),
                Forms\Components\Select::make('ramo_id')
                    ->label('Ramo')
                    ->relationship('ramo', 'name')
                    ->preload(),
                Forms\Components\Select::make('control')
                    ->label('Control')
                    ->options([
                        'Normal' => 'Normal',
                        'Directo' => 'Directo',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sector.name')
                    ->label('Sector')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('acronym')
                    ->label('Acrónimo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('juridicalNature.name')
                    ->label('Naturaleza Jurídica')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ramo.name')
                    ->label('Ramo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
}
