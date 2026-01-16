{{-- Página 1.5: Plan Nacional de Desarrollo --}}

<div style="padding: 0 20px;">
    {{-- Header con logos --}}
    <table style="width: 100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: top; border: none;">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" height="60" alt="Logo Izquierdo">
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle; border: none;">
                <div style="font-size: 12pt; font-weight: bold; color: #611232; line-height: 1.3;">
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
    <table style="width: 100%; border: 1px solid #000; border-radius: 5px; margin-bottom: 10px;">
        <tr>
            <td style="border: 1px solid #000; background-color: #98989A; padding: 6px 12px; font-size: 9pt; font-weight: bold; width: 22%; border-radius: 5px 0 0 0;">Dependencia o Entidad:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt;" colspan="3">{{ $estrategy->institution_name }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #98989A; padding: 6px 12px; font-size: 9pt; font-weight: bold;">Naturaleza Jurídica:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt; width: 28%;">{{ $estrategy->juridical_nature_name }}</td>
            <td style="border: 1px solid #000; background-color: #98989A; padding: 6px 12px; font-size: 9pt; font-weight: bold; width: 22%;">Cabeza de sector:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt; width: 28%;">{{ $estrategy->institution->sector->name ?? 'No disponible' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #98989A; padding: 6px 12px; font-size: 9pt; font-weight: bold; border-radius: 0 0 0 5px;">Fecha de elaboración:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt; border-radius: 0 0 5px 0;" colspan="3">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
        </tr>
    </table>

    {{-- Plan Nacional de Desarrollo --}}
    <div style="margin-bottom: 12px;">
        <div style="font-weight: bold; margin-bottom: 8px; font-size: 10pt; color: #333;">Plan Nacional de Desarrollo:</div>
        <div style="border: 1px solid #000; border-radius: 5px; padding: 12px; background-color: #EFD0C8;">
            @php
                $snapshot = $estrategy->ejes_plan_nacional_snapshot;
                $ejes_plan = $estrategy->ejes_plan_nacional ?? [];
                $pnd = $estrategy->planNacionalDesarrollo;

                // Usar snapshot si existe (preserva datos históricos)
                if ($snapshot && !empty($snapshot['ejes'])) {
                    $nombreEjesGenerales = $snapshot['nombre_ejes_generales'] ?? 'Ejes Generales';
                    $nombreEjesTransversales = $snapshot['nombre_ejes_transversales'] ?? 'Ejes Transversales';

                    $selectedGenerales = collect($snapshot['ejes'])
                        ->filter(fn($eje) => ($eje['tipo'] ?? '') === 'general')
                        ->pluck('label')
                        ->toArray();

                    $selectedTransversales = collect($snapshot['ejes'])
                        ->filter(fn($eje) => ($eje['tipo'] ?? '') === 'transversal')
                        ->pluck('label')
                        ->toArray();

                    $noPndMessage = false;
                } elseif (!$pnd) {
                    // Fallback a ejes hardcodeados para estrategias antiguas sin snapshot
                    $nombreEjesGenerales = 'Ejes Generales';
                    $nombreEjesTransversales = 'Ejes Transversales';

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

                    $selectedGenerales = array_values(array_filter($ejesGenerales, fn($key) => isset($ejes_plan[$key]) && $ejes_plan[$key], ARRAY_FILTER_USE_KEY));
                    $selectedTransversales = array_values(array_filter($ejesTransversales, fn($key) => isset($ejes_plan[$key]) && $ejes_plan[$key], ARRAY_FILTER_USE_KEY));

                    // Verificar si la estrategia fue creada sin PND activo
                    $noPndMessage = !$estrategy->plan_nacional_desarrollo_id;
                } else {
                    // Cargar ejes desde el PND asociado (fallback para estrategias sin snapshot)
                    $nombreEjesGenerales = $pnd->nombre_ejes_generales ?? 'Ejes Generales';
                    $nombreEjesTransversales = $pnd->nombre_ejes_transversales ?? 'Ejes Transversales';

                    $ejesGenerales = collect($pnd->ejes_generales ?? [])->pluck('label', 'key')->toArray();
                    $ejesTransversales = collect($pnd->ejes_transversales ?? [])->pluck('label', 'key')->toArray();

                    $selectedGenerales = array_values(array_filter($ejesGenerales, fn($key) => isset($ejes_plan[$key]) && $ejes_plan[$key], ARRAY_FILTER_USE_KEY));
                    $selectedTransversales = array_values(array_filter($ejesTransversales, fn($key) => isset($ejes_plan[$key]) && $ejes_plan[$key], ARRAY_FILTER_USE_KEY));

                    $noPndMessage = false;
                }
            @endphp

            @if($noPndMessage)
                {{-- Mostrar mensaje si fue creada sin PND activo --}}
                <div style="font-size: 10pt; color: #666; font-style: italic; text-align: center; padding: 20px;">
                    El Plan Nacional de Desarrollo se habilitará cuando este sea publicado
                </div>
            @else
                {{-- Ejes Generales con nombre dinámico --}}
                <div style="margin-bottom: 16px;">
                    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 8px; color: #F9EEEB;">
                        {{ $nombreEjesGenerales }}:
                    </div>
                    @if(count($selectedGenerales) > 0)
                        <div>
                            @foreach($selectedGenerales as $ejeTexto)
                                <span class="eje-badge">{{ $ejeTexto }}</span>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size: 9pt; color: #666; font-style: italic;">
                            No se seleccionaron {{ strtolower($nombreEjesGenerales) }}
                        </div>
                    @endif
                </div>

                {{-- Ejes Transversales con nombre dinámico --}}
                <div>
                    <div style="font-weight: bold; font-size: 9pt; margin-bottom: 8px; color: #8b5a00;">
                        {{ $nombreEjesTransversales }}:
                    </div>
                    @if(count($selectedTransversales) > 0)
                        <div>
                            @foreach($selectedTransversales as $ejeTexto)
                                <span class="eje-transversal-badge">{{ $ejeTexto }}</span>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size: 9pt; color: #666; font-style: italic;">
                            No se seleccionaron {{ strtolower($nombreEjesTransversales) }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Programa Sectorial y/o Especial --}}
    <div style="margin-bottom: 10px;">
        <div style="font-weight: bold; margin-bottom: 5px; font-size: 9pt; color: #333;">Programa Sectorial y/o Especial:</div>
        <div style="border: 1px solid #000; border-radius: 5px; padding: 12px; min-height: 50px; text-align: justify; font-size: 9pt; line-height: 1.5; background-color: #F9EEEB;">
            {{ $estrategy->programa_sectorial_especial ?? 'No especificado' }}
        </div>
    </div>

    {{-- Objetivos Estratégicos y/o Transversales --}}
    <div style="margin-bottom: 12px;">
        <div style="font-weight: bold; margin-bottom: 5px; font-size: 9pt; color: #333;">Objetivos Estratégicos y/o Transversales:</div>
        <div style="border: 1px solid #000; border-radius: 5px; padding: 12px; min-height: 50px; text-align: justify; font-size: 9pt; line-height: 1.5; background-color: #F9EEEB;">
            {{ $estrategy->objetivos_estrategicos_transversales ?? 'No especificado' }}
        </div>
    </div>

    {{-- Sección de Firmas --}}
    @if($estrategy->institution && $estrategy->institution->isSector)
        {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
        <table style="margin-top: 40px; width: 100%;">
            <tr>
                <td style="text-align: center; vertical-align: bottom; padding-top: 30px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 50%; padding-top: 5px; font-size: 9pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
            </tr>
        </table>
    @else
        {{-- Si NO es Sector, mostrar ambas firmas --}}
        <table style="margin-top: 40px; width: 100%;">
            <tr>
                <td style="width: 48%; text-align: center; vertical-align: bottom; padding-top: 30px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 85%; padding-top: 5px; font-size: 9pt;">
                        {{ $estrategy->NombreSectorResponsable ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la coordinadora sectorial
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; text-align: center; vertical-align: bottom; padding-top: 30px;">
                    <div style="border-top: 1px solid #000; margin: 0 auto; width: 85%; padding-top: 5px; font-size: 9pt;">
                        {{ $estrategy->responsable_name ?? '_________________________________' }}<br>
                        Nombre y firma del titular de comunicación social de la dependencia/entidad
                    </div>
                </td>
            </tr>
        </table>
    @endif
</div>
