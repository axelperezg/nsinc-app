<?php

namespace App\Exports;

use App\Models\Campaign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class CampaignsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Tipos de medios y sus campos correspondientes en la tabla
     */
    private function getMediaTypes(): array
    {
        return [
            'Televisoras' => 'televisoras',
            'Radiodifusoras' => 'radiodifusoras',
            'Cine' => 'cine',
            'Diarios CDMX' => 'decdmx',
            'Diarios Estados' => 'deedos',
            'Diarios Extranjeros' => 'deextr',
            'Revistas' => 'revistas',
            'Medios Complementarios' => 'mediosComplementarios',
            'Medios Digitales' => 'mediosDigitales',
            'Medios Digitales Internet' => 'mediosDigitalesInternet',
            'Pre-Estudios' => 'preEstudios',
            'Post-Estudios' => 'postEstudios',
            'Diseño' => 'disenio',
            'Producción' => 'produccion',
            'Pre-Producción' => 'preProduccion',
            'Post-Producción' => 'postProduccion',
            'Copiado' => 'copiado',
        ];
    }

    /**
     * Retorna la colección expandida con 17 filas por campaña
     */
    public function collection()
    {
        $campaigns = Campaign::with([
            'campaignType',
            'estrategy.institution',
            'versions'
        ])->get();

        $expandedRows = new Collection();
        $mediaTypes = $this->getMediaTypes();

        foreach ($campaigns as $campaign) {
            // Por cada campaña, crear 17 filas (una por cada tipo de medio)
            foreach ($mediaTypes as $mediaName => $mediaField) {
                $expandedRows->push([
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'campaign_type' => $campaign->campaignType ? $campaign->campaignType->name : '',
                    'institution' => $campaign->estrategy && $campaign->estrategy->institution ? $campaign->estrategy->institution->name : '',
                    'institution_name' => $campaign->institution_name ?? '',
                    'tema_especifico' => $campaign->temaEspecifco ?? '',
                    'objetivo_comunicacion' => $campaign->objetivoComuicacion ?? '',
                    'coemisores' => $campaign->coemisores ?? '',
                    'sexo' => is_array($campaign->sexo) ? implode(', ', $campaign->sexo) : $campaign->sexo,
                    'edad' => is_array($campaign->edad) ? implode(', ', $campaign->edad) : $campaign->edad,
                    'poblacion' => is_array($campaign->poblacion) ? implode(', ', $campaign->poblacion) : $campaign->poblacion,
                    'nse' => is_array($campaign->nse) ? implode(', ', $campaign->nse) : $campaign->nse,
                    'caracteristicas' => $campaign->caracEspecific ?? '',
                    'tv_oficial' => $campaign->tv_oficial ? 'Sí' : 'No',
                    'radio_oficial' => $campaign->radio_oficial ? 'Sí' : 'No',
                    'tv_comercial' => $campaign->tv_comercial ? 'Sí' : 'No',
                    'radio_comercial' => $campaign->radio_comercial ? 'Sí' : 'No',
                    'tipo_medio' => $mediaName,
                    'monto' => $campaign->{$mediaField} ?? 0,
                    'num_versiones' => $campaign->versions->count(),
                    'created_at' => $campaign->created_at ? $campaign->created_at->format('d/m/Y H:i:s') : '',
                    'updated_at' => $campaign->updated_at ? $campaign->updated_at->format('d/m/Y H:i:s') : '',
                ]);
            }
        }

        return $expandedRows;
    }

    /**
     * Define los encabezados del archivo Excel
     */
    public function headings(): array
    {
        return [
            'ID Campaña',
            'Nombre de la Campaña',
            'Tipo de Campaña',
            'Institución',
            'Nombre Institución',
            'Tema Específico',
            'Objetivo de Comunicación',
            'Coemisores',
            'Sexo',
            'Edad',
            'Población',
            'NSE',
            'Características Específicas',
            'TV Oficial',
            'Radio Oficial',
            'TV Comercial',
            'Radio Comercial',
            'Tipo de Medio',
            'Monto',
            'Número de Versiones',
            'Fecha de Creación',
            'Última Actualización',
        ];
    }

    /**
     * Aplica estilos a la hoja de Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para el encabezado
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }
}
