{{-- Página 1: Carátula de Estrategia Anual - Diseño Horizontal según PDF de Referencia --}}

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

    {{-- Datos Principales --}}
    <table style="width: 100%; border: 1px solid #000; margin-bottom: 8px;">
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 3px 5px; font-size: 7.5pt; font-weight: bold; width: 20%;">Dependencia o Entidad:</td>
            <td style="border: 1px solid #000; padding: 3px 5px; font-size: 7.5pt;" colspan="3">{{ $estrategy->institution_name }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 3px 5px; font-size: 7.5pt; font-weight: bold;">Naturaleza Jurídica:</td>
            <td style="border: 1px solid #000; padding: 3px 5px; font-size: 7.5pt; width: 30%;">{{ $estrategy->juridical_nature_name }}</td>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 3px 5px; font-size: 7.5pt; font-weight: bold; width: 20%;">Cabeza de sector:</td>
            <td style="border: 1px solid #000; padding: 3px 5px; font-size: 7.5pt; width: 30%;">{{ $estrategy->institution->sector->name ?? 'No disponible' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 3px 5px; font-size: 7.5pt; font-weight: bold;">Fecha de elaboración:</td>
            <td style="border: 1px solid #000; padding: 3px 5px; font-size: 7.5pt;" colspan="3">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
        </tr>
    </table>

    {{-- Misión --}}
    <div style="margin-bottom: 8px;">
        <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Misión:</div>
        <div style="border: 1px solid #000; padding: 6px; min-height: 60px; text-align: justify; font-size: 7.5pt; line-height: 1.3;">
            {{ $estrategy->mision }}
        </div>
    </div>

    {{-- Visión --}}
    <div style="margin-bottom: 8px;">
        <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Visión:</div>
        <div style="border: 1px solid #000; padding: 6px; min-height: 60px; text-align: justify; font-size: 7.5pt; line-height: 1.3;">
            {{ $estrategy->vision }}
        </div>
    </div>

    {{-- Objetivo Institucional --}}
    <div style="margin-bottom: 8px;">
        <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Objetivo Institucional:</div>
        <div style="border: 1px solid #000; padding: 6px; min-height: 50px; text-align: justify; font-size: 7.5pt; line-height: 1.3;">
            {{ $estrategy->objetivo_institucional }}
        </div>
    </div>

    {{-- Objetivo de la estrategia de comunicación --}}
    <div style="margin-bottom: 8px;">
        <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Objetivo de la estrategia de comunicación:</div>
        <div style="border: 1px solid #000; padding: 6px; min-height: 50px; text-align: justify; font-size: 7.5pt; line-height: 1.3;">
            {{ $estrategy->objetivo_estrategia }}
        </div>
    </div>

    {{-- Metas Nacionales del PND (solo para 36101) o Entorno de Mercado y Metas (solo para 36201) --}}
    @if($estrategy->partida_presupuestal === '36101')
        {{-- Plan Nacional de Desarrollo para Comunicación Social --}}
        <div style="margin-bottom: 6px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Metas Nacionales del PND (elija con una "x"):</div>
            @php
                $ejes_plan = $estrategy->ejes_plan_nacional ?? [];
                $eje1 = isset($ejes_plan['eje_general_1_gobernanza']) && $ejes_plan['eje_general_1_gobernanza'];
                $eje2 = isset($ejes_plan['eje_general_2_desarrollo']) && $ejes_plan['eje_general_2_desarrollo'];
                $eje3 = isset($ejes_plan['eje_general_3_economia']) && $ejes_plan['eje_general_3_economia'];
                $eje4 = isset($ejes_plan['eje_general_4_sustentable']) && $ejes_plan['eje_general_4_sustentable'];
            @endphp
            <div style="border: 1px solid #000; padding: 5px; font-size: 7.5pt; margin-bottom: 4px;">
                <span class="checkbox {{ $eje1 ? 'checked' : '' }}"></span> 1.- PND 2025-2030 Eje General 1
                <span class="checkbox {{ $eje2 ? 'checked' : '' }}"></span> Eje General 2
                <span class="checkbox {{ $eje3 ? 'checked' : '' }}"></span> Eje General 3
                <span class="checkbox {{ $eje4 ? 'checked' : '' }}"></span> Eje General 4
            </div>
            @php
                $ejeT1 = isset($ejes_plan['eje_transversal_1_igualdad']) && $ejes_plan['eje_transversal_1_igualdad'];
                $ejeT2 = isset($ejes_plan['eje_transversal_2_innovacion']) && $ejes_plan['eje_transversal_2_innovacion'];
                $ejeT3 = isset($ejes_plan['eje_transversal_3_derechos']) && $ejes_plan['eje_transversal_3_derechos'];
            @endphp
            <div style="border: 1px solid #000; padding: 5px; font-size: 7.5pt; margin-bottom: 6px;">
                Ejes T:
                <span class="checkbox {{ $ejeT1 ? 'checked' : '' }}"></span> Igualdad Sustantiva y derechos de las mujeres
                <span class="checkbox {{ $ejeT2 ? 'checked' : '' }}"></span> Innovación pública para el desarrollo tecnológico nacional
                <span class="checkbox {{ $ejeT3 ? 'checked' : '' }}"></span> Derechos de las comunidades indígenas y afromexicanas
            </div>
        </div>

        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Meta (s) nacional (es) que regirán el programa de comunicación:</div>
            <div style="border: 1px solid #000; padding: 6px; min-height: 45px; font-size: 7.5pt; line-height: 1.3;">
                @if($estrategy->ejes_plan_nacional && count($estrategy->ejes_plan_nacional) > 0)
                    @php
                        $ejesGenerales = [
                            'eje_general_1_gobernanza' => 'PND 2025-2030 Eje General 1',
                            'eje_general_2_desarrollo' => 'Eje General 2',
                            'eje_general_3_economia' => 'Eje General 3',
                            'eje_general_4_sustentable' => 'Eje General 4',
                        ];
                        $ejesTransversales = [
                            'eje_transversal_1_igualdad' => 'Igualdad Sustantiva y derechos de las mujeres',
                            'eje_transversal_2_innovacion' => 'Innovación pública para el desarrollo tecnológico nacional',
                            'eje_transversal_3_derechos' => 'Derechos de los pueblos y comunidades indígenas y afromexicanas',
                        ];
                        $selectedGenerales = array_filter($ejesGenerales, fn($key) => isset($estrategy->ejes_plan_nacional[$key]) && $estrategy->ejes_plan_nacional[$key], ARRAY_FILTER_USE_KEY);
                        $selectedTransversales = array_filter($ejesTransversales, fn($key) => isset($estrategy->ejes_plan_nacional[$key]) && $estrategy->ejes_plan_nacional[$key], ARRAY_FILTER_USE_KEY);
                        $allSelected = array_merge($selectedGenerales, $selectedTransversales);
                    @endphp
                    {{ implode(' [] ', $allSelected) }}{{ count($allSelected) > 0 ? ' []' : '' }}<br>
                    Ejes T: {{ implode(' [] ', array_values($selectedTransversales)) }}{{ count($selectedTransversales) > 0 ? ' []' : '' }}
                @else
                    PND 2025-2030 Eje General 1 [] Eje General 2 [] Eje General 3 [] Eje General 4 [] Selecciona manualmente la opción aplicable.<br>
                    Ejes T: Igualdad Sustantiva y derechos de las mujeres [] Innovación pública para el desarrollo tecnológico nacional [] Derechos de las comunidades indígenas y afromexicanas [].
                @endif
            </div>
        </div>
    @else
        {{-- Entorno de Mercado y Metas Generales para Promoción y Publicidad --}}
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Entorno de Mercado:</div>
            <div style="border: 1px solid #000; padding: 6px; min-height: 55px; text-align: justify; font-size: 7.5pt; line-height: 1.3;">
                {{ $estrategy->entorno_mercado ?? 'No especificado' }}
            </div>
        </div>

        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 8pt;">Metas Generales:</div>
            <div style="border: 1px solid #000; padding: 6px; min-height: 55px; text-align: justify; font-size: 7.5pt; line-height: 1.3;">
                {{ $estrategy->metas_generales ?? 'No especificado' }}
            </div>
        </div>
    @endif

    {{-- Sección de Firmas Condicional --}}
    @if($estrategy->institution && $estrategy->institution->isSector)
        {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
        <table style="margin-top: 30px; width: 100%;">
            <tr>
                <td style="text-align: center; vertical-align: bottom; padding-top: 25px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 50%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
            </tr>
        </table>
    @else
        {{-- Si NO es Sector, mostrar ambas firmas --}}
        <table style="margin-top: 30px; width: 100%;">
            <tr>
                <td style="width: 48%; text-align: center; vertical-align: bottom; padding-top: 25px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 80%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; text-align: center; vertical-align: bottom; padding-top: 25px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 80%; padding-top: 3px; font-size: 7pt;">
                        {{ $estrategy->responsable_name ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la dependencia/entidad
                    </div>
                </td>
            </tr>
        </table>
    @endif
</div>
