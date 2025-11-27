{{-- Página 1: Carátula de Estrategia Anual --}}

<table class="header-logos" style="margin-top: 15px;">
    <tr>
        <td class="logo-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" height="60" alt="Logo Izquierdo">
            @endif
        </td>
        <td class="logo-center"></td>
        <td class="logo-right">
            @if($logoRightPath && file_exists($logoRightPath))
                <img src="{{ $logoRightPath }}" height="60" alt="Logo Derecho">
            @endif
        </td>
    </tr>
</table>

<div class="title">
    ESTRATEGIA ANUAL DE {{ $estrategy->partida_presupuestal === '36101' ? 'COMUNICACIÓN SOCIAL' : 'PROMOCIÓN Y PUBLICIDAD' }}<br>
    PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}
</div>

{{-- Datos sin bordes en dos columnas --}}
<div style="margin-bottom: 15px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding-right: 10px; vertical-align: top;">
                <div style="margin-bottom: 3px;">
                    <span style="font-weight: bold;">Dependencia o Entidad:</span>
                    <br>
                    <span>{{ $estrategy->institution_name }}</span>
                </div>
                 <div style="margin-bottom: 3px;">
                    <span style="font-weight: bold;">Partida Presupuestal:</span>
                    <span>{{ $estrategy->partida_presupuestal }}</span>
                </div>
                <div style="margin-bottom: 3px;">
                    <span style="font-weight: bold;">Naturaleza Jurídica:</span>
                    <span>{{ $estrategy->juridical_nature_name }}</span>
                </div>
            </td>
            <td style="width: 50%; padding-left: 10px; vertical-align: top;">
                <div style="margin-bottom: 3px;">
                    <span style="font-weight: bold;">Cabeza de sector:</span>
                    <span>{{ $estrategy->institution->sector->name ?? 'No disponible' }}</span>
                </div>
                <div style="margin-bottom: 3px;">
                    <span style="font-weight: bold;">Fecha de elaboración:</span>
                    <span>{{ \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</span>
                </div>
                <div style="margin-bottom: 3px;">
                    <span style="font-weight: bold;">Solicitud:</span>
                    <span>{{ $estrategy->concepto }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- Secciones con marcos redondeados --}}
<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Misión:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->mision }}</div>
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Visión:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->vision }}</div>
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Objetivo Institucional:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->objetivo_institucional }}</div>
    </div>
</div>

<div style="margin-bottom: 12px;">
    <div style="font-weight: bold; margin-bottom: 3px;">Objetivo de la estrategia de comunicación:</div>
    <div style="border: 1px solid #000; border-radius: 8px; padding: 8px;">
        <div style="text-align: justify; font-size: 8pt;">{{ $estrategy->objetivo_estrategia }}</div>
    </div>
</div>

{{-- Sección de Firmas Condicional --}}
<div class="signature-container">
    {{-- Separador que agrega 50px cuando las firmas quedan en nueva página --}}
    <div class="signature-spacer"></div>
    <div class="signature-content">
        @if($estrategy->institution && $estrategy->institution->isSector)
            {{-- Si es Sector, solo mostrar firma del Responsable de Sector centrada --}}
            <table style="margin-top: 10px; width: 100%;">
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
            <table class="signature-row" style="margin-top: 10px;">
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
    </div>
</div>
