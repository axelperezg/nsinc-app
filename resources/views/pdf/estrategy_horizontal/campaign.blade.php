{{-- Página de Campaña (Hoja 4 del ejemplo) --}}

@php
    $esComunicacion = $estrategy->partida_presupuestal === '36101';
    $partida        = $estrategy->partida_presupuestal;

    $totalCampaign =
        ($campaign->televisoras           ?? 0) +
        ($campaign->radiodifusoras        ?? 0) +
        ($campaign->radio_comunitaria     ?? 0) +
        ($campaign->mediosDigitalesInternet ?? 0) +
        ($campaign->cine                  ?? 0) +
        ($campaign->decdmx                ?? 0) +
        ($campaign->deedos                ?? 0) +
        ($campaign->deextr                ?? 0) +
        ($campaign->revistas              ?? 0) +
        ($campaign->mediosComplementarios ?? 0) +
        ($campaign->mediosDigitales       ?? 0) +
        ($campaign->preEstudios           ?? 0) +
        ($campaign->postEstudios          ?? 0) +
        ($campaign->disenio               ?? 0) +
        ($campaign->produccion            ?? 0) +
        ($campaign->preProduccion         ?? 0) +
        ($campaign->postProduccion        ?? 0) +
        ($campaign->copiado               ?? 0);

    $porcentajeCampaign = $estrategy->presupuesto > 0
        ? ($totalCampaign / $estrategy->presupuesto) * 100
        : 0;

    $acumulado = 0;
    foreach ($estrategy->campaigns as $c) {
        $acumulado +=
            ($c->televisoras           ?? 0) + ($c->radiodifusoras        ?? 0) +
            ($c->radio_comunitaria     ?? 0) + ($c->mediosDigitalesInternet ?? 0) + ($c->cine ?? 0) +
            ($c->decdmx                ?? 0) + ($c->deedos                ?? 0) +
            ($c->deextr                ?? 0) + ($c->revistas              ?? 0) +
            ($c->mediosComplementarios ?? 0) + ($c->mediosDigitales       ?? 0) +
            ($c->preEstudios           ?? 0) + ($c->postEstudios          ?? 0) +
            ($c->disenio               ?? 0) + ($c->produccion            ?? 0) +
            ($c->preProduccion         ?? 0) + ($c->postProduccion        ?? 0) +
            ($c->copiado               ?? 0);
    }

    $sexoValues     = filled($campaign->sexo)     ? (is_array($campaign->sexo)     ? $campaign->sexo     : [$campaign->sexo])     : [];
    $edadValues     = filled($campaign->edad)     ? (is_array($campaign->edad)     ? $campaign->edad     : [$campaign->edad])     : [];
    $poblacionValues = filled($campaign->poblacion) ? (is_array($campaign->poblacion) ? $campaign->poblacion : [$campaign->poblacion]) : [];
    $nseValues      = filled($campaign->nse)      ? (is_array($campaign->nse)      ? $campaign->nse      : [$campaign->nse])      : [];
    sort($nseValues);
@endphp

{{-- ===== HEADER CON LOGOS ===== --}}
<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 10px;">
    <tr>
        <td style="width: 25%; text-align: left; vertical-align: middle; border: none;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="50" alt="Logo Izquierdo">
            @endif
        </td>
        <td style="width: 50%; border: none;"></td>
        <td style="width: 25%; text-align: right; vertical-align: middle; border: none;">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="50" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

{{-- ===== TÍTULO ===== --}}
<div style="text-align: center; font-size: 10pt; font-weight: bold; margin-bottom: 8px;">
    {{ $esComunicacion ? 'PROGRAMA ANUAL DE COMUNICACIÓN SOCIAL' : 'PROGRAMA ANUAL DE PROMOCIÓN Y PUBLICIDAD' }} PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
</div>

