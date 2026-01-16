<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Base\BaseEstrategyResource;
use App\Filament\Resources\ComunicacionSocialResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Auth;

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

        if (! $user) {
            return false;
        }

        // Super admin, usuarios de institución, coordinadores de sector y usuarios DGNC pueden ver
        return $user->role && in_array($user->role->name, [
            'super_admin',
            'institution_user',
            'sector_coordinator',
            'dgnc_user',
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
                // Campo oculto para guardar referencia al PND
                Forms\Components\Hidden::make('plan_nacional_desarrollo_id')
                    ->default(function ($record) {
                        // Si está editando una estrategia existente, usar su PND
                        if ($record && $record->plan_nacional_desarrollo_id) {
                            return $record->plan_nacional_desarrollo_id;
                        }
                        // Para nuevas estrategias, obtener el PND activo
                        $pnd = \App\Models\PlanNacionalDesarrollo::getActive();

                        return $pnd?->id;
                    })
                    ->dehydrated(true),

                Forms\Components\Section::make('Plan Nacional de Desarrollo')
                    ->description(function ($record) {
                        // Obtener PND (del record o el activo)
                        $pndId = $record?->plan_nacional_desarrollo_id
                            ?? \App\Models\PlanNacionalDesarrollo::getActive()?->id;

                        if (! $pndId) {
                            return 'El Plan Nacional de Desarrollo se habilitará cuando este sea publicado';
                        }

                        $pnd = \App\Models\PlanNacionalDesarrollo::find($pndId);

                        return $pnd
                            ? "Selecciona los ejes del {$pnd->nombre} que se relacionan con tu estrategia"
                            : 'Selecciona los ejes del Plan Nacional que se relacionan con tu estrategia';
                    })
                    ->icon('heroicon-o-flag')
                    ->schema(function ($record) {
                        // Obtener PND
                        $pndId = $record?->plan_nacional_desarrollo_id
                            ?? \App\Models\PlanNacionalDesarrollo::getActive()?->id;

                        if (! $pndId) {
                            // PND inactivo - mostrar mensaje
                            return [
                                Forms\Components\Placeholder::make('pnd_inactive')
                                    ->label('')
                                    ->content('El Plan Nacional de Desarrollo se habilitará cuando este sea publicado')
                                    ->extraAttributes(['class' => 'text-center italic text-gray-500']),
                            ];
                        }

                        $pnd = \App\Models\PlanNacionalDesarrollo::find($pndId);

                        if (! $pnd) {
                            return [];
                        }

                        $schema = [];

                        // Generar sección de Ejes Generales
                        if (! empty($pnd->ejes_generales)) {
                            $ejesGeneralesFields = [];
                            foreach ($pnd->ejes_generales as $eje) {
                                $description = $eje['description'] ?? 'este eje';
                                $ejesGeneralesFields[] = Forms\Components\Checkbox::make("ejes_plan_nacional.{$eje['key']}")
                                    ->label($eje['label'])
                                    ->hint($eje['description'] ?? '')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText("Marca si tu estrategia contribuye a {$description}.");
                            }

                            $schema[] = Forms\Components\Section::make($pnd->nombre_ejes_generales ?? 'Ejes Generales')
                                ->description('Selecciona los ejes que aplican a tu estrategia de comunicación')
                                ->icon('heroicon-o-chart-bar')
                                ->schema($ejesGeneralesFields)
                                ->columns(2)
                                ->collapsible();
                        }

                        // Generar sección de Ejes Transversales
                        if (! empty($pnd->ejes_transversales)) {
                            $ejesTransversalesFields = [];
                            foreach ($pnd->ejes_transversales as $eje) {
                                $description = $eje['description'] ?? 'este eje';
                                $ejesTransversalesFields[] = Forms\Components\Checkbox::make("ejes_plan_nacional.{$eje['key']}")
                                    ->label($eje['label'])
                                    ->hint($eje['description'] ?? '')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->helperText("Marca si tu estrategia promueve {$description}.")
                                    ->columnSpan(2);
                            }

                            $schema[] = Forms\Components\Section::make($pnd->nombre_ejes_transversales ?? 'Ejes Transversales')
                                ->description('Selecciona los ejes que aplican a tu estrategia')
                                ->icon('heroicon-o-arrow-path')
                                ->schema($ejesTransversalesFields)
                                ->columns(2)
                                ->collapsible();
                        }

                        return $schema;
                    })
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Programas y Objetivos')
                    ->description('Describe los programas y objetivos relacionados con esta estrategia')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('programa_sectorial_especial')
                            ->label('Programa Sectorial y/o Especial')
                            ->required()
                            ->rows(4)
                            ->maxLength(65535)
                            ->hint('Campo obligatorio para Comunicación Social')
                            ->hintIcon('heroicon-o-information-circle')
                            ->helperText('Describe el programa sectorial y/o especial relacionado con esta estrategia.')
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('objetivos_estrategicos_transversales')
                            ->label('Objetivos Estratégicos y/o Transversales')
                            ->required()
                            ->rows(4)
                            ->maxLength(65535)
                            ->hint('Campo obligatorio para Comunicación Social')
                            ->hintIcon('heroicon-o-information-circle')
                            ->helperText('Describe los objetivos estratégicos y/o transversales de esta estrategia.')
                            ->columnSpan(2),
                    ])
                    ->columns(2)
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
