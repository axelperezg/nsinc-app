<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigurationResource\Pages;
use App\Filament\Resources\ConfigurationResource\RelationManagers;
use App\Models\Configuration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConfigurationResource extends Resource
{
    protected static ?string $model = Configuration::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configuraciones';

    protected static ?string $modelLabel = 'Configuración';

    protected static ?string $pluralModelLabel = 'Configuraciones';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('key')
                            ->label('Clave')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Identificador único de la configuración'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(2)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Valor')
                    ->schema([
                        // Toggle para valores booleanos
                        Forms\Components\Toggle::make('value')
                            ->label('Activado')
                            ->visible(fn ($record) => $record && isset($record->type) && $record->type === 'boolean')
                            ->default(false)
                            ->formatStateUsing(fn ($state) => filter_var($state, FILTER_VALIDATE_BOOLEAN))
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                        // FileUpload para logo PDF izquierdo
                        Forms\Components\FileUpload::make('value')
                            ->label('Logo Izquierdo')
                            ->visible(fn ($record) => $record && isset($record->key) && $record->key === 'pdf.logo_path')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public')
                            ->imagePreviewHeight('150')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                            ->helperText('Formatos permitidos: PNG, JPG. Tamaño máximo: 2MB. Se recomienda usar fondo transparente.')
                            ->downloadable()
                            ->deletable(true),

                        // FileUpload para logo PDF derecho
                        Forms\Components\FileUpload::make('value')
                            ->label('Logo Derecho')
                            ->visible(fn ($record) => $record && isset($record->key) && $record->key === 'pdf.logo_right_path')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public')
                            ->imagePreviewHeight('150')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                            ->helperText('Formatos permitidos: PNG, JPG. Tamaño máximo: 2MB. Se recomienda usar fondo transparente.')
                            ->downloadable()
                            ->deletable(true),

                        // TextInput para otros tipos de string
                        Forms\Components\TextInput::make('value')
                            ->label('Valor')
                            ->visible(fn ($record) => $record && isset($record->type) && isset($record->key) && $record->type === 'string' && !in_array($record->key, ['pdf.logo_path', 'pdf.logo_right_path']))
                            ->maxLength(255),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Grupo')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'widgets' => 'info',
                        'pdf' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Configuración')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description)
                    ->wrap(),

                Tables\Columns\IconColumn::make('value')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => filter_var($record->value, FILTER_VALIDATE_BOOLEAN))
                    ->visible(fn ($record) => isset($record->type) && $record->type === 'boolean'),

                Tables\Columns\ImageColumn::make('value')
                    ->label('Logo')
                    ->disk('public')
                    ->height(50)
                    ->visible(fn ($record) => isset($record->key) && in_array($record->key, ['pdf.logo_path', 'pdf.logo_right_path']))
                    ->defaultImageUrl(fn () => null),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->limit(50)
                    ->visible(fn ($record) => isset($record->type) && isset($record->key) && $record->type === 'string' && !in_array($record->key, ['pdf.logo_path', 'pdf.logo_right_path']))
                    ->placeholder('No configurado')
                    ->wrap(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última modificación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('group')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Grupo')
                    ->options([
                        'general' => 'General',
                        'widgets' => 'Widgets',
                        'pdf' => 'PDF',
                        'notifications' => 'Notificaciones',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                // Sin acciones bulk para evitar eliminaciones accidentales
            ])
            ->emptyStateHeading('No hay configuraciones')
            ->emptyStateDescription('Las configuraciones se crean automáticamente.');
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
            'index' => Pages\ListConfigurations::route('/'),
            'edit' => Pages\EditConfiguration::route('/{record}/edit'),
        ];
    }

    /**
     * Solo super_admin puede ver las configuraciones
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->role && $user->role->name === 'super_admin';
    }

    /**
     * No permitir crear configuraciones desde la UI
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * No permitir eliminar configuraciones
     */
    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * No permitir eliminar múltiples configuraciones
     */
    public static function canDeleteAny(): bool
    {
        return false;
    }
}
