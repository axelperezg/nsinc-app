{{-- Página 1: Misión, Visión y Objetivos --}}

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
                <div style="font-size: 12pt; font-weight: bold; color: #000; line-height: 1.3;">
                    ESTRATEGIA ANUAL DE {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}<br>
                    PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
                </div>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: top; border: none;">
                @if($logoRightPath && file_exists($logoRightPath))
                    <img src="{{ $logoRightPath }}" height="30" alt="Logo Derecho">
                @endif
            </td>
        </tr>
    </table>

    {{-- Datos Principales --}}
    <table style="width: 100%; border: 1px solid #000; border-radius: 5px; margin-bottom: 10px;">
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px 12px; font-size: 9pt; font-weight: bold; width: 22%; border-radius: 5px 0 0 0;">Dependencia o Entidad:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt;" colspan="3">{{ $estrategy->institution_name }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px 12px; font-size: 9pt; font-weight: bold;">Naturaleza Jurídica:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt; width: 28%;">{{ $estrategy->juridical_nature_name }}</td>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px 12px; font-size: 9pt; font-weight: bold; width: 22%;">Cabeza de sector:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt; width: 28%;">{{ $estrategy->institution->sector->name ?? 'No disponible' }}</td>
        </tr>
        <!--<tr>
            <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 6px 12px; font-size: 9pt; font-weight: bold; border-radius: 0 0 0 5px;">Fecha de elaboración:</td>
            <td style="border: 1px solid #000; padding: 6px 12px; font-size: 9pt; border-radius: 0 0 5px 0;" colspan="3">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
        </tr>-->
    </table>

    {{-- Misión --}}
    <div style="margin-bottom: 12px;">
        <div style="font-weight: bold; margin-bottom: 5px; font-size: 10pt; color: #333;">Misión:</div>
        <div style="border: 1px solid #000; border-radius: 5px; padding: 12px; min-height: 80px; text-align: justify; font-size: 9pt; line-height: 1.5; background-color: #fafafa;">
            {{ $estrategy->mision }}
        </div>
    </div>

    {{-- Visión --}}
    <div style="margin-bottom: 12px;">
        <div style="font-weight: bold; margin-bottom: 5px; font-size: 10pt; color: #333;">Visión:</div>
        <div style="border: 1px solid #000; border-radius: 5px; padding: 12px; min-height: 80px; text-align: justify; font-size: 9pt; line-height: 1.5; background-color: #fafafa;">
            {{ $estrategy->vision }}
        </div>
    </div>

    {{-- Objetivos (lado a lado en cuadros separados más compactos) --}}
    <table style="width: 100%; margin-bottom: 10px; border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="border: 1px solid #000; border-radius: 8px; padding: 8px; background-color: #f0f8ff;">
                    <div style="font-weight: bold; margin-bottom: 3px; font-size: 9pt; color: #2c5282;">Objetivo Institucional:</div>
                    <div style="text-align: justify; font-size: 9pt; line-height: 1.3;">
                        {{ $estrategy->objetivo_institucional }}
                    </div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div style="border: 1px solid #000; border-radius: 8px; padding: 8px; background-color: #fff8f0;">
                    <div style="font-weight: bold; margin-bottom: 3px; font-size: 9pt; color: #8b5a00;">Objetivo de la estrategia de comunicación:</div>
                    <div style="text-align: justify; font-size: 9pt; line-height: 1.3;">
                        {{ $estrategy->objetivo_estrategia }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

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
