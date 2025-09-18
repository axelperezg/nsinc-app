<?php

namespace App\Http\Controllers;

use App\Models\OficioDgncDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OficioDgncController extends Controller
{
    /**
     * Descargar un archivo de oficio DGNC
     */
    public function download(OficioDgncDocument $oficioDgncDocument): BinaryFileResponse
    {
        // Verificar que el archivo existe
        if (!$oficioDgncDocument->fileExists()) {
            abort(404, 'Archivo no encontrado');
        }

        // Obtener la ruta completa del archivo desde el disco local
        $filePath = Storage::disk('local')->path($oficioDgncDocument->archivo_path);

        // Descargar el archivo con el nombre original
        return response()->download(
            $filePath,
            $oficioDgncDocument->archivo_original_name,
            [
                'Content-Type' => $oficioDgncDocument->archivo_mime_type,
            ]
        );
    }
}
