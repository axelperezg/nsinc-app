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
    <div style="margin-bottom: 3px;">
        <span style="font-weight: bold;">Dependencia o Entidad:</span>
        <span>{{ $estrategy->institution_name }}</span>
    </div>
    <div style="margin-bottom: 3px;">
        <span style="font-weight: bold;">Naturaleza Jurídica:</span>
        <span>{{ $estrategy->juridical_nature_name }}</span>
    </div>
    <div style="margin-bottom: 3px;">
        <span style="font-weight: bold;">Cabeza de sector:</span>
        <span>{{ $estrategy->institution->sector->name ?? 'No disponible' }}</span>
    </div>
    <div style="margin-bottom: 3px;">
        <span style="font-weight: bold;">Fecha de elaboración:</span>
        <span>{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</span>
    </div>
</div>

{{-- Secciones con marcos redondeados --}}
<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Misión:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->mision }}</div>
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Visión:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->vision }}</div>
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Objetivo Institucional:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->objetivo_institucional }}</div>
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Objetivo de la estrategia de comunicación:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->objetivo_estrategia }}</div>
    </div>
</div>

@php
    // Decodificar el JSON de ejes_plan_nacional
    $ejesData = is_string($estrategy->ejes_plan_nacional)
        ? json_decode($estrategy->ejes_plan_nacional, true)
        : (is_array($estrategy->ejes_plan_nacional) ? $estrategy->ejes_plan_nacional : []);

    // Mapeo de keys a nombres descriptivos
    $nombresEjes = [
        'eje_general_1_gobernanza' => 'Eje General 1: Gobernanza con justicia y participación ciudadana',
        'eje_general_2_desarrollo' => 'Eje General 2: Desarrollo con bienestar y humanismo',
        'eje_general_3_economia' => 'Eje General 3: Economía moral y trabajo',
        'eje_general_4_sustentable' => 'Eje General 4: Desarrollo sustentable',
        'eje_transversal_1_igualdad' => 'Igualdad sustantiva y derechos de las mujeres',
        'eje_transversal_2_innovacion' => 'Innovación pública para el desarrollo tecnológico nacional',
        'eje_transversal_3_derechos' => 'Derechos de los pueblos y comunidades indígenas y afromexicanas',
    ];

    // Filtrar ejes generales que son true
    $ejesGenerales = [];
    foreach ($ejesData as $key => $value) {
        if (str_starts_with($key, 'eje_general_') && $value === true) {
            $ejesGenerales[] = $nombresEjes[$key] ?? $key;
        }
    }

    // Filtrar ejes transversales que son true
    $ejesTransversales = [];
    foreach ($ejesData as $key => $value) {
        if (str_starts_with($key, 'eje_transversal_') && $value === true) {
            $ejesTransversales[] = $nombresEjes[$key] ?? $key;
        }
    }
@endphp

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Eje(s) General(es) del Plan Nacional de Desarrollo:</div>
    <div style="margin-top: 5px;">
        @if(count($ejesGenerales) > 0)
            @foreach($ejesGenerales as $eje)
                <div style="margin: 2px 0;">
                    <span class="checkbox checked"></span>
                    {{ $eje }}
                </div>
            @endforeach
        @else
            <div>Ningún eje general seleccionado</div>
        @endif
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 5px;">Estrategia(s) Transversal(es) que regirán el programa de comunicación:</div>
    <div style="margin-top: 5px;">
        @if(count($ejesTransversales) > 0)
            @foreach($ejesTransversales as $eje)
                <div style="margin: 2px 0;">
                    <span class="checkbox checked"></span>
                    {{ $eje }}
                </div>
            @endforeach
        @else
            <div>Ninguna estrategia transversal seleccionada</div>
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
