{{-- Página 2: Programas y Presupuesto --}}

<table class="header-logos">
    <tr>
        <td class="logo-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="60" alt="Logo Izquierdo">
            @endif
        </td>
        <td class="logo-center"></td>
        <td class="logo-right">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="60" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

<div class="title">
    ESTRATEGIA ANUAL DE COMUNICACIÓN SOCIAL<br>
    PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
</div>

@php
    $totalElectronicos = 0;
    $totalImpresos = 0;
    $totalComplementarios = 0;
    $totalEstudios = 0;
    $totalDisenoProduccion = 0;

    foreach($estrategy->campaigns as $campaign) {
        // Medios Electrónicos
        $totalElectronicos += ($campaign->televisoras ?? 0) + ($campaign->radiodifusoras ?? 0) + ($campaign->mediosDigitalesInternet ?? 0);

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

<table style="width: 100%; border: 1px solid #000; margin-bottom: 15px;">
    <tr>
        <th style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px; width: 33%;">Programa Sectorial y/o Especial</th>
        <th style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px; width: 33%;">Objetivos Estratégicos y/o Transversales</th>
        <th style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px; width: 34%;">Temas Específicos Derivadores de los Objetivos Estratégicos y/o Transversales</th>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 6px; vertical-align: top;">
            Plan Nacional de Desarrollo {{ $estrategy->anio }}-{{ $estrategy->anio + 6 }}.
        </td>
        <td style="border: 1px solid #000; padding: 6px; vertical-align: top;">
            {{ implode(', ', $estrategy->ejes_plan_nacional ?? []) }}
        </td>
        <td style="border: 1px solid #000; padding: 6px; vertical-align: top;">
            @foreach($estrategy->campaigns as $campaign)
                {{ $loop->iteration }}.- {{ $campaign->temaEspecifco }}<br>
            @endforeach
        </td>
    </tr>
</table>

<div class="budget-summary">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="budget-row">
                    <span class="budget-label">MEDIOS ELECTRÓNICOS</span>
                    <span class="budget-value">{{ number_format($totalElectronicos, 2) }}</span>
                </div>
                <div class="budget-row">
                    <span class="budget-label">MEDIOS IMPRESOS</span>
                    <span class="budget-value">{{ number_format($totalImpresos, 2) }}</span>
                </div>
                <div class="budget-row">
                    <span class="budget-label">MEDIOS COMPLEMENTARIOS</span>
                    <span class="budget-value">{{ number_format($totalComplementarios, 2) }}</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="budget-row">
                    <span class="budget-label">ESTUDIOS</span>
                    <span class="budget-value">{{ number_format($totalEstudios, 2) }}</span>
                </div>
                <div class="budget-row">
                    <span class="budget-label">DISEÑO, PRODUCCIÓN, POST-PRODUCCIÓN</span>
                    <span class="budget-value">{{ number_format($totalDisenoProduccion, 2) }}</span>
                </div>
                <div class="budget-row">
                    <span class="budget-label">TOTAL</span>
                    <span class="budget-value">{{ number_format($granTotal, 2) }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- Sección de Firmas --}}
<table class="signature-row" style="margin-top: 60px;">
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
