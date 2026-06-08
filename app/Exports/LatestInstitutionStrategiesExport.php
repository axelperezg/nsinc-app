<?php

namespace App\Exports;

use App\Models\Campaign;
use App\Models\Estrategy;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LatestInstitutionStrategiesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    private function getMediaTypes(): array
    {
        return [
            'Televisoras'              => 'televisoras',
            'Radiodifusoras'           => 'radiodifusoras',
            'Radio Comunitaria'        => 'radio_comunitaria',
            'Cine'                     => 'cine',
            'Diarios CDMX'             => 'decdmx',
            'Diarios Estados'          => 'deedos',
            'Diarios Extranjeros'      => 'deextr',
            'Revistas'                 => 'revistas',
            'Medios Complementarios'   => 'mediosComplementarios',
            'Medios Digitales'         => 'mediosDigitales',
            'Medios Digitales Internet'=> 'mediosDigitalesInternet',
            'Pre-Estudios'             => 'preEstudios',
            'Post-Estudios'            => 'postEstudios',
            'Diseño'                   => 'disenio',
            'Producción'               => 'produccion',
            'Pre-Producción'           => 'preProduccion',
            'Post-Producción'          => 'postProduccion',
            'Copiado'                  => 'copiado',
        ];
    }

    public function collection()
    {
        // Obtener el ID de la última estrategia registrada por cada institución
        $latestStrategyIds = Estrategy::select(DB::raw('MAX(id) as max_id'))
            ->whereNotNull('institution_id')
            ->groupBy('institution_id')
            ->pluck('max_id');

        $campaigns = Campaign::with([
            'campaignType',
            'estrategy.institution',
            'estrategy.juridicalNature',
            'estrategy.responsable',
            'versions',
        ])
            ->whereIn('estrategy_id', $latestStrategyIds)
            ->get();

        $expandedRows = new Collection();
        $mediaTypes   = $this->getMediaTypes();

        foreach ($campaigns as $campaign) {
            $estrategia                    = $campaign->estrategy;
            $estrategiaId                  = $estrategia ? $estrategia->id : '';
            $estrategiaAnio                = $estrategia ? $estrategia->anio : '';
            $estrategiaConcepto            = $estrategia ? $estrategia->concepto : '';
            $estrategiaEstado              = $estrategia ? $estrategia->estado_estrategia : '';
            $estrategiaFechaElaboracion    = $estrategia && $estrategia->fecha_elaboracion
                ? $estrategia->fecha_elaboracion->format('d/m/Y')
                : '';
            $estrategiaOficioDgnc          = $estrategia ? ($estrategia->oficio_dgnc ?? '') : '';
            $estrategiaPresupuesto         = $estrategia ? ($estrategia->presupuesto ?? 0) : 0;
            $estrategiaPartidaPresupuestal = $estrategia ? ($estrategia->partida_presupuestal ?? '') : '';

            foreach ($mediaTypes as $mediaName => $mediaField) {
                $expandedRows->push([
                    // Estrategia
                    'estrategia_id'                  => $estrategiaId,
                    'estrategia_anio'                => $estrategiaAnio,
                    'estrategia_concepto'            => $estrategiaConcepto,
                    'estrategia_estado'              => $estrategiaEstado,
                    'estrategia_fecha_elaboracion'   => $estrategiaFechaElaboracion,
                    'estrategia_oficio_dgnc'         => $estrategiaOficioDgnc,
                    'estrategia_presupuesto'         => $estrategiaPresupuesto,
                    'estrategia_partida_presupuestal'=> $estrategiaPartidaPresupuestal,

                    // Campaña
                    'campana_id'              => $campaign->id,
                    'campana_nombre'          => $campaign->name,
                    'campana_tipo'            => $campaign->campaignType ? $campaign->campaignType->name : '',
                    'institucion'             => $estrategia && $estrategia->institution
                        ? $estrategia->institution->name
                        : ($campaign->institution_name ?? ''),
                    'objetivo_comunicacion'   => $campaign->objetivoComunicacion ?? '',
                    'coemisores_acronyms'     => $campaign->coemisores_acronyms ?? '',

                    // Audiencia
                    'sexo'          => is_array($campaign->sexo)     ? implode(', ', $campaign->sexo)     : $campaign->sexo,
                    'edad'          => is_array($campaign->edad)     ? implode(', ', $campaign->edad)     : $campaign->edad,
                    'poblacion'     => is_array($campaign->poblacion) ? implode(', ', $campaign->poblacion) : $campaign->poblacion,
                    'nse'           => is_array($campaign->nse)      ? implode(', ', $campaign->nse)      : $campaign->nse,
                    'caracteristicas'=> $campaign->caracEspecific ?? '',

                    // Medios Oficiales
                    'tv_oficial'     => $campaign->tv_oficial     ? 'Sí' : 'No',
                    'radio_oficial'  => $campaign->radio_oficial  ? 'Sí' : 'No',
                    'tv_comercial'   => $campaign->tv_comercial   ? 'Sí' : 'No',
                    'radio_comercial'=> $campaign->radio_comercial ? 'Sí' : 'No',

                    // Tipo de Medio y Monto
                    'tipo_medio' => $mediaName,
                    'monto'      => (float) ($campaign->{$mediaField} ?? 0),

                    // Versiones y Fechas
                    'num_versiones'             => $campaign->versions->count(),
                    'campana_fecha_creacion'    => $campaign->created_at ? $campaign->created_at->format('d/m/Y H:i:s') : '',
                    'campana_ultima_actualizacion'=> $campaign->updated_at ? $campaign->updated_at->format('d/m/Y H:i:s') : '',
                ]);
            }
        }

        return $expandedRows;
    }

    public function headings(): array
    {
        return [
            'ID Estrategia',
            'Año Estrategia',
            'Concepto',
            'Estado Estrategia',
            'Fecha Elaboración',
            'Oficio DGNC',
            'Presupuesto Total',
            'Partida Presupuestal',
            'ID Campaña',
            'Nombre de la Campaña',
            'Tipo de Campaña',
            'Institución',
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
            'Fecha Creación Campaña',
            'Última Actualización Campaña',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'Y' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Estrategia A-H — Azul oscuro
        $sheet->getStyle('A1:H1')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E5C8A']],
        ]);

        // Campaña I-N — Verde
        $sheet->getStyle('I1:N1')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '548235']],
        ]);

        // Audiencia O-S — Naranja
        $sheet->getStyle('O1:S1')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C55A11']],
        ]);

        // Medios Oficiales T-W — Morado
        $sheet->getStyle('T1:W1')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '7030A0']],
        ]);

        // Tipo de Medio y Monto X-Y — Rojo
        $sheet->getStyle('X1:Y1')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
        ]);

        // Versiones y Fechas Z-AB — Gris
        $sheet->getStyle('Z1:AB1')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '7F7F7F']],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->setAutoFilter('A1:AB1');
        $sheet->freezePane('A2');

        return [];
    }
}
