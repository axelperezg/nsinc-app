{{-- Página 1: Carátula (Misión, Visión, Objetivos) --}}

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

{{-- Título principal --}}
<div style="text-align: center; margin-bottom: 12px;">
    <div style="font-size: 13pt; font-weight: bold; line-height: 1.4;">
        ESTRATEGIA ANUAL DE {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}<br>
        PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
    </div>
</div>

{{-- Datos de la institución --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
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
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold;">Partida presupuestal:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt;">{{ $estrategy->partida_presupuestal }}</td>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold;">Solicitud:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt;">{{ $estrategy->concepto }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; background-color: #d9d9d9; padding: 4px 8px; font-size: 9pt; font-weight: bold;">Fecha de elaboración:</td>
        <td style="border: 1px solid #000; padding: 4px 8px; font-size: 9pt;" colspan="3">{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
    </tr>
</table>

{{-- Misión y Visión en dos columnas --}}
<table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 8px;">
    <tr>
        <td style="width: 50%; vertical-align: top; border: none;">
            <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">Misión:</div>
            <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 70px;">
                {{ $estrategy->mision }}
            </div>
        </td>
        <td style="width: 50%; vertical-align: top; border: none;">
            <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">Visión:</div>
            <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 70px;">
                {{ $estrategy->vision }}
            </div>
        </td>
    </tr>
</table>

{{-- Objetivos en dos columnas --}}
<table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 14px;">
    <tr>
        <td style="width: 50%; vertical-align: top; border: none;">
            <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">Objetivo Institucional:</div>
            <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 60px;">
                {{ $estrategy->objetivo_institucional }}
            </div>
        </td>
        <td style="width: 50%; vertical-align: top; border: none;">
            <div style="background-color: #9B1B30; color: #fff; font-size: 9pt; font-weight: bold; padding: 4px 8px; margin-bottom: 0;">Objetivo de la estrategia de comunicación:</div>
            <div style="border: 1px solid #000; border-top: none; padding: 8px; font-size: 9pt; text-align: justify; line-height: 1.4; min-height: 60px;">
                {{ $estrategy->objetivo_estrategia }}
            </div>
        </td>
    </tr>
</table>

{{-- Firmas --}}
@if($estrategy->institution && $estrategy->institution->isSector)
    <table style="margin-top: 116px; width: 100%; border: none;">
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
    <table style="margin-top: 116px; width: 100%; border: none;">
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
