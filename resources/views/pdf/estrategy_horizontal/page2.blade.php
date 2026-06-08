{{-- Página 2: Resumen Presupuestal (Hoja 3 del ejemplo) --}}

{{-- Header con logos --}}
<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 18px;">
    <tr>
        <td style="width: 25%; text-align: left; vertical-align: middle; border: none;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="55" alt="Logo Izquierdo">
            @endif
        </td>
        <td style="width: 50%; border: none;"></td>
        <td style="width: 25%; text-align: right; vertical-align: middle; border: none;">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="55" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

{{-- Títulos --}}
<div style="text-align: center; margin-bottom: 14px;">
    <div style="font-size: 14pt; font-weight: bold; line-height: 1.4; margin-bottom: 4px;">
        ESTRATEGIA ANUAL DE {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}<br>
        PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
    </div>
    <div style="font-size: 11pt; font-weight: bold; margin-top: 6px;">
        RESUMEN PRESUPUESTAL - {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}
    </div>
</div>

@php
    $totalElectronicos    = 0;
    $totalRadioComunitaria = 0;
    $totalImpresos        = 0;
    $totalComplementarios = 0;
    $totalEstudios        = 0;
    $totalDisenoProduccion = 0;

    foreach ($estrategy->campaigns as $c) {
        $totalRadioComunitaria += ($c->radio_comunitaria ?? 0);
        $totalElectronicos    += ($c->televisoras ?? 0) + ($c->radiodifusoras ?? 0) + ($c->radio_comunitaria ?? 0) + ($c->mediosDigitalesInternet ?? 0);
        $totalImpresos        += ($c->decdmx ?? 0) + ($c->deedos ?? 0) + ($c->deextr ?? 0) + ($c->revistas ?? 0);
        $totalComplementarios += ($c->cine ?? 0) + ($c->mediosComplementarios ?? 0) + ($c->mediosDigitales ?? 0);
        $totalEstudios        += ($c->preEstudios ?? 0) + ($c->postEstudios ?? 0);
        $totalDisenoProduccion += ($c->disenio ?? 0) + ($c->produccion ?? 0) + ($c->preProduccion ?? 0) + ($c->postProduccion ?? 0) + ($c->copiado ?? 0);
    }
    $granTotal = $totalElectronicos + $totalImpresos + $totalComplementarios + $totalEstudios + $totalDisenoProduccion;
@endphp