{{-- ===== DEPENDENCIA ===== --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
    <tr>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold; width: 22%;">Dependencia o Entidad:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt;">{{ $estrategy->institution_name }}</td>
    </tr>
</table>

{{-- miles de pesos --}}
<div style="text-align: right; font-size: 8pt; margin-top: 3px; margin-bottom: 2px;">miles de pesos / I.V.A. incluido</div>

{{-- ===== PRESUPUESTO Y ACUMULADO ===== --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
    <tr>
        <td style="border: 1px solid #000; background-color: #9B1B30; color: #fff; padding: 4px 8px; font-size: 9pt; font-weight: bold; width: 80%;">
            Presupuesto anual de la dependencia o entidad destinado a la partida {{ $partida }}:
        </td>
        <td style="border: 1px solid #000; background-color: #f2e4b8; padding: 4px 8px; text-align: right; font-size: 9pt; font-weight: bold; width: 20%;">
            {{ number_format($estrategy->presupuesto, 2) }}
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; background-color: #9B1B30; color: #fff; padding: 4px 8px; font-size: 9pt; font-weight: bold;">
            ACUMULADO EN CAMPAÑAS:
        </td>
        <td style="border: 1px solid #000; background-color: #9B1B30; color: #fff; padding: 4px 8px; text-align: right; font-size: 9pt; font-weight: bold;">
            {{ number_format($acumulado, 2) }}
        </td>
    </tr>
</table>

{{-- ===== BANNER CAMPAÑA ===== --}}
<div style="background-color: #9B1B30; color: #fff; text-align: center; font-size: 11pt; font-weight: bold; padding: 6px; margin-bottom: 6px;">
    CAMPAÑA {{ $campaignNumber }}
</div>

{{-- ===== DOS COLUMNAS: DATOS | MEDIOS ===== --}}
<table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
    <tr>

        {{-- ======== COLUMNA IZQUIERDA: DATOS ======== --}}
        <td style="width: 58%; vertical-align: top; border: none; border-right: 1px solid #000; padding: 0;">

            {{-- Datos generales --}}
            <div style="background-color: #9B1B30; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 4px 8px;">
                Datos generales
            </div>
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; width: 36%; vertical-align: top;">Nombre de la campaña:</td>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">{{ $campaign->name }}</td>
                </tr>
                @if($esComunicacion)
                <tr>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; vertical-align: top;">Clasificación de campaña:</td>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">{{ $campaign->campaignType->name ?? 'No especificado' }}</td>
                </tr>
                @else
                <tr>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; vertical-align: top;">Meta de Campaña:</td>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">{{ $campaign->meta_campana ?? 'No especificado' }}</td>
                </tr>
                @endif
                <tr>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; vertical-align: top;">Tema específico:</td>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">{{ $campaign->temaEspecifico ? Str::limit($campaign->temaEspecifico, 300, '... (ver en sistema)') : 'No especificado' }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; vertical-align: top;">Público objetivo:</td>
                    <td style="border: none; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">
                        @if(count($sexoValues) > 0)- Sexo: {{ implode(', ', $sexoValues) }}<br>@endif
                        @if(count($edadValues) > 0)- Edad: {{ implode(', ', $edadValues) }}<br>@endif
                        @if(count($poblacionValues) > 0)- Población: {{ implode(', ', $poblacionValues) }}<br>@endif
                        @if(count($nseValues) > 0)- NSE: {{ implode(', ', $nseValues) }}@endif
                    </td>
                </tr>
            </table>

            {{-- Datos de la campaña --}}
            <div style="background-color: #9B1B30; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 4px 8px; border-top: 1px solid #000;">
                Datos de la campaña
            </div>
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; width: 36%; vertical-align: top;">Objetivo de Comunicación:</td>
                    <td style="border: none; border-bottom: 1px solid #e0e0e0; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">{{ $campaign->objetivoComunicacion ? Str::limit($campaign->objetivoComunicacion, 300, '... (ver en sistema)') : 'No especificado' }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; vertical-align: top;">Coemisor(es):</td>
                    <td style="border: none; padding: 3px 8px; font-size: 8.5pt; vertical-align: top;">{{ $campaign->coemisores_acronyms ?? 'No especificado' }}</td>
                </tr>
            </table>

            {{-- Versiones de la Campaña --}}
            <div style="background-color: #9B1B30; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 4px 8px; border-top: 1px solid #000;">
                Versiones de la Campaña
            </div>
            <div style="padding: 4px 8px;">
                @if($campaign->versions && $campaign->versions->count() > 0)
                    @php
                        $versionsWithDates = $campaign->versions->filter(fn($v) => !empty($v->fechaInicio) || !empty($v->fechaFinal));
                        $fechaMin = $versionsWithDates->min('fechaInicio');
                        $fechaMax = $versionsWithDates->max('fechaFinal');
                    @endphp
                    @if($fechaMin || $fechaMax)
                        <div style="font-size: 8.5pt; font-weight: bold; margin-bottom: 4px;">
                            Vigencia general: {{ $fechaMin ? \Carbon\Carbon::parse($fechaMin)->format('d/m/Y') : '' }} - {{ $fechaMax ? \Carbon\Carbon::parse($fechaMax)->format('d/m/Y') : '' }}
                        </div>
                    @endif
                    @foreach($campaign->versions as $version)
                        @php
                            $inicio = $version->fechaInicio ? \Carbon\Carbon::parse($version->fechaInicio)->format('d/m/Y') : 'Sin fecha';
                            $fin    = $version->fechaFinal  ? \Carbon\Carbon::parse($version->fechaFinal)->format('d/m/Y')  : 'Sin fecha';
                        @endphp
                        <div style="font-size: 8.5pt; margin-bottom: 3px;">
                            <strong>{{ $version->name ?? 'Etapa ' . $loop->iteration }}</strong><br>
                            Del {{ $inicio }} al {{ $fin }}
                        </div>
                    @endforeach
                @else
                    <div style="font-size: 8.5pt; color: #666; font-style: italic;">Sin versiones definidas</div>
                @endif
            </div>

        </td>

        {{-- ======== COLUMNA DERECHA: MEDIOS ======== --}}
        <td style="width: 42%; vertical-align: top; border: none; padding: 0;">

            {{-- Header Medios --}}
            <div style="background-color: #d9d9d9; text-align: center; font-size: 9pt; font-weight: bold; padding: 5px 4px; border-bottom: 1px solid #000;">
                Medios a utilizar
            </div>

            {{-- Sub-tabla Tiempos Oficiales / Comerciales --}}
            <table style="width: 100%; border-collapse: collapse; border-bottom: 1px solid #000;">
                <tr>
                    <td colspan="2" style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; background-color: #9B1B30; color: #fff;">Tiempos oficiales</td>
                    <td colspan="2" style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; background-color: #9B1B30; color: #fff;">Tiempos comerciales</td>
                    <td rowspan="3" style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; background-color: #9B1B30; color: #fff; vertical-align: middle;">Recursos<br>programados<br>por tipo medio</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; width: 10%;">TV</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; width: 10%;">Radio</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; width: 10%;">TV</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 7.5pt; font-weight: bold; padding: 2px; width: 10%;">Radio</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align: center; font-size: 8pt; font-weight: bold; padding: 2px;">{{ $campaign->tv_oficial    ? 'X' : '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 8pt; font-weight: bold; padding: 2px;">{{ $campaign->radio_oficial  ? 'X' : '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 8pt; font-weight: bold; padding: 2px;">{{ $campaign->tv_comercial   ? 'X' : '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 8pt; font-weight: bold; padding: 2px;">{{ $campaign->radio_comercial ? 'X' : '' }}</td>
                </tr>
            </table>

            {{-- Tabla de medios y montos --}}
            <table style="width: 100%; border-collapse: collapse;">
                @php
                    $medios = [
                        ['label' => 'Televisoras',                    'val' => $campaign->televisoras           ?? 0],
                        ['label' => 'Radiodifusoras',                 'val' => $campaign->radiodifusoras        ?? 0],
                        ['label' => 'Radio Comunitaria',              'val' => $campaign->radio_comunitaria     ?? 0],
                        ['label' => 'Cine',                           'val' => $campaign->cine                  ?? 0],
                        ['label' => 'Diarios Editados en CDMX',       'val' => $campaign->decdmx                ?? 0],
                        ['label' => 'Diarios Editados en los Estados','val' => $campaign->deedos                ?? 0],
                        ['label' => 'Diarios Editados en el Extranjero','val' => $campaign->deextr              ?? 0],
                        ['label' => 'Revistas',                       'val' => $campaign->revistas              ?? 0],
                        ['label' => 'Medios Complementarios',         'val' => $campaign->mediosComplementarios ?? 0],
                        ['label' => 'Medios Digitales',               'val' => $campaign->mediosDigitales       ?? 0],
                        ['label' => 'Pre-Estudios',                   'val' => $campaign->preEstudios           ?? 0],
                        ['label' => 'Post-Estudios',                  'val' => $campaign->postEstudios          ?? 0],
                        ['label' => 'Diseño',                         'val' => $campaign->disenio               ?? 0],
                        ['label' => 'Producción',                     'val' => $campaign->produccion            ?? 0],
                        ['label' => 'Preproducción',                  'val' => $campaign->preProduccion         ?? 0],
                        ['label' => 'Post-producción',                'val' => $campaign->postProduccion        ?? 0],
                        ['label' => 'Copiado',                        'val' => $campaign->copiado               ?? 0],
                    ];
                @endphp
                @foreach($medios as $medio)
                    <tr>
                        <td style="border: 1px solid #000; padding: 1px 4px; font-size: 7pt; width: 68%;">{{ $medio['label'] }}</td>
                        <td style="border: 1px solid #000; padding: 1px 4px; font-size: 7pt; text-align: right; width: 32%;">{{ number_format($medio['val'], 2) }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #d9d9d9;">
                    <td style="border: 1px solid #000; padding: 4px 5px; font-size: 8.5pt; font-weight: bold;">Presupuesto asignado a la campaña</td>
                    <td style="border: 1px solid #000; padding: 4px 5px; font-size: 8.5pt; font-weight: bold; text-align: right;">{{ number_format($totalCampaign, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 2px 5px; font-size: 7.5pt; background-color: #9B1B30; color: #fff;">Porcentaje que representa la campaña de la partida {{ $partida }}:</td>
                    <td style="border: 1px solid #000; padding: 2px 5px; font-size: 7.5pt; text-align: right; font-weight: bold; background-color: #9B1B30; color: #fff;">{{ number_format($porcentajeCampaign, 2) }}%</td>
                </tr>
            </table>

        </td>
    </tr>
</table>

{{-- ===== FIRMAS ===== --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    <table style="margin-top: 30px; width: 100%; border: none;">
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
    <table style="margin-top: 30px; width: 100%; border: none;">
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
