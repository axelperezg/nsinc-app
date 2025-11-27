{{-- Página intermedia: Plan Nacional de Desarrollo (36101) o Entorno de Mercado y Metas (36201) --}}

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

@if($estrategy->partida_presupuestal === '36101')
    {{-- PLAN NACIONAL DE DESARROLLO para Comunicación Social --}}
    <div class="title" style="font-size: 12pt; margin-bottom: 20px;">
        PLAN NACIONAL DE DESARROLLO
    </div>

    @if($estrategy->ejes_plan_nacional && count($estrategy->ejes_plan_nacional) > 0)
        @php
            $ejesGenerales = [
                'eje_general_1_gobernanza' => 'Eje General 1: Gobernanza con justicia y participación ciudadana',
                'eje_general_2_desarrollo' => 'Eje General 2: Desarrollo con bienestar y humanismo',
                'eje_general_3_economia' => 'Eje General 3: Economía moral y trabajo',
                'eje_general_4_sustentable' => 'Eje General 4: Desarrollo sustentable',
            ];

            $ejesTransversales = [
                'eje_transversal_1_igualdad' => 'Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres',
                'eje_transversal_2_innovacion' => 'Eje Transversal 2: Innovación pública para el desarrollo tecnológico nacional',
                'eje_transversal_3_derechos' => 'Eje Transversal 3: Derechos de los pueblos y comunidades indígenas y afromexicanas',
            ];

            $selectedGenerales = array_filter($ejesGenerales, fn($key) => isset($estrategy->ejes_plan_nacional[$key]) && $estrategy->ejes_plan_nacional[$key], ARRAY_FILTER_USE_KEY);
            $selectedTransversales = array_filter($ejesTransversales, fn($key) => isset($estrategy->ejes_plan_nacional[$key]) && $estrategy->ejes_plan_nacional[$key], ARRAY_FILTER_USE_KEY);
        @endphp

        {{-- Ejes Generales --}}
        @if(count($selectedGenerales) > 0)
        <div style="margin-bottom: 20px;">
            <div class="gray-header" style="margin-bottom: 10px;">
                EJES GENERALES
            </div>
            <table class="data-table">
                @foreach($selectedGenerales as $eje)
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; font-size: 9pt; text-align: justify;">
                        {{ $eje }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        {{-- Ejes Transversales --}}
        @if(count($selectedTransversales) > 0)
        <div style="margin-bottom: 20px;">
            <div class="gray-header" style="margin-bottom: 10px;">
                EJES TRANSVERSALES
            </div>
            <table class="data-table">
                @foreach($selectedTransversales as $eje)
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; font-size: 9pt; text-align: justify;">
                        {{ $eje }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        @if(count($selectedGenerales) == 0 && count($selectedTransversales) == 0)
        <div style="text-align: center; color: #666; font-style: italic; padding: 20px;">
            No se han seleccionado ejes del Plan Nacional de Desarrollo
        </div>
        @endif
    @else
        <div style="text-align: center; color: #666; font-style: italic; padding: 20px;">
            No se han definido ejes del Plan Nacional de Desarrollo para esta estrategia
        </div>
    @endif

@else
    {{-- ENTORNO DE MERCADO Y METAS GENERALES para Promoción y Publicidad --}}
    <div class="title" style="font-size: 12pt; margin-bottom: 20px;">
        ENTORNO DE MERCADO Y METAS GENERALES
    </div>

    {{-- Entorno de Mercado --}}
    <div style="margin-bottom: 20px;">
        <div class="gray-header" style="margin-bottom: 10px;">
            ENTORNO DE MERCADO
        </div>
        <div class="section-box" style="min-height: 150px;">
            @if($estrategy->entorno_mercado)
                <p style="text-align: justify; font-size: 9pt; line-height: 1.5;">
                    {{ $estrategy->entorno_mercado }}
                </p>
            @else
                <p style="text-align: center; color: #666; font-style: italic;">
                    No se ha definido el entorno de mercado
                </p>
            @endif
        </div>
    </div>

    {{-- Metas Generales --}}
    <div style="margin-bottom: 20px;">
        <div class="gray-header" style="margin-bottom: 10px;">
            METAS GENERALES
        </div>
        <div class="section-box" style="min-height: 150px;">
            @if($estrategy->metas_generales)
                <p style="text-align: justify; font-size: 9pt; line-height: 1.5;">
                    {{ $estrategy->metas_generales }}
                </p>
            @else
                <p style="text-align: center; color: #666; font-style: italic;">
                    No se han definido metas generales
                </p>
            @endif
        </div>
    </div>
@endif

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
