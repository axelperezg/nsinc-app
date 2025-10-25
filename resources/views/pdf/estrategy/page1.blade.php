{{-- Página 1: Carátula de Estrategia Anual --}}

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

{{-- Datos sin bordes --}}
<div style="margin-bottom: 15px;">
    <div style="margin-bottom: 8px;">
        <span style="font-weight: bold;">Dependencia o Entidad:</span>
        <span>{{ $estrategy->institution_name }}</span>
    </div>
    <div style="margin-bottom: 8px;">
        <span style="font-weight: bold;">Naturaleza Jurídica:</span>
        <span>{{ $estrategy->juridical_nature_name }}</span>
    </div>
    <div style="margin-bottom: 8px;">
        <span style="font-weight: bold;">Cabeza de sector:</span>
        <span>{{ $estrategy->institution->sector->name ?? 'No disponible' }}</span>
    </div>
    <div style="margin-bottom: 8px;">
        <span style="font-weight: bold;">Fecha de elaboración:</span>
        <span>{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</span>
    </div>
</div>

{{-- Secciones sin bordes --}}
<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Misión:</div>
    <div style="text-align: justify;">{{ $estrategy->mision }}</div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Visión:</div>
    <div style="text-align: justify;">{{ $estrategy->vision }}</div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Objetivo Institucional:</div>
    <div style="text-align: justify;">{{ $estrategy->objetivo_institucional }}</div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Objetivo de la estrategia de comunicación:</div>
    <div style="text-align: justify;">{{ $estrategy->objetivo_estrategia }}</div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Metas Nacionales del PND (elija con una "x"):</div>
    <div style="margin-top: 8px;">
        @if(is_array($estrategy->ejes_plan_nacional))
            @foreach($estrategy->ejes_plan_nacional as $eje)
                <div style="margin: 3px 0;">
                    <span class="checkbox checked"></span>
                    {{ $eje }}
                </div>
            @endforeach
        @else
            <div>
                <span class="checkbox {{ str_contains($estrategy->ejes_plan_nacional ?? '', 'Eje General 1') ? 'checked' : '' }}"></span> PND {{ $estrategy->anio }}-{{ $estrategy->anio + 6 }} Eje General 1
                &nbsp;&nbsp;
                <span class="checkbox {{ str_contains($estrategy->ejes_plan_nacional ?? '', 'Eje General 2') ? 'checked' : '' }}"></span> Eje General 2
                &nbsp;&nbsp;
                <span class="checkbox {{ str_contains($estrategy->ejes_plan_nacional ?? '', 'Eje General 3') ? 'checked' : '' }}"></span> Eje General 3
                &nbsp;&nbsp;
                <span class="checkbox {{ str_contains($estrategy->ejes_plan_nacional ?? '', 'Eje General 4') ? 'checked' : '' }}"></span> Eje General 4
            </div>
        @endif
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Meta (s) nacional (es) que regirán el programa de comunicación:</div>
    <div style="margin-top: 5px;">
        @if(is_array($estrategy->ejes_plan_nacional) && count($estrategy->ejes_plan_nacional) > 0)
            {{ implode(', ', $estrategy->ejes_plan_nacional) }}
        @else
            {{ $estrategy->ejes_plan_nacional ?? 'Selecciona manualmente la opción aplicable.' }}
        @endif
    </div>
</div>

{{-- Resumen de Medios sin bordes --}}
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

<div style="margin-bottom: 15px; margin-top: 20px;">
    <div style="font-weight: bold; font-size: 11pt; margin-bottom: 10px; text-align: center;">RESUMEN DE MEDIOS</div>

    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 50%; vertical-align: top; border: none; padding-right: 10px;">
                <div style="margin-bottom: 5px;">
                    <span style="font-weight: bold; display: inline-block; width: 70%;">MEDIOS ELECTRÓNICOS</span>
                    <span style="display: inline-block; width: 25%; text-align: right;">{{ number_format($totalElectronicos, 2) }}</span>
                </div>
                <div style="margin-bottom: 5px;">
                    <span style="font-weight: bold; display: inline-block; width: 70%;">MEDIOS IMPRESOS</span>
                    <span style="display: inline-block; width: 25%; text-align: right;">{{ number_format($totalImpresos, 2) }}</span>
                </div>
                <div style="margin-bottom: 5px;">
                    <span style="font-weight: bold; display: inline-block; width: 70%;">MEDIOS COMPLEMENTARIOS</span>
                    <span style="display: inline-block; width: 25%; text-align: right;">{{ number_format($totalComplementarios, 2) }}</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; border: none; padding-left: 10px;">
                <div style="margin-bottom: 5px;">
                    <span style="font-weight: bold; display: inline-block; width: 70%;">ESTUDIOS</span>
                    <span style="display: inline-block; width: 25%; text-align: right;">{{ number_format($totalEstudios, 2) }}</span>
                </div>
                <div style="margin-bottom: 5px;">
                    <span style="font-weight: bold; display: inline-block; width: 70%;">DISEÑO, PRODUCCIÓN, POST-PRODUCCIÓN</span>
                    <span style="display: inline-block; width: 25%; text-align: right;">{{ number_format($totalDisenoProduccion, 2) }}</span>
                </div>
                <div style="margin-bottom: 5px; border-top: 2px solid #000; padding-top: 5px;">
                    <span style="font-weight: bold; display: inline-block; width: 70%;">TOTAL</span>
                    <span style="font-weight: bold; display: inline-block; width: 25%; text-align: right;">{{ number_format($granTotal, 2) }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- Sección de Firmas Condicional --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
    <table style="margin-top: 60px; width: 100%;">
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
@endif
