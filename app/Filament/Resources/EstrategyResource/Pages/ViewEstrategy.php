<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use Filament\Resources\Pages\ViewRecord;
//use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ViewEstrategy extends ViewRecord
{
    protected static string $resource = EstrategyResource::class;

    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Section::make('Información General')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('anio')
                        ->label('Año')
                        ->disabled(),
                    \Filament\Forms\Components\TextInput::make('institution.sector.name')
                        ->label('Sector')
                        ->disabled(),
                    \Filament\Forms\Components\TextInput::make('institution.name')
                        ->label('Institución')
                        ->disabled(),
                    \Filament\Forms\Components\TextInput::make('estado_estrategia')
                        ->label('Estado')
                        ->disabled(),
                    \Filament\Forms\Components\TextInput::make('presupuesto')
                        ->label('Presupuesto')
                        ->prefix('$')
                        ->disabled(),
                    \Filament\Forms\Components\TextInput::make('responsable')
                        ->label('Responsable')
                        ->disabled(),
                    \Filament\Forms\Components\Textarea::make('objetivo')
                        ->label('Objetivo')
                        ->disabled(),
                    \Filament\Forms\Components\Textarea::make('justificacion')
                        ->label('Justificación')
                        ->disabled(),
                ])
                ->columns(2),

            \Filament\Forms\Components\Section::make('Campañas')
                ->schema([
                    \Filament\Forms\Components\Repeater::make('campaigns')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('nombre_campana')
                                ->label('Nombre de Campaña')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('objetivo_campana')
                                ->label('Objetivo de Campaña')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('publico_objetivo')
                                ->label('Público Objetivo')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('televisoras')
                                ->label('Televisoras')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('radiodifusoras')
                                ->label('Radiodifusoras')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('cine')
                                ->label('Cine')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('prensa')
                                ->label('Prensa')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('revistas')
                                ->label('Revistas')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('exterior')
                                ->label('Exterior')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('internet')
                                ->label('Internet')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('redes_sociales')
                                ->label('Redes Sociales')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('eventos')
                                ->label('Eventos')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('copiado')
                                ->label('Copiado')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('produccion')
                                ->label('Producción')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('medios_complementarios')
                                ->label('Medios Complementarios')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('otros')
                                ->label('Otros')
                                ->prefix('$')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('total_campana')
                                ->label('Total Campaña')
                                ->prefix('$')
                                ->disabled(),
                        ])
                        ->columns(3)
                        ->disabled()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['nombre_campana'] ?? null),
                ]),
        ];
    }



    // protected function getHeaderActions(): array
    // {
    //     $actions = [];
    //     $user = Auth::user();

    //     if (!$user || !$user->role) {
    //         return $actions;
    //     }

    //     // Solo super admin puede eliminar
    //     if ($user->role->name === 'super_admin') {
    //         $actions[] = Action::make('delete')
    //             ->label('Eliminar')
    //             ->icon('heroicon-o-trash')
    //             ->color('danger')
    //             ->requiresConfirmation()
    //             ->modalHeading('Eliminar Estrategia')
    //             ->modalDescription('¿Estás seguro de que quieres eliminar esta estrategia? Esta acción no se puede deshacer.')
    //             ->modalSubmitActionLabel('Sí, Eliminar')
    //             ->modalCancelActionLabel('Cancelar')
    //             ->action(fn () => $this->record->delete())
    //             ->redirect(route('filament.admin.resources.estrategies.index'));
    //     }

    //     // Solo usuarios de institución pueden enviar a CS si está en estado 'Creada'
    //     if ($user->role->name === 'institution_user' && 
    //         in_array($this->record->estado_estrategia, ['Creada', 'Rechazada CS', 'Rechazada DGNC'])) {
    //         $actions[] = Action::make('enviar_cs')
    //             ->label('Enviar a CS')
    //             ->icon('heroicon-o-paper-airplane')
    //             ->color('success')
    //             ->requiresConfirmation()
    //             ->modalHeading('Enviar a CS')
    //             ->modalDescription('¿Estás seguro de que quieres enviar esta estrategia a Coordinadora de Sector? Una vez enviada, no podrás editarla hasta que sea evaluada nuevamente.')
    //             ->modalSubmitActionLabel('Sí, Enviar')
    //             ->modalCancelActionLabel('Cancelar')
    //             ->action(function () {
    //                 $this->record->update(['estado_estrategia' => 'Enviado a CS']);
    //                 $this->notify('success', 'Estrategia enviada exitosamente a CS');
    //                 $this->redirect(route('filament.admin.resources.estrategies.index'));
    //             });
    //     }

    //     // Solo coordinadores de sector pueden evaluar estrategias si está en estado 'Enviado a CS'
    //     if ($user->role->name === 'sector_coordinator' && 
    //         $this->record->estado_estrategia === 'Enviado a CS') {
    //         $actions[] = Action::make('evaluar_estrategia')
    //             ->label('Evaluar Estrategia')
    //             ->icon('heroicon-o-clipboard-document-list')
    //             ->color('info')
    //             ->url(route('filament.admin.resources.estrategies.index'))
    //             ->openUrlInNewTab();
    //     }

    //     // Solo usuarios DGNC pueden autorizar si está en estado 'Aceptada CS'
    //     if ($user->role->name === 'dgnc_user' && 
    //         $this->record->estado_estrategia === 'Aceptada CS') {
    //         $actions[] = Action::make('autorizar_dgnc')
    //             ->label('Autorizar DGNC')
    //             ->icon('heroicon-o-shield-check')
    //             ->color('success')
    //             ->requiresConfirmation()
    //             ->modalHeading('Autorizar Estrategia')
    //             ->modalDescription('¿Estás seguro de que quieres autorizar esta estrategia? Una vez autorizada, estará disponible para modificaciones.')
    //             ->modalSubmitActionLabel('Sí, Autorizar')
    //             ->modalCancelActionLabel('Cancelar')
    //             ->action(function () {
    //                 $this->record->update(['estado_estrategia' => 'Autorizada']);
    //                 $this->notify('success', 'Estrategia autorizada exitosamente por DGNC');
    //                 $this->redirect(route('filament.admin.resources.estrategies.index'));
    //             });
    //     }

    //     // Solo mostrar modificar si está autorizada
    //     if ($this->record->estado_estrategia === 'Autorizada') {
    //         $actions[] = Action::make('modificar_estrategia')
    //             ->label('Modificar Estrategia')
    //             ->icon('heroicon-o-document-duplicate')
    //             ->color('warning')
    //             ->url(route('filament.admin.resources.estrategies.modificar', ['record' => $this->record->id]))
    //             ->requiresConfirmation()
    //             ->modalHeading('Modificar Estrategia')
    //             ->modalDescription('¿Estás seguro de que quieres crear una modificación de esta estrategia? Se duplicará con todos sus datos y campañas.')
    //             ->modalSubmitActionLabel('Sí, Modificar')
    //             ->modalCancelActionLabel('Cancelar');
    //     }

    //     return $actions;
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            // Aquí puedes agregar widgets si los necesitas
        ];
    }
}
