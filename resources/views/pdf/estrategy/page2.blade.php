{{-- Página 2: Resumen de Medios y Distribución Presupuestal --}}

<table class="header-logos">
    <tr>
        <td class="logo-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="35" alt="Logo Izquierdo">
            @endif
        </td>
        <td class="logo-center"></td>
        <td class="logo-right">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="35" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

<div class="title" style="font-size: 12pt; margin-bottom: 20px;">
    @if($estrategy->partida_presupuestal === '36101')
        RESUMEN PRESUPUESTAL - COMUNICACIÓN SOCIAL
    @else
        RESUMEN PRESUPUESTAL - PROMOCIÓN Y PUBLICIDAD
    @endif
</div>

@php
    // Calcular totales por categoría
    $totalElectronicos = 0;
    $totalRadioComunitaria = 0;
    $totalImpresos = 0;
    $totalComplementarios = 0;
    $totalEstudios = 0;
    $totalDisenoProduccion = 0;

    foreach($estrategy->campaigns as $campaign) {
        // Medios Electrónicos
        $totalRadioComunitaria += ($campaign->radio_comunitaria ?? 0);
        $totalElectronicos += ($campaign->televisoras ?? 0) + ($campaign->radiodifusoras ?? 0) + ($campaign->radio_comunitaria ?? 0) + ($campaign->mediosDigitalesInternet ?? 0);

        // Medios Impresos
        $totalImpresos += ($campaign->decdmx ?? 0) + ($campaign->deedos ?? 0) + ($campaign->deextr ?? 0) + ($campaign->revistas ?? 0);

        // Medios Complementarios
        $totalComplementarios += ($campaign->cine ?? 0) + ($campaign->mediosComplementarios ?? 0) + ($campaign->mediosDigitales ?? 0);

        // Estudios
        $totalEstudios += ($campaign->preEstudios ?? 0) + ($campaign->postEstudios ?? 0);

        // Diseño, Producción, Post-Producción
        $totalDisenoProduccion += ($campaign->disenio ?? 0) + ($campaign->produccion ?? 0) + ($campaign->preProduccion ?? 0) + ($campaign->postProduccion ?? 0) + ($campaign->copiado ?? 0);
    }

    $granTotal = $totalElectronicos + $totalImpresos + $totalComplementarios + $totalEstudios + $totalDisenoProduccion;
@endphp

{{-- Tabla de Resumen de Medios --}}
<div style="margin-bottom: 20px;">
    <table style="width: 100%; border: 1px solid #000;">
        <tr>
            <th colspan="2" style="background-color: #9B1B30; color: #ffffff; padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">
                DISTRIBUCIÓN PRESUPUESTAL POR CATEGORÍA
            </th>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; font-weight: bold; width: 60%;">MEDIOS ELECTRÓNICOS</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: right; width: 40%;">${{ number_format($totalElectronicos, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; font-weight: bold;">MEDIOS IMPRESOS</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: right;">${{ number_format($totalImpresos, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; font-weight: bold;">MEDIOS COMPLEMENTARIOS</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: right;">${{ number_format($totalComplementarios, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; font-weight: bold;">ESTUDIOS</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: right;">${{ number_format($totalEstudios, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; font-weight: bold;">DISEÑO, PRODUCCIÓN Y POST-PRODUCCIÓN</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: right;">${{ number_format($totalDisenoProduccion, 2) }}</td>
        </tr>
        <tr style="background-color: #9B1B30;">
            <td style="border: 1px solid #000; padding: 6px; color: #ffffff; font-weight: bold; font-size: 10pt;">PRESUPUESTO TOTAL</td>
            <td style="border: 1px solid #000; padding: 6px; color: #ffffff; text-align: right; font-weight: bold; font-size: 10pt;">${{ number_format($granTotal, 2) }}</td>
        </tr>
    </table>
</div>

{{-- Gráfico de Barras Horizontales --}}
<div style="margin: 20px 0;">
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 15px; text-align: center;">DISTRIBUCIÓN PORCENTUAL DEL PRESUPUESTO</div>

    @if($granTotal > 0)
    <table style="width: 100%; border-collapse: collapse;">
        @php
            $categorias = [
                ['nombre' => 'Medios Electrónicos', 'valor' => $totalElectronicos, 'color' => '#9B1B30'],
                ['nombre' => 'Radio Comunitaria', 'valor' => $totalRadioComunitaria, 'color' => '#2D8A4E'],
                ['nombre' => 'Medios Impresos', 'valor' => $totalImpresos, 'color' => '#C45E73'],
                ['nombre' => 'Medios Complementarios', 'valor' => $totalComplementarios, 'color' => '#E89CAC'],
                ['nombre' => 'Estudios', 'valor' => $totalEstudios, 'color' => '#B8860B'],
                ['nombre' => 'Diseño y Producción', 'valor' => $totalDisenoProduccion, 'color' => '#DAA520']
            ];
        @endphp

        @foreach($categorias as $categoria)
            @if($categoria['valor'] > 0)
                @php
                    $porcentaje = ($categoria['valor'] / $granTotal) * 100;
                    $anchoBar = $porcentaje; // Porcentaje del ancho total
                @endphp
                <tr>
                    <td style="width: 35%; padding: 4px 8px; font-size: 8pt; font-weight: bold; border: none;">
                        {{ $categoria['nombre'] }}
                    </td>
                    <td style="width: 50%; padding: 4px; border: none;">
                        <div style="background-color: {{ $categoria['color'] }}; width: {{ $anchoBar }}%; height: 18px; border-radius: 3px;"></div>
                    </td>
                    <td style="width: 15%; padding: 4px 8px; font-size: 8pt; text-align: right; border: none;">
                        {{ number_format($porcentaje, 1) }}%
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
    @else
    <p style="text-align: center; color: #666; font-style: italic;">No hay datos presupuestales para mostrar</p>
    @endif
</div>

{{-- Sección de Firmas Condicional --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
    <table style="margin-top: 50px; width: 100%;">
        <tr>
            <td style="text-align: center; vertical-align: bottom; padding-top: 40px;">
                <div style="border-top: 1px solid #000; margin: 0 auto; width: 60%; padding-top: 5px; font-size: 8pt;">
                    {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la coordinadora sectorial
                </div>
            </td>
        </tr>
    </table>
@else
    {{-- Si NO es Sector, mostrar ambas firmas --}}
    <table class="signature-row" style="margin-top: 50px;">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">
                    {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la coordinadora sectorial
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td class="signature-cell">
                <div class="signature-line">
                    {{ $estrategy->responsable_name ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la dependencia/entidad
                </div>
            </td>
        </tr>
    </table>
@endif
