<?php

namespace App\Filament\Resources\EstrategyResource\Actions;

use App\Models\OficioDgncDocument;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VerOficiosDgncAction
{
    public static function make(): Action
    {
        return Action::make('ver_oficios_dgnc')
            ->label('Ver Oficios DGNC')
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->form(function ($record) {
                $documentos = $record->oficioDgncDocuments;
                
                if ($documentos->isEmpty()) {
                    return [
                        Forms\Components\Section::make('Sin Oficio')
                            ->schema([
                                Forms\Components\Placeholder::make('sin_oficio')
                                    ->label('')
                                    ->content('No se han cargado oficios DGNC para esta estrategia.')
                                    ->extraAttributes(['class' => 'text-center text-gray-500 py-8']),
                            ])
                            ->columns(1),
                    ];
                }
                
                $sections = [];
                
                foreach ($documentos as $index => $documento) {
                    $sections[] = Forms\Components\Section::make($documento->numero_oficio)
                        ->schema([
                            Forms\Components\TextInput::make("documento_{$index}_numero")
                                ->label('Número de Oficio')
                                ->default($documento->numero_oficio)
                                ->disabled(),
                            
                            Forms\Components\TextInput::make("documento_{$index}_fecha")
                                ->label('Fecha de Oficio')
                                ->default($documento->fecha_oficio->format('d/m/Y'))
                                ->disabled(),
                            
                            Forms\Components\TextInput::make("documento_{$index}_tamaño")
                                ->label('Tamaño del Archivo')
                                ->default($documento->formatted_file_size)
                                ->disabled(),
                            
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make("descargar_{$index}")
                                    ->label('Descargar')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('success')
                                    ->size('sm')
                                    ->url(route('oficio-dgnc.download', $documento->id))
                                    ->openUrlInNewTab(false)
                                    ->extraAttributes([
                                        'target' => '_self',
                                        'download' => $documento->archivo_original_name
                                    ]),
                                
                                Forms\Components\Actions\Action::make("borrar_{$index}")
                                    ->label('Borrar')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->size('sm')
                                    ->requiresConfirmation()
                                    ->modalHeading('Confirmar Eliminación')
                                    ->modalDescription("¿Estás seguro de que deseas eliminar el oficio '{$documento->numero_oficio}'? Esta acción no se puede deshacer.")
                                    ->modalSubmitActionLabel('Sí, Eliminar')
                                    ->modalCancelActionLabel('Cancelar')
                                    ->action(function () use ($documento) {
                                        try {
                                            // Eliminar el archivo del almacenamiento
                                            if (Storage::disk('local')->exists($documento->archivo_path)) {
                                                Storage::disk('local')->delete($documento->archivo_path);
                                            }
                                            
                                            // Eliminar el registro de la base de datos
                                            $documento->delete();
                                            
                                            Notification::make()
                                                ->title('Oficio Eliminado')
                                                ->body("El oficio '{$documento->numero_oficio}' ha sido eliminado exitosamente.")
                                                ->success()
                                                ->send();
                                                
                                        } catch (\Exception $e) {
                                            Notification::make()
                                                ->title('Error al Eliminar')
                                                ->body('Hubo un error al eliminar el oficio. Por favor, inténtalo de nuevo.')
                                                ->danger()
                                                ->send();
                                        }
                                    }),
                            ])
                            ->alignment('center')
                            ->fullWidth(false),
                        ])
                        ->columns(1)
                        ->collapsible();
                }
                
                return [
                    Forms\Components\Section::make('Oficios DGNC Cargados')
                        ->schema($sections)
                        ->columns(1),
                ];
            })
            ->modalHeading(function ($record) {
                $documentos = $record->oficioDgncDocuments;
                if ($documentos->isEmpty()) {
                    return 'Oficios DGNC - Sin Oficio';
                }
                return 'Oficios DGNC Cargados';
            })
            ->modalWidth('4xl')
            ->modalSubmitActionLabel('Cerrar')
            ->modalCancelAction(false);
    }
}
