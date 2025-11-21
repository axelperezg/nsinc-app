{{-- Página de Campaña --}}

{{-- Header con logos --}}
<table class="header-logos" style="margin-bottom: 10px;">
    <tr>
        <td class="logo-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="45" alt="Logo Izquierdo">
            @endif
        </td>
        <td class="logo-center"></td>
        <td class="logo-right">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="45" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

@php
    // Calcular totales por campaña
    $totalCampaign = ($campaign->televisoras ?? 0) +
                     ($campaign->radiodifusoras ?? 0) +
                     ($campaign->cine ?? 0) +
                     ($campaign->decdmx ?? 0) +
                     ($campaign->deedos ?? 0) +
                     ($campaign->deextr ?? 0) +
                     ($campaign->revistas ?? 0) +
                     ($campaign->mediosComplementarios ?? 0) +
                     ($campaign->mediosDigitales ?? 0) +
                     ($campaign->mediosDigitalesInternet ?? 0) +
                     ($campaign->preEstudios ?? 0) +
                     ($campaign->postEstudios ?? 0) +
                     ($campaign->disenio ?? 0) +
                     ($campaign->produccion ?? 0) +
                     ($campaign->preProduccion ?? 0) +
                     ($campaign->postProduccion ?? 0) +
                     ($campaign->copiado ?? 0);

    $porcentajeCampaign = $estrategy->presupuesto > 0 ? ($totalCampaign / $estrategy->presupuesto) * 100 : 0;

    // Acumulado
    $acumulado = 0;
    foreach($estrategy->campaigns as $c) {
        $acumulado += ($c->televisoras ?? 0) +
                      ($c->radiodifusoras ?? 0) +
                      ($c->cine ?? 0) +
                      ($c->decdmx ?? 0) +
                      ($c->deedos ?? 0) +
                      ($c->deextr ?? 0) +
                      ($c->revistas ?? 0) +
                      ($c->mediosComplementarios ?? 0) +
                      ($c->mediosDigitales ?? 0) +
                      ($c->mediosDigitalesInternet ?? 0) +
                      ($c->preEstudios ?? 0) +
                      ($c->postEstudios ?? 0) +
                      ($c->disenio ?? 0) +
                      ($c->produccion ?? 0) +
                      ($c->preProduccion ?? 0) +
                      ($c->postProduccion ?? 0) +
                      ($c->copiado ?? 0);
    }
@endphp

<div style="font-weight: bold; font-size: 10pt; text-align: center; margin-bottom: 5px;">
    PROGRAMA ANUAL DE COMUNICACIÓN SOCIAL PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
</div>

<table class="data-table" style="margin-bottom: 4px;">
    <tr>
        <td class="label-cell">Dependencia o Entidad:</td>
        <td colspan="3">{{ $estrategy->institution_name }}</td>
    </tr>
    <tr>
        <td class="label-cell">Fecha de elaboración:</td>
        <td colspan="3">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
    </tr>
</table>

<table style="width: 100%; border: 1px solid #000; margin-bottom: 4px;">
    <tr>
        <td colspan="2" style="padding: 4px; text-align: right; font-weight: bold;">miles de pesos / I.V.A. incluido</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 4px; background-color: #d9d9d9; font-weight: bold;">
            Presupuesto anual de la dependencia o entidad destinado a la partida 36101:
        </td>
        <td style="border: 1px solid #000; padding: 4px; text-align: right; background-color: #f2e4b8; font-weight: bold; width: 120px;">
            {{ number_format($estrategy->presupuesto, 2) }}
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 4px; background-color: #d9d9d9; font-weight: bold;">
            ACUMULADO EN CAMPAÑAS:
        </td>
        <td style="border: 1px solid #000; padding: 4px; text-align: right; font-weight: bold;">
            {{ number_format($acumulado, 2) }}
        </td>
    </tr>
</table>

<div class="gray-header">CAMPAÑA {{ $campaignNumber }}</div>

