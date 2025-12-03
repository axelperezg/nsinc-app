{{-- Página 2: Resumen de Medios y Distribución Presupuestal - Diseño Horizontal según PDF de Referencia --}}

<div style="padding: 0 10px;">
    <table style="width: 100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: top; border: none;">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" height="60" alt="Logo Izquierdo">
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle; border: none;">
                <div style="font-size: 11pt; font-weight: bold; color: #000; line-height: 1.2;">
                    ESTRATEGIA ANUAL DE {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}<br>
                    PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
                </div>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: top; border: none;">
                @if($logoRightPath && file_exists($logoRightPath))
                    <img src="{{ $logoRightPath }}" height="60" alt="Logo Derecho">
                @endif
            </td>
        </tr>
    </table>

    @php
        // Calcular totales por categoría
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

    {{-- Tabla de Programa Sectorial, Objetivos y Temas --}}
    <table style="width: 100%; border: 1px solid #000; margin-bottom: 10px;">
        <tr>
            <th style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold; width: 33%; text-align: center;">
                Programa Sectorial y/o Especial
            </th>
            <th style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold; width: 33%; text-align: center;">
                Objetivos Estratégicos y/o Transversales
            </th>
            <th style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold; width: 34%; text-align: center;">
                Temas Específicos Derivadores de los Objetivos Estratégicos y/o Transversales
            </th>
        </tr>
        @if($estrategy->campaigns && $estrategy->campaigns->count() > 0)
            @foreach($estrategy->campaigns->take(2) as $index => $campaign)
            <tr>
                <td style="border: 1px solid #000; padding: 5px; font-size: 7pt; vertical-align: top;">
                    Este campo será requisitado hasta que se publique el Plan Nacional de Desarrollo 2025-2030
                </td>
                <td style="border: 1px solid #000; padding: 5px; font-size: 7pt; vertical-align: top;">
                    Este campo será requisitado hasta que se publique el Plan Nacional de Desarrollo 2025-2030
                </td>
                <td style="border: 1px solid #000; padding: 5px; font-size: 7pt; vertical-align: top;">
                    {{ $index + 1 }}.- {{ $campaign->temaEspecifco ?? 'No especificado' }}
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td style="border: 1px solid #000; padding: 5px; font-size: 7pt;">
                    Este campo será requisitado hasta que se publique el Plan Nacional de Desarrollo 2025-2030
                </td>
                <td style="border: 1px solid #000; padding: 5px; font-size: 7pt;">
                    Este campo será requisitado hasta que se publique el Plan Nacional de Desarrollo 2025-2030
                </td>
                <td style="border: 1px solid #000; padding: 5px; font-size: 7pt;">
                    Sin campañas definidas
                </td>
            </tr>
        @endif
    </table>

    {{-- Tabla de Resumen de Medios --}}
    <table style="width: 100%; border: 1px solid #000; margin-bottom: 10px;">
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold; width: 70%;">
                MEDIOS ELECTRÓNICOS
            </td>
            <td style="border: 1px solid #000; padding: 4px; font-size: 7.5pt; text-align: right; width: 30%;">
                {{ number_format($totalElectronicos, 2) }}
            </td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold;">
                MEDIOS IMPRESOS
            </td>
            <td style="border: 1px solid #000; padding: 4px; font-size: 7.5pt; text-align: right;">
                {{ number_format($totalImpresos, 2) }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold;">
                MEDIOS COMPLEMENTARIOS
            </td>
            <td style="border: 1px solid #000; padding: 4px; font-size: 7.5pt; text-align: right;">
                {{ number_format($totalComplementarios, 2) }}
            </td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold;">
                ESTUDIOS
            </td>
            <td style="border: 1px solid #000; padding: 4px; font-size: 7.5pt; text-align: right;">
                {{ number_format($totalEstudios, 2) }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px; font-size: 7.5pt; font-weight: bold;">
                DISEÑO, PRODUCCIÓN, POST-PRODUCCIÓN
            </td>
            <td style="border: 1px solid #000; padding: 4px; font-size: 7.5pt; text-align: right;">
                {{ number_format($totalDisenoProduccion, 2) }}
            </td>
        </tr>
        <tr style="background-color: #d9d9d9;">
            <td style="border: 1px solid #000; padding: 5px; font-size: 8pt; font-weight: bold;">
                TOTAL
            </td>
            <td style="border: 1px solid #000; padding: 5px; font-size: 8pt; text-align: right; font-weight: bold;">
                {{ number_format($granTotal, 2) }}
            </td>
        </tr>
    </table>

    {{-- Sección de Firmas Condicional --}}
    @if($estrategy->institution && $estrategy->institution->isSector)
        {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
        <table style="margin-top: 60px; width: 100%;">
            <tr>
                <td style="text-align: center; vertical-align: bottom; padding-top: 30px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 50%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
            </tr>
        </table>
    @else
        {{-- Si NO es Sector, mostrar ambas firmas --}}
        <table style="margin-top: 60px; width: 100%;">
            <tr>
                <td style="width: 48%; text-align: center; vertical-align: bottom; padding-top: 30px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 80%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; text-align: center; vertical-align: bottom; padding-top: 30px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 80%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->responsable_name ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la dependencia/entidad
                    </div>
                </td>
            </tr>
        </table>
    @endif
</div>
