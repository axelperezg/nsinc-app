<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Base\BaseEstrategyResource;
use App\Filament\Resources\PromocionPublicidadResource\Pages;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Components\Wizard;

class PromocionPublicidadResource extends BaseEstrategyResource
{
    protected static string $partidaPresupuestal = '36201';

    protected static string $tituloPDF = 'PROMOCIÓN Y PUBLICIDAD';

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationLabel = 'Promoción y Publicidad';

    protected static ?string $modelLabel = 'Estrategia de Promoción y Publicidad';

    protected static ?string $pluralModelLabel = 'Estrategias de Promoción y Publicidad';

    protected static ?string $navigationGroup = 'Partida 36201';

    protected static ?int $navigationSort = 2;

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
     * Step específico de Entorno de Mercado y Metas para Promoción y Publicidad
     */
    protected static function getEspecificosWizardStep(): ?Wizard\Step
    {
        return Wizard\Step::make('Entorno y Metas')
            ->description('Contexto y objetivos específicos')
            ->icon('heroicon-o-chart-bar')
            ->completedIcon('heroicon-o-check-circle')
            ->schema([
                Forms\Components\Section::make('Análisis de Entorno y Metas')
                    ->description('Define el contexto del mercado y las metas generales de la estrategia')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Textarea::make('entorno_mercado')
                            ->label('Entorno de Mercado')
                            ->required()
                            ->rows(8)
                            ->hint('Análisis del contexto')
                            ->hintIcon('heroicon-o-information-circle')
                            ->helperText('Describe el entorno del mercado en el que se desarrollará la estrategia de promoción y publicidad. Incluye análisis de competencia, tendencias del sector, oportunidades y amenazas.')
                            ->placeholder('Ejemplo: El mercado actual presenta una alta competencia en el sector digital, con un crecimiento del 25% en la inversión en medios digitales. Las principales tendencias incluyen...'),

                        Forms\Components\Textarea::make('metas_generales')
                            ->label('Metas Generales')
                            ->required()
                            ->rows(8)
                            ->hint('Objetivos cuantificables')
                            ->hintIcon('heroicon-o-information-circle')
                            ->helperText('Especifica las metas generales que se buscan alcanzar con esta estrategia. Deben ser medibles y específicas.')
                            ->placeholder('Ejemplo: Incrementar el conocimiento de marca en un 40% en el público objetivo. Generar 10,000 leads calificados. Alcanzar 2 millones de impresiones en medios digitales...'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromocionPublicidad::route('/'),
            'create' => Pages\CreatePromocionPublicidad::route('/create'),
            'view' => Pages\ViewPromocionPublicidad::route('/{record}'),
            'edit' => Pages\EditPromocionPublicidad::route('/{record}/edit'),
            'modificar' => Pages\ModificarPromocionPublicidad::route('/{record}/modificar'),
            'solventar' => Pages\SolventarPromocionPublicidad::route('/{record}/solventar'),
            'cancelar' => Pages\CancelarPromocionPublicidad::route('/{record}/cancelar'),
        ];
    }
}