{{-- Tabla de distribución presupuestal --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 18px;">
    <tr>
        <td colspan="2" style="background-color: #9B1B30; color: #fff; padding: 8px 12px; text-align: center; font-weight: bold; font-size: 10pt; border: 1px solid #000;">
            DISTRIBUCIÓN PRESUPUESTAL POR CATEGORÍA
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 8px 12px; font-weight: bold; font-size: 10pt; width: 75%;">MEDIOS ELECTRÓNICOS</td>
        <td style="border: 1px solid #000; padding: 8px 12px; text-align: right; font-size: 10pt; width: 25%;">${{ number_format($totalElectronicos, 2) }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 8px 12px; font-weight: bold; font-size: 10pt;">MEDIOS IMPRESOS</td>
        <td style="border: 1px solid #000; padding: 8px 12px; text-align: right; font-size: 10pt;">${{ number_format($totalImpresos, 2) }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 8px 12px; font-weight: bold; font-size: 10pt;">MEDIOS COMPLEMENTARIOS</td>
        <td style="border: 1px solid #000; padding: 8px 12px; text-align: right; font-size: 10pt;">${{ number_format($totalComplementarios, 2) }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 8px 12px; font-weight: bold; font-size: 10pt;">ESTUDIOS</td>
        <td style="border: 1px solid #000; padding: 8px 12px; text-align: right; font-size: 10pt;">${{ number_format($totalEstudios, 2) }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 8px 12px; font-weight: bold; font-size: 10pt;">DISEÑO, PRODUCCIÓN Y POST-PRODUCCIÓN</td>
        <td style="border: 1px solid #000; padding: 8px 12px; text-align: right; font-size: 10pt;">${{ number_format($totalDisenoProduccion, 2) }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 8px 12px; font-weight: bold; font-size: 10pt; background-color: #9B1B30; color: #fff;">PRESUPUESTO TOTAL</td>
        <td style="border: 1px solid #000; padding: 8px 12px; text-align: right; font-size: 10pt; font-weight: bold; background-color: #9B1B30; color: #fff;">${{ number_format($granTotal, 2) }}</td>
    </tr>
</table>

{{-- Gráfica de distribución porcentual --}}
<div style="text-align: center; font-weight: bold; font-size: 10pt; margin-bottom: 12px;">
    DISTRIBUCIÓN PORCENTUAL DEL PRESUPUESTO
</div>

@if($granTotal > 0)
    @php
        $categorias = [
            ['nombre' => 'Medios Electrónicos',    'valor' => $totalElectronicos,     'color' => '#9B1B30'],
            ['nombre' => 'Radio Comunitaria',       'valor' => $totalRadioComunitaria, 'color' => '#2D8A4E'],
            ['nombre' => 'Medios Impresos',         'valor' => $totalImpresos,         'color' => '#C45E73'],
            ['nombre' => 'Medios Complementarios',  'valor' => $totalComplementarios,  'color' => '#E89CAC'],
            ['nombre' => 'Estudios',                'valor' => $totalEstudios,         'color' => '#B8860B'],
            ['nombre' => 'Diseño y Producción',     'valor' => $totalDisenoProduccion, 'color' => '#DAA520'],
        ];
    @endphp
    @foreach($categorias as $cat)
        @if($cat['valor'] > 0)
            @php $pct = ($cat['valor'] / $granTotal) * 100; @endphp
            <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 5px;">
                <tr>
                    <td style="border: none; width: 20%; padding: 0 8px 0 0; font-size: 9pt; vertical-align: middle;">
                        {{ $cat['nombre'] }}
                    </td>
                    <td style="border: none; width: 65%; padding: 0; vertical-align: middle;">
                        <table style="width: 100%; border: none; border-collapse: collapse;">
                            <tr>
                                <td style="width: {{ number_format($pct, 1) }}%; background-color: {{ $cat['color'] }}; height: 18px; border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: none; width: 15%; padding: 0 0 0 8px; font-size: 9pt; text-align: right; vertical-align: middle;">
                        {{ number_format($pct, 1) }}%
                    </td>
                </tr>
            </table>
        @endif
    @endforeach
@else
    <div style="text-align: center; color: #666; font-style: italic;">No hay datos presupuestales para mostrar</div>
@endif

{{-- Sección de Firmas --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    <table style="margin-top: 50px; width: 100%;">
        <tr>
            <td style="text-align: center; vertical-align: bottom; border: none;">
                <div style="border-top: 1px solid #000; margin: 0 auto; width: 45%; padding-top: 4px; font-size: 8.5pt; text-align: center;">
                    {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la coordinadora sectorial
                </div>
            </td>
        </tr>
    </table>
@else
    <table style="margin-top: 126px; width: 100%; border: none;">
        <tr>
            <td style="width: 48%; text-align: center; vertical-align: bottom; border: none;">
                <div style="border-top: 1px solid #000; margin: 0 auto; width: 80%; padding-top: 4px; font-size: 8.5pt; text-align: center;">
                    {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la coordinadora sectorial
                </div>
            </td>
            <td style="width: 4%; border: none;"></td>
            <td style="width: 48%; text-align: center; vertical-align: bottom; border: none;">
                <div style="border-top: 1px solid #000; margin: 0 auto; width: 80%; padding-top: 4px; font-size: 8.5pt; text-align: center;">
                    {{ $estrategy->responsable_name ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la dependencia/entidad
                </div>
            </td>
        </tr>
    </table>
@endif
