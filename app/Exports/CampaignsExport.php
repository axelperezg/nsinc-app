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
            'estrategy.juridicalNature',
            'estrategy.responsable',
            'versions'
        ])->get();

        $expandedRows = new Collection();
        $mediaTypes = $this->getMediaTypes();

        foreach ($campaigns as $campaign) {
            // Datos de la estrategia (solo si existe la relación)
            $estrategia = $campaign->estrategy;
            $estrategiaId = $estrategia ? $estrategia->id : '';
            $estrategiaAnio = $estrategia ? $estrategia->anio : '';
            $estrategiaConcepto = $estrategia ? $estrategia->concepto : '';
            $estrategiaEstado = $estrategia ? $estrategia->estado_estrategia : '';
            $estrategiaFechaElaboracion = $estrategia && $estrategia->fecha_elaboracion
                ? $estrategia->fecha_elaboracion->format('d/m/Y')
                : '';
            $estrategiaOficioDgnc = $estrategia ? ($estrategia->oficio_dgnc ?? '') : '';
            $estrategiaPresupuesto = $estrategia ? ($estrategia->presupuesto ?? 0) : 0;
            $estrategiaPartidaPresupuestal = $estrategia ? ($estrategia->partida_presupuestal ?? '') : '';

            // Por cada campaña, crear 17 filas (una por cada tipo de medio)
            foreach ($mediaTypes as $mediaName => $mediaField) {
                $expandedRows->push([
                    // Información de la Estrategia
                    'estrategia_id' => $estrategiaId,
                    'estrategia_anio' => $estrategiaAnio,
                    'estrategia_concepto' => $estrategiaConcepto,
                    'estrategia_estado' => $estrategiaEstado,
                    'estrategia_fecha_elaboracion' => $estrategiaFechaElaboracion,
                    'estrategia_oficio_dgnc' => $estrategiaOficioDgnc,
                    'estrategia_presupuesto' => $estrategiaPresupuesto,
                    'estrategia_partida_presupuestal' => $estrategiaPartidaPresupuestal,

                    // Información de la Campaña
                    'campana_id' => $campaign->id,
                    'campana_nombre' => $campaign->name,
                    'campana_tipo' => $campaign->campaignType ? $campaign->campaignType->name : '',
                    'institucion' => $campaign->estrategy && $campaign->estrategy->institution
                        ? $campaign->estrategy->institution->name
                        : ($campaign->institution_name ?? ''),
                    'objetivo_comunicacion' => $campaign->objetivoComunicacion ?? '',
                    'coemisores_acronyms' => $campaign->coemisores_acronyms ?? '',

                    // Audiencia
                    'sexo' => is_array($campaign->sexo) ? implode(', ', $campaign->sexo) : $campaign->sexo,
                    'edad' => is_array($campaign->edad) ? implode(', ', $campaign->edad) : $campaign->edad,
                    'poblacion' => is_array($campaign->poblacion) ? implode(', ', $campaign->poblacion) : $campaign->poblacion,
                    'nse' => is_array($campaign->nse) ? implode(', ', $campaign->nse) : $campaign->nse,
                    'caracteristicas' => $campaign->caracEspecific ?? '',

                    // Medios Oficiales
                    'tv_oficial' => $campaign->tv_oficial ? 'Sí' : 'No',
                    'radio_oficial' => $campaign->radio_oficial ? 'Sí' : 'No',
                    'tv_comercial' => $campaign->tv_comercial ? 'Sí' : 'No',
                    'radio_comercial' => $campaign->radio_comercial ? 'Sí' : 'No',

                    // Tipo de Medio y Monto
                    'tipo_medio' => $mediaName,
                    'monto' => $campaign->{$mediaField} ?? 0,

                    // Versiones y Fechas
                    'num_versiones' => $campaign->versions->count(),
                    'campana_fecha_creacion' => $campaign->created_at ? $campaign->created_at->format('d/m/Y H:i:s') : '',
                    'campana_ultima_actualizacion' => $campaign->updated_at ? $campaign->updated_at->format('d/m/Y H:i:s') : '',
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
            // Información de la Estrategia
            'ID Estrategia',
            'Año Estrategia',
            'Concepto',
            'Estado Estrategia',
            'Fecha Elaboración',
            'Oficio DGNC',
            'Presupuesto Total',
            'Partida Presupuestal',

            // Información de la Campaña
            'ID Campaña',
            'Nombre de la Campaña',
            'Tipo de Campaña',
            'Institución',
            'Objetivo de Comunicación',
            'Coemisores',

            // Audiencia
            'Sexo',
            'Edad',
            'Población',
            'NSE',
            'Características Específicas',

            // Medios Oficiales
            'TV Oficial',
            'Radio Oficial',
            'TV Comercial',
            'Radio Comercial',

            // Tipo de Medio y Monto
            'Tipo de Medio',
            'Monto',

            // Versiones y Fechas
            'Número de Versiones',
            'Fecha Creación Campaña',
            'Última Actualización Campaña',
        ];
    }

    /**
     * Aplica estilos a la hoja de Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado completo
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Colorear secciones específicas del encabezado
        // Sección Estrategia (columnas A-H) - Azul oscuro
        $sheet->getStyle('A1:H1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E5C8A'] // Azul oscuro
            ],
        ]);

        // Sección Campaña (columnas I-N) - Verde
        $sheet->getStyle('I1:N1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '548235'] // Verde
            ],
        ]);

        // Sección Audiencia (columnas O-S) - Naranja
        $sheet->getStyle('O1:S1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C55A11'] // Naranja
            ],
        ]);

        // Sección Medios Oficiales (columnas T-W) - Morado
        $sheet->getStyle('T1:W1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7030A0'] // Morado
            ],
        ]);

        // Sección Tipo de Medio y Monto (columnas X-Y) - Rojo
        $sheet->getStyle('X1:Y1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C00000'] // Rojo
            ],
        ]);

        // Sección Versiones y Fechas (columnas Z-AB) - Gris
        $sheet->getStyle('Z1:AB1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7F7F7F'] // Gris
            ],
        ]);

        // Ajustar altura de la fila del encabezado
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Habilitar filtros
        $sheet->setAutoFilter('A1:AB1');

        // Congelar primera fila
        $sheet->freezePane('A2');

        return [];
    }
}