<table class="campaign-table">
    <tr>
        <td class="campaign-left">
            <table style="width: 100%; border: none;">
                <tr>
                    <td colspan="2" style="background-color: #d9d9d9; padding: 4px; font-weight: bold; border: none;">
                        Datos generales
                    </td>
                </tr>
                @php
                    $sexoValues = filled($campaign->sexo) ? (is_array($campaign->sexo) ? $campaign->sexo : [$campaign->sexo]) : [];
                    $edadValues = filled($campaign->edad) ? (is_array($campaign->edad) ? $campaign->edad : [$campaign->edad]) : [];
                    $poblacionValues = filled($campaign->poblacion) ? (is_array($campaign->poblacion) ? $campaign->poblacion : [$campaign->poblacion]) : [];
                    $nseValues = filled($campaign->nse) ? (is_array($campaign->nse) ? $campaign->nse : [$campaign->nse]) : [];
                @endphp
                <tr>
                    <td style="font-weight: bold; padding: 2px 4px; border: none; line-height: 1.2;">Nombre de la campaña:</td>
                    <td style="padding: 2px 4px; border: none; line-height: 1.2;">{{ $campaign->name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 2px 4px; border: none; line-height: 1.2;">Tipo de campaña:</td>
                    <td style="padding: 2px 4px; border: none; line-height: 1.2;">{{ $campaign->campaignType->name ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 2px 4px; border: none; line-height: 1.2;">Tema específico:</td>
                    <td style="padding: 2px 4px; border: none; line-height: 1.2;">{{ $campaign->temaEspecifco }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 2px 4px; border: none; line-height: 1.2;">Público objetivo:</td>
                    <td style="padding: 2px 4px; border: none; line-height: 1.2;">
                        
                        <div style="line-height: 1.2;">- Sexo: {{ count($sexoValues) > 0 ? implode(', ', $sexoValues) : 'No especificado' }}</div>
                        <div style="line-height: 1.2;">- Edad: {{ count($edadValues) > 0 ? implode(', ', $edadValues) : 'No especificado' }}</div>
                        <div style="line-height: 1.2;">- Población: {{ count($poblacionValues) > 0 ? implode(', ', $poblacionValues) : 'No especificado' }}</div>
                        <div style="line-height: 1.2;">- NSE: {{ count($nseValues) > 0 ? implode(', ', $nseValues) : 'No especificado' }}</div>
                    </td>
                </tr>
                
                @if(!empty($campaign->coemisores))
                <tr>
                    <td style="font-weight: bold; padding: 4px; border: none;">Coemisores:</td>
                    <td style="padding: 4px; border: none;">{{ $campaign->coemisores }}</td>
                </tr>
                @endif
                @if(!empty($campaign->caracteristicas_especificas))
                <tr>
                    <td style="font-weight: bold; padding: 4px; border: none;">Características Específicas:</td>
                    <td style="padding: 4px; border: none;">{{ $campaign->caracteristicas_especificas }}</td>
                </tr>
                @endif
            </table>
        </td>
        <td class="campaign-right">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="background-color: #d9d9d9; padding: 4px; font-weight: bold; border: none;">
                        Versiones de la Campaña
                    </td>
                </tr>
                @if($campaign->versions && $campaign->versions->count() > 0)
                    @php
                        $versionsWithDates = $campaign->versions->filter(function ($version) {
                            return !empty($version->fechaInicio) || !empty($version->fechaFinal);
                        });
                        $fechaInicioMin = $versionsWithDates->min('fechaInicio');
                        $fechaFinalMax = $versionsWithDates->max('fechaFinal');
                    @endphp
                    @if($fechaInicioMin || $fechaFinalMax)
                        <tr>
                            <td style="padding: 4px; border: none; font-size: 7.5pt; font-weight: bold;">
                                Vigencia general:
                                {{ $fechaInicioMin ? \Carbon\Carbon::parse($fechaInicioMin)->format('d/m/Y') : 'Sin fecha inicial' }}
                                -
                                {{ $fechaFinalMax ? \Carbon\Carbon::parse($fechaFinalMax)->format('d/m/Y') : 'Sin fecha final' }}
                            </td>
                        </tr>
                    @endif
                    @foreach($campaign->versions as $version)
                        @php
                            $startDate = $version->fechaInicio ? \Carbon\Carbon::parse($version->fechaInicio)->format('d/m/Y') : 'Sin fecha inicial';
                            $endDate = $version->fechaFinal ? \Carbon\Carbon::parse($version->fechaFinal)->format('d/m/Y') : 'Sin fecha final';
                        @endphp
                        <tr>
                            <td style="padding: 4px; border: none; font-size: 7.5pt;">
                                <strong>{{ $version->name ?? 'Versión ' . $loop->iteration }}</strong><br>
                                Del {{ $startDate }} al {{ $endDate }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="padding: 4px; border: none; font-style: italic; color: #666;">Sin versiones definidas</td>
                    </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<div style="margin-top: 4px;">
    <table style="width: 100%; border: 1px solid #000;">
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px 4px; font-weight: bold; width: 70%;">
                RESUMEN DE MEDIOS
            </td>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px 4px; font-weight: bold; text-align: right;">
                MONTO
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; background-color: #e0e0e0; padding: 2px 4px; font-weight: bold; font-size: 8pt;">
                MEDIOS ELECTRÓNICOS
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Televisoras</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->televisoras ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Radiodifusoras</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->radiodifusoras ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Radios Comunitarias</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->mediosDigitalesInternet ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; background-color: #e0e0e0; padding: 2px 4px; font-weight: bold; font-size: 8pt;">
                MEDIOS IMPRESOS
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Diarios CDMX</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->decdmx ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Diarios Estados</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->deedos ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Diarios o medios extranjeros</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->deextr ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Revistas</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->revistas ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; background-color: #e0e0e0; padding: 2px 4px; font-weight: bold; font-size: 8pt;">
                MEDIOS COMPLEMENTARIOS
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Cine</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->cine ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Medios Complementarios</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->mediosComplementarios ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Medios Digitales</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->mediosDigitales ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; background-color: #e0e0e0; padding: 2px 4px; font-weight: bold; font-size: 8pt;">
                ESTUDIOS
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Pre Estudios</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->preEstudios ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Post Estudios</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->postEstudios ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; background-color: #e0e0e0; padding: 2px 4px; font-weight: bold; font-size: 8pt;">
                DISEÑO, PRODUCCIÓN, PRE-PRODUCCIÓN, POST-PRODUCCIÓN Y COPIADO
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Diseño</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->disenio ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Producción</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->produccion ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Pre-Producción</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->preProduccion ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Post-Producción</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->postProduccion ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 1px 4px; font-size: 8pt;">Copiado</td>
            <td style="border: 1px solid #000; padding: 1px 4px; text-align: right; font-size: 8pt;">{{ number_format($campaign->copiado ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px 4px; font-weight: bold; font-size: 8pt;">
                TOTAL CAMPAÑA {{ $campaignNumber }}
            </td>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px 4px; text-align: right; font-weight: bold; font-size: 8pt;">
                {{ number_format($totalCampaign, 2) }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 2px 4px; font-weight: bold; font-size: 7.5pt;">
                Porcentaje de la campaña respecto al presupuesto total
            </td>
            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right; font-weight: bold; font-size: 7.5pt;">
                {{ number_format($porcentajeCampaign, 2) }}%
            </td>
        </tr>
    </table>
</div>

{{-- Sección de Firmas Condicional --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
    <table style="margin-top: calc(15px + 2cm); width: 100%;">
        <tr>
            <td style="text-align: center; vertical-align: bottom; padding-top: 20px;">
                <div style="border-top: 1px solid #000; margin: 0 auto; width: 60%; padding-top: 5px; font-size: 8pt;">
                    {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                    Nombre y firma del titular de comunicación social de la coordinadora sectorial
                </div>
            </td>
        </tr>
    </table>
@else
    {{-- Si NO es Sector, mostrar ambas firmas --}}
    <table class="signature-row" style="margin-top: calc(15px + 2cm);">
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
