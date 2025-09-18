<?php

namespace App\Filament\Resources\EstrategyResource\Actions;

use App\Models\OficioDgncDocument;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CargarOficioDgncAction
{
    public static function make(): Action
    {
        return Action::make('cargar_oficio_dgnc')
            ->label('Cargar Oficio DGNC')
            ->icon('heroicon-o-document-plus')
            ->color('success')
            ->visible(function () {
                // Solo usuarios con rol dgnc_user pueden ver esta acción
                $user = Auth::user();
                return $user && $user->role && $user->role->name === 'dgnc_user';
            })
            ->form([
                Forms\Components\TextInput::make('numero_oficio')
                    ->label('Número de Oficio')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: OFICIO-DGNC-2025-001'),
                
                Forms\Components\DatePicker::make('fecha_oficio')
                    ->label('Fecha de Oficio')
                    ->required()
                    ->default(now()),
                
                Forms\Components\FileUpload::make('archivo_pdf')
                    ->label('Archivo PDF')
                    ->required()
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240) // 10MB máximo
                    ->directory('oficios-dgnc')
                    ->disk('local') // Usar disco local para archivos privados
                    ->visibility('private')
                    ->storeFileNamesIn('archivo_original_name'),
            ])
            ->action(function (array $data, $record) {
                try {
                    // Validar que el archivo se haya subido correctamente
                    if (empty($data['archivo_pdf'])) {
                        throw new \Exception('No se ha seleccionado ningún archivo PDF.');
                    }
                    
                    // Obtener información del archivo subido
                    $archivo = $data['archivo_pdf'];
                    
                    // Verificar que el archivo existe en el almacenamiento
                    if (!Storage::disk('local')->exists($archivo)) {
                        throw new \Exception('El archivo no se encontró en el almacenamiento.');
                    }
                    
                    // Obtener el tamaño del archivo
                    $archivoSize = Storage::disk('local')->size($archivo);
                    
                    // Generar nombre del archivo basado en el número de oficio
                    $nombreArchivo = $data['numero_oficio'] . '.pdf';
                    
                    // Crear el documento en la base de datos
                    $documento = OficioDgncDocument::create([
                        'estrategy_id' => $record->id,
                        'numero_oficio' => $data['numero_oficio'],
                        'fecha_oficio' => $data['fecha_oficio'],
                        'nombre_archivo' => $nombreArchivo,
                        'descripcion_documento' => null, // Ya no se usa
                        'archivo_path' => $archivo,
                        'archivo_original_name' => $data['archivo_original_name'] ?? basename($archivo),
                        'archivo_mime_type' => 'application/pdf',
                        'archivo_size' => $archivoSize,
                    ]);
                    
                    Notification::make()
                        ->title('Oficio DGNC Cargado')
                        ->body("El oficio {$data['numero_oficio']} ha sido cargado exitosamente.")
                        ->success()
                        ->send();
                        
                } catch (\Exception $e) {
                    // Log del error para debugging
                    Log::error('Error al cargar oficio DGNC: ' . $e->getMessage(), [
                        'data' => $data,
                        'record_id' => $record->id ?? null,
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    Notification::make()
                        ->title('Error al Cargar Oficio')
                        ->body('Hubo un error al cargar el oficio DGNC: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->modalHeading('Cargar Oficio DGNC')
            ->modalDescription('Sube un nuevo documento de oficio DGNC para esta estrategia.')
            ->modalSubmitActionLabel('Cargar Documento')
            ->modalCancelActionLabel('Cancelar');
    }
}
