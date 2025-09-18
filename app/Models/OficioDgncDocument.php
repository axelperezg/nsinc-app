<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OficioDgncDocument extends Model
{
    protected $fillable = [
        'estrategy_id',
        'numero_oficio',
        'fecha_oficio',
        'nombre_archivo',
        'descripcion_documento',
        'archivo_path',
        'archivo_original_name',
        'archivo_mime_type',
        'archivo_size',
    ];

    protected $casts = [
        'fecha_oficio' => 'date',
        'archivo_size' => 'integer',
    ];

    /**
     * Obtiene la estrategia a la que pertenece este documento
     */
    public function estategy(): BelongsTo
    {
        return $this->belongsTo(Estrategy::class);
    }

    /**
     * Obtiene el tamaño del archivo formateado
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->archivo_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Verifica si el archivo existe en el sistema de archivos
     */
    public function fileExists(): bool
    {
        return Storage::disk('local')->exists($this->archivo_path);
    }

    /**
     * Obtiene la URL pública del archivo
     */
    public function getFileUrl(): string
    {
        return route('oficio-dgnc.download', $this->id);
    }
}
