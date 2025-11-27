<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Base\BaseEstrategyResource;
use App\Filament\Resources\ComunicacionSocialResource\Pages;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Components\Wizard;

class ComunicacionSocialResource extends BaseEstrategyResource
{
    protected static string $partidaPresupuestal = '36101';

    protected static string $tituloPDF = 'COMUNICACIÓN SOCIAL';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Comunicación Social';

    protected static ?string $modelLabel = 'Estrategia de Comunicación Social';

    protected static ?string $pluralModelLabel = 'Estrategias de Comunicación Social';

    protected static ?string $navigationGroup = 'Partida 36101';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super admin, usuarios de institución, coordinadores de sector y usuarios DGNC pueden ver
        return $user->role && in_array($user->role->name, [
            'super_admin',
            'institution_user',
            'sector_coordinator',
            'dgnc_user'
        ]);
    }

    /**
     * Step específico del Plan Nacional de Desarrollo para Comunicación Social
     */
    protected static function getEspecificosWizardStep(): ?Wizard\Step
    {
        return Wizard\Step::make('Plan Nacional de Desarrollo')
            ->description('Ejes estratégicos relacionados')
            ->icon('heroicon-o-flag')
            ->completedIcon('heroicon-o-check-circle')
            ->schema([
                Forms\Components\Section::make('Plan Nacional de Desarrollo')
                    ->description('Selecciona los ejes del Plan Nacional que se relacionan con tu estrategia')
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\Section::make('Ejes Generales')
                            ->description('Selecciona los ejes generales que aplican a tu estrategia de comunicación')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_1_gobernanza')
                                    ->label('Eje General 1: Gobernanza con justicia y participación ciudadana')
                                    ->hint('Fortalecimiento democrático')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia contribuye a fortalecer la gobernanza y participación ciudadana.'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_2_desarrollo')
                                    ->label('Eje General 2: Desarrollo con bienestar y humanismo')
                                    ->hint('Bienestar social')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia contribuye al desarrollo social y bienestar de la población.'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_3_economia')
                                    ->label('Eje General 3: Economía moral y trabajo')
                                    ->hint('Desarrollo económico')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia contribuye al desarrollo económico y generación de empleo.'),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_general_4_sustentable')
                                    ->label('Eje General 4: Desarrollo sustentable')
                                    ->hint('Medio ambiente')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia contribuye a la sustentabilidad y protección ambiental.'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Ejes Transversales')
                            ->description('Selecciona los ejes transversales que aplican a tu estrategia')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_transversal_1_igualdad')
                                    ->label('Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres')
                                    ->hint('Igualdad de género')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia promueve la igualdad de género y derechos de las mujeres.')
                                    ->columnSpan(2),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_transversal_2_innovacion')
                                    ->label('Eje Transversal 2: Innovación pública para el desarrollo tecnológico nacional')
                                    ->hint('Innovación tecnológica')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia incorpora innovación y desarrollo tecnológico.')
                                    ->columnSpan(2),
                                Forms\Components\Checkbox::make('ejes_plan_nacional.eje_transversal_3_derechos')
                                    ->label('Eje Transversal 3: Derechos de los pueblos y comunidades indígenas y afromexicanas')
                                    ->hint('Pueblos originarios')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText('Marca si tu estrategia incluye a pueblos y comunidades indígenas y afromexicanas.')
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComunicacionSocial::route('/'),
            'create' => Pages\CreateComunicacionSocial::route('/create'),
            'view' => Pages\ViewComunicacionSocial::route('/{record}'),
            'edit' => Pages\EditComunicacionSocial::route('/{record}/edit'),
            'modificar' => Pages\ModificarComunicacionSocial::route('/{record}/modificar'),
            'solventar' => Pages\SolventarComunicacionSocial::route('/{record}/solventar'),
            'cancelar' => Pages\CancelarComunicacionSocial::route('/{record}/cancelar'),
        ];
    }
}
