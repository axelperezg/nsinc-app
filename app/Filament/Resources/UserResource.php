<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    public static function shouldRegisterNavigation(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Solo super administradores pueden ver el menú de Usuarios
        return $user->role && $user->role->name === 'super_admin';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Usuario')
                    ->description('Datos básicos del usuario')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Dejar en blanco para mantener la contraseña actual al editar'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Asignación de Rol y Pertenencia')
                    ->description('Define el rol del usuario y su asignación a institución o sector según corresponda')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\Select::make('role_id')
                            ->label('Rol')
                            ->relationship('role', 'display_name')
                            ->preload()
                            ->required()
                            ->live()
                            ->helperText('Selecciona el rol que tendrá el usuario en el sistema')
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Limpiar sector e institución cuando cambia el rol
                                $set('sector_id', null);
                                $set('institution_id', null);
                            }),

                        Forms\Components\Select::make('sector_id')
                            ->label('Sector')
                            ->relationship('sector', 'name')
                            ->searchable()
                            ->preload()
                            ->required(function (callable $get) {
                                $roleId = $get('role_id');
                                if (!$roleId) return false;

                                $role = \App\Models\Role::find($roleId);
                                return $role && $role->name === 'sector_coordinator';
                            })
                            ->visible(function (callable $get) {
                                $roleId = $get('role_id');
                                if (!$roleId) return false;

                                $role = \App\Models\Role::find($roleId);
                                return $role && $role->name === 'sector_coordinator';
                            })
                            ->helperText('Selecciona el sector que coordinará este usuario')
                            ->placeholder('Selecciona un sector'),

                        Forms\Components\Select::make('institution_id')
                            ->label('Institución')
                            ->relationship('institution', 'name')
                            ->searchable()
                            ->preload()
                            ->required(function (callable $get) {
                                $roleId = $get('role_id');
                                if (!$roleId) return false;

                                $role = \App\Models\Role::find($roleId);
                                return $role && $role->name === 'institution_user';
                            })
                            ->visible(function (callable $get) {
                                $roleId = $get('role_id');
                                if (!$roleId) return false;

                                $role = \App\Models\Role::find($roleId);
                                return $role && $role->name === 'institution_user';
                            })
                            ->helperText('Selecciona la institución a la que pertenece este usuario')
                            ->placeholder('Selecciona una institución'),

                        Forms\Components\Placeholder::make('info_super_admin')
                            ->label('')
                            ->content('Los usuarios con rol Super Administrador y Usuario DGNC tienen acceso a todas las instituciones y sectores del sistema.')
                            ->visible(function (callable $get) {
                                $roleId = $get('role_id');
                                if (!$roleId) return false;

                                $role = \App\Models\Role::find($roleId);
                                return $role && in_array($role->name, ['super_admin', 'dgnc_user']);
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Institución')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sector.name')
                    ->label('Sector')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.display_name')
                    ->label('Rol')
                    ->searchable()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
