{{-- Página de Campaña - Diseño Horizontal con Colores --}}

<div style="padding: 0 10px;">
    <table class="header-logos" style="margin-bottom: 8px;">
        <tr>
            <td class="logo-left">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" height="45" alt="Logo Izquierdo">
                @endif
            </td>
            <td class="logo-center">
                <div class="title" style="font-size: 9pt; color: #000;">
                    PROGRAMA ANUAL DE COMUNICACIÓN SOCIAL PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
                </div>
            </td>
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

    <table class="data-table" style="margin-bottom: 6px;">
        <tr>
            <td class="label-cell" style="width: 20%; padding: 3px 5px; font-size: 7pt; background-color: #d9d9d9;">Dependencia o Entidad:</td>
            <td colspan="3" style="padding: 3px 5px; font-size: 7pt;">{{ $estrategy->institution_name }}</td>
        </tr>
        <tr>
            <td class="label-cell" style="padding: 3px 5px; font-size: 7pt; background-color: #d9d9d9;">Fecha de elaboración:</td>
            <td colspan="3" style="padding: 3px 5px; font-size: 7pt;">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
        </tr>
    </table>

    <table style="width: 100%; border: 1px solid #000; margin-bottom: 6px;">
        <tr>
            <td colspan="2" style="padding: 2px 5px; text-align: right; font-size: 6.5pt; border: none;">miles de pesos / I.V.A. incluido</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 3px 5px; background-color: #9B1B30; color: #ffffff; font-weight: bold; font-size: 7pt;">
                Presupuesto anual de la dependencia o entidad destinado a la partida {{ $estrategy->partida_presupuestal }}:
            </td>
            <td style="border: 1px solid #000; padding: 3px 5px; text-align: right; background-color: #f2e4b8; font-weight: bold; width: 130px; font-size: 7pt;">
                {{ number_format($estrategy->presupuesto, 2) }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 3px 5px; background-color: #9B1B30; color: #ffffff; font-weight: bold; font-size: 7pt;">
                ACUMULADO EN CAMPAÑAS:
            </td>
            <td style="border: 1px solid #000; padding: 3px 5px; background-color: #9B1B30; color: #ffffff; text-align: right; font-weight: bold; font-size: 7pt;">
                {{ number_format($acumulado, 2) }}
            </td>
        </tr>
    </table>

    <div class="gray-header" style="margin-bottom: 6px; font-size: 9pt; background-color: #9B1B30; color: #ffffff; padding: 5px; text-align: center; font-weight: bold;">
        CAMPAÑA {{ $campaignNumber }}
    </div>

    {{-- Datos generales y Medios a utilizar en dos columnas --}}
    <table style="width: 100%; border: 1px solid #000; margin-bottom: 6px;">
        <tr>
            <td style="width: 60%; vertical-align: top; border: 1px solid #000; padding: 5px;">
                {{-- Datos generales --}}
                <div style="background-color: #9B1B30; color: #ffffff; font-weight: bold; padding: 3px; margin-bottom: 4px; font-size: 7.5pt; text-align: center;">
                    Datos generales
                </div>
                @php
                    $sexoValues = filled($campaign->sexo) ? (is_array($campaign->sexo) ? $campaign->sexo : [$campaign->sexo]) : [];
                    $edadValues = filled($campaign->edad) ? (is_array($campaign->edad) ? $campaign->edad : [$campaign->edad]) : [];
                    $poblacionValues = filled($campaign->poblacion) ? (is_array($campaign->poblacion) ? $campaign->poblacion : [$campaign->poblacion]) : [];
                    $nseValues = filled($campaign->nse) ? (is_array($campaign->nse) ? $campaign->nse : [$campaign->nse]) : [];
                @endphp
                <div style="margin-bottom: 3px; font-size: 7pt;"><strong>Nombre de la campaña:</strong> {{ $campaign->name }}</div>
                <div style="margin-bottom: 3px; font-size: 7pt;"><strong>Versión(es):</strong>
                    @if($campaign->versions && $campaign->versions->count() > 0)
                        {{ $campaign->versions->pluck('name')->implode(', ') }}
                    @else
                        Sin versiones
                    @endif
                </div>
                <div style="margin-bottom: 3px; font-size: 7pt;"><strong>Tema específico:</strong> {{ $campaign->temaEspecifco ?? 'No especificado' }}</div>
                <div style="margin-bottom: 3px; font-size: 7pt;"><strong>Objetivo de comunicación:</strong> {{ $campaign->objetivoComuicacion ?? 'No especificado' }}</div>
                <div style="margin-bottom: 3px; font-size: 7pt;"><strong>Clasificación de campaña:</strong> {{ $campaign->campaignType->name ?? 'No especificado' }}</div>
                <div style="margin-bottom: 3px; font-size: 7pt;"><strong>Coemisor:</strong> {{ $campaign->coemisores_acronyms ?? 'No especificado' }}</div>

                <div style="margin-bottom: 3px; margin-top: 4px; font-size: 7pt;"><strong>Población objetivo:</strong></div>
                <div style="margin-left: 10px; font-size: 6.5pt; line-height: 1.3;">
                    <div>HOMBRES Y MUJERES | POBLACIÓN: {{ count($poblacionValues) > 0 ? implode(', ', $poblacionValues) : 'No especificado' }}</div>
                    <div>{{ count($edadValues) > 0 ? implode(', ', $edadValues) : 'No especificado' }} AÑOS</div>
                    <div>NSE: {{ count($nseValues) > 0 ? implode(', ', $nseValues) : 'No especificado' }}</div>
                </div>

                <div style="margin-bottom: 3px; margin-top: 4px; font-size: 7pt;"><strong>Vigencia de la campaña:</strong></div>
                @if($campaign->versions && $campaign->versions->count() > 0)
                    <div style="margin-left: 10px; margin-top: 3px;">
                        <table style="width: 100%; border: 1px solid #000; font-size: 6.5pt;">
                            <tr style="background-color: #d9d9d9;">
                                <th style="border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">Etapas</th>
                                <th style="border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">Fechas</th>
                            </tr>
                            @foreach($campaign->versions as $version)
                                @php
                                    $startDate = $version->fechaInicio ? \Carbon\Carbon::parse($version->fechaInicio)->format('d \d\e F \d\e Y') : 'Sin fecha inicial';
                                    $endDate = $version->fechaFinal ? \Carbon\Carbon::parse($version->fechaFinal)->format('d \d\e F \d\e Y') : 'Sin fecha final';
                                @endphp
                                <tr>
                                    <td style="border: 1px solid #000; padding: 2px; text-align: center;">{{ $loop->iteration }}</td>
                                    <td style="border: 1px solid #000; padding: 2px;">{{ $startDate }} al {{ $endDate }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </td>
            <td style="width: 40%; vertical-align: top; border: 1px solid #000; padding: 5px;">
                {{-- Medios a utilizar --}}
                <div style="background-color: #9B1B30; color: #ffffff; font-weight: bold; padding: 3px; margin-bottom: 4px; text-align: center; font-size: 7.5pt;">
                    Medios a utilizar
                </div>
                <table style="width: 100%; border: 1px solid #000; font-size: 6.5pt;">
                    <tr>
                        <td colspan="2" style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px; text-align: center; font-weight: bold;">Tiempos oficiales</td>
                        <td colspan="2" style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px; text-align: center; font-weight: bold;">Tiempos comerciales</td>
                        <td rowspan="2" style="border: 1px solid #000; background-color: #d9d9d9; padding: 2px; text-align: center; font-weight: bold; vertical-align: middle;">Recursos<br>programados<br>por tipo medio</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; background-color: #e8e8e8; width: 10%; font-weight: bold;">TV</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; background-color: #e8e8e8; width: 10%; font-weight: bold;">Radio</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; background-color: #e8e8e8; width: 10%; font-weight: bold;">TV</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; background-color: #e8e8e8; width: 10%; font-weight: bold;">Radio</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">{{ $campaign->tv_oficial ? 'X' : '' }}</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">{{ $campaign->radio_oficial ? 'X' : '' }}</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">{{ $campaign->tv_comercial ? 'X' : '' }}</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">{{ $campaign->radio_comercial ? 'X' : '' }}</td>
                        <td style="border: 1px solid #000; padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Televisoras</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->televisoras ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Radiodifusoras</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->radiodifusoras ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Cine</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->cine ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Diarios Editados en CDMX</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->decdmx ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Diarios Editados en los Estados</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->deedos ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Diarios Editados en el Extranjero</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->deextr ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Revistas</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->revistas ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Medios Complementarios</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->mediosComplementarios ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Medios Digitales</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->mediosDigitales ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Pre-Estudios</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->preEstudios ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Post-Estudios</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->postEstudios ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Diseño</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->disenio ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Producción</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->produccion ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Preproducción</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->preProduccion ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Post-producción</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->postProduccion ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-weight: bold;">Copiado</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right;">{{ number_format($campaign->copiado ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #d9d9d9;">
                        <td colspan="4" style="border: 1px solid #000; padding: 3px; font-weight: bold; font-size: 7pt;">Presupuesto asignado a la campaña</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: right; font-weight: bold; font-size: 7pt;">{{ number_format($totalCampaign, 2) }}</td>
                    </tr>
                    <tr style="background-color: #e8e8e8;">
                        <td colspan="4" style="border: 1px solid #000; padding: 2px; font-size: 6pt;">Porcentaje que representa la campaña de la partida {{ $estrategy->partida_presupuestal }}:</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: right; font-weight: bold;">{{ number_format($porcentajeCampaign, 2) }}%</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Sección de Firmas Condicional --}}
    @if($estrategy->institution && $estrategy->institution->isSector)
        {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
        <table style="margin-top: 35px; width: 100%;">
            <tr>
                <td style="text-align: center; vertical-align: bottom; padding-top: 20px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 50%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
            </tr>
        </table>
    @else
        {{-- Si NO es Sector, mostrar ambas firmas --}}
        <table class="signature-row" style="margin-top: 35px;">
            <tr>
                <td class="signature-cell" style="padding-top: 20px;">
                    <div class="signature-line">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td class="signature-cell" style="padding-top: 20px;">
                    <div class="signature-line">
                        {{ $estrategy->responsable_name ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la dependencia/entidad
                    </div>
                </td>
            </tr>
        </table>
    @endif
</div>
