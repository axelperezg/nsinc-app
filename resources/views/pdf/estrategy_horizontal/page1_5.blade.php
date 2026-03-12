{{-- Página 1.5: PND (36101) o Entorno de Mercado y Metas (36201) --}}

{{-- Header con logos --}}
<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 14px;">
    <tr>
        <td style="width: 25%; text-align: left; vertical-align: middle; border: none;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="55" alt="Logo Izquierdo">
            @endif
        </td>
        <td style="width: 50%; border: none;"></td>
        <td style="width: 25%; text-align: right; vertical-align: middle; border: none;">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="55" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

{{-- Título --}}
<div style="text-align: center; margin-bottom: 12px;">
    <div style="font-size: 13pt; font-weight: bold; line-height: 1.4;">
        ESTRATEGIA ANUAL DE {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}<br>
        PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
    </div>
</div>

{{-- Datos de la institución --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
    <tr>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold; width: 20%;">Dependencia o Entidad:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt;" colspan="3">{{ $estrategy->institution_name }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold;">Naturaleza Jurídica:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt; width: 30%;">{{ $estrategy->juridical_nature_name }}</td>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold; width: 20%;">Cabeza de sector:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt; width: 30%;">{{ $estrategy->institution->sector->name ?? 'No disponible' }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold;">Fecha de elaboración:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt;" colspan="3">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
    </tr>
</table>

@if($estrategy->partida_presupuestal === '36101')
    {{-- ===== PLAN NACIONAL DE DESARROLLO (solo para Comunicación Social) ===== --}}

    @php
        $snapshot = $estrategy->ejes_plan_nacional_snapshot;
        $ejes_plan = $estrategy->ejes_plan_nacional ?? [];
        $pnd = $estrategy->planNacionalDesarrollo;

        if ($snapshot && !empty($snapshot['ejes'])) {
            $nombreEjesGenerales    = $snapshot['nombre_ejes_generales']    ?? 'Ejes Generales';
            $nombreEjesTransversales = $snapshot['nombre_ejes_transversales'] ?? 'Ejes Transversales';
            $selectedGenerales      = collect($snapshot['ejes'])->filter(fn($e) => ($e['tipo'] ?? '') === 'general')->pluck('label')->toArray();
            $selectedTransversales  = collect($snapshot['ejes'])->filter(fn($e) => ($e['tipo'] ?? '') === 'transversal')->pluck('label')->toArray();
            $noPndMessage = false;
        } elseif (!$pnd) {
            $nombreEjesGenerales    = 'Ejes Generales';
            $nombreEjesTransversales = 'Ejes Transversales';
            $ejesGenerales = [
                'eje_general_1_gobernanza'  => 'Eje General 1: Gobernanza con justicia y participación ciudadana',
                'eje_general_2_desarrollo'  => 'Eje General 2: Desarrollo con bienestar y humanismo',
                'eje_general_3_economia'    => 'Eje General 3: Economía moral y trabajo',
                'eje_general_4_sustentable' => 'Eje General 4: Desarrollo sustentable',
            ];
            $ejesTransversales = [
                'eje_transversal_1_igualdad'   => 'Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres',
                'eje_transversal_2_innovacion' => 'Eje Transversal 2: Innovación pública para el desarrollo tecnológico nacional',
                'eje_transversal_3_derechos'   => 'Eje Transversal 3: Derechos de los pueblos y comunidades indígenas y afromexicanas',
            ];
            $selectedGenerales     = array_values(array_filter($ejesGenerales,     fn($k) => isset($ejes_plan[$k]) && $ejes_plan[$k], ARRAY_FILTER_USE_KEY));
            $selectedTransversales = array_values(array_filter($ejesTransversales, fn($k) => isset($ejes_plan[$k]) && $ejes_plan[$k], ARRAY_FILTER_USE_KEY));
            $noPndMessage = !$estrategy->plan_nacional_desarrollo_id;
        } else {
            $nombreEjesGenerales    = $pnd->nombre_ejes_generales    ?? 'Ejes Generales';
            $nombreEjesTransversales = $pnd->nombre_ejes_transversales ?? 'Ejes Transversales';
            $ejesGenerales     = collect($pnd->ejes_generales    ?? [])->pluck('label', 'key')->toArray();
            $ejesTransversales = collect($pnd->ejes_transversales ?? [])->pluck('label', 'key')->toArray();
            $selectedGenerales     = array_values(array_filter($ejesGenerales,     fn($k) => isset($ejes_plan[$k]) && $ejes_plan[$k], ARRAY_FILTER_USE_KEY));
            $selectedTransversales = array_values(array_filter($ejesTransversales, fn($k) => isset($ejes_plan[$k]) && $ejes_plan[$k], ARRAY_FILTER_USE_KEY));
            $noPndMessage = false;
        }
    @endphp

    @if($noPndMessage)
        <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 5px 8px; margin-bottom: 0;">Plan Nacional de Desarrollo</div>
        <div style="border: 1px solid #000; border-top: none; padding: 20px; text-align: center; font-size: 9pt; color: #666; font-style: italic;">
            El Plan Nacional de Desarrollo se habilitará cuando este sea publicado
        </div>
    @else
        {{-- Ejes Generales y Transversales en dos columnas --}}
        <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 10px;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">{{ strtoupper($nombreEjesGenerales) }}</div>
                    <div style="border: 1px solid #000; border-top: none; padding: 8px; min-height: 80px;">
                        @if(count($selectedGenerales) > 0)
                            @foreach($selectedGenerales as $eje)
                                <div style="font-size: 8.5pt; padding: 3px 0; border-bottom: 1px dotted #ccc; line-height: 1.3;">{{ $eje }}</div>
                            @endforeach
                        @else
                            <div style="font-size: 8.5pt; color: #666; font-style: italic;">No se seleccionaron ejes generales</div>
                        @endif
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">{{ strtoupper($nombreEjesTransversales) }}</div>
                    <div style="border: 1px solid #000; border-top: none; padding: 8px; min-height: 80px;">
                        @if(count($selectedTransversales) > 0)
                            @foreach($selectedTransversales as $eje)
                                <div style="font-size: 8.5pt; padding: 3px 0; border-bottom: 1px dotted #ccc; line-height: 1.3;">{{ $eje }}</div>
                            @endforeach
                        @else
                            <div style="font-size: 8.5pt; color: #666; font-style: italic;">No se seleccionaron ejes transversales</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Programa Sectorial y Objetivos Estratégicos en dos columnas --}}
        <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 14px;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">Programa Sectorial y/o Especial</div>
                    <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 60px;">
                        {{ $estrategy->programa_sectorial_especial ?? 'No especificado' }}
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">Objetivos Estratégicos y/o Transversales</div>
                    <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 60px;">
                        {{ $estrategy->objetivos_estrategicos_transversales ?? 'No especificado' }}
                    </div>
                </td>
            </tr>
        </table>
    @endif

@else
    {{-- ===== ENTORNO DE MERCADO Y METAS GENERALES (solo para Promoción y Publicidad) ===== --}}

    <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; vertical-align: top; border: none;">
                <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">ENTORNO DE MERCADO</div>
                <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 200px;">
                    @if($estrategy->entorno_mercado)
                        {{ $estrategy->entorno_mercado }}
                    @else
                        <span style="color: #666; font-style: italic;">No se ha definido el entorno de mercado</span>
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; border: none;">
                <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">METAS GENERALES</div>
                <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 200px;">
                    @if($estrategy->metas_generales)
                        {{ $estrategy->metas_generales }}
                    @else
                        <span style="color: #666; font-style: italic;">No se han definido metas generales</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>
@endif

{{-- Firmas --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    <table style="margin-top: 106px; width: 100%; border: none;">
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
    <table style="margin-top: 106px; width: 100%; border: none;">
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
