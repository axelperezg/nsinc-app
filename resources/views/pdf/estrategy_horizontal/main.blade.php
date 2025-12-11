<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Estrategia de {{ $estrategy->partida_presupuestal === '36101' ? 'Comunicación Social' : 'Promoción y Publicidad' }} {{ $estrategy->anio }}</title>
    <style>
        @page {
            size: letter landscape;
            margin-top: 1.5cm;
            margin-bottom: 1.5cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            position: relative;
        }
        .page-break {
            page-break-after: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logos {
            width: 100%;
            margin-bottom: 8px;
        }
        .header-logos td {
            vertical-align: middle;
        }
        .logo-left {
            width: 20%;
            text-align: left;
        }
        .logo-right {
            width: 20%;
            text-align: right;
        }
        .logo-center {
            width: 60%;
            text-align: center;
        }
        .title {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin: 8px 0;
        }
        .data-table {
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 8px;
        }
        .data-table td, .data-table th {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8pt;
        }
        .data-table .label-cell {
            background-color: #d9d9d9;
            font-weight: bold;
            width: 25%;
        }
        .section-box {
            border: 1px solid #000;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 8px;
            min-height: 50px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            margin-right: 3px;
            vertical-align: middle;
        }
        .checkbox.checked::after {
            content: 'X';
            display: block;
            text-align: center;
            line-height: 10px;
            font-weight: bold;
            font-size: 8pt;
        }
        .signature-section {
            margin-top: 25px;
        }
        .signature-row {
            width: 100%;
        }
        .signature-cell {
            width: 48%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 30px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 80%;
            padding-top: 3px;
            font-size: 7pt;
        }
        .gray-header {
            background-color: #d9d9d9;
            font-weight: bold;
            padding: 4px;
            text-align: center;
            font-size: 9pt;
        }
        .eje-badge {
            display: inline-block;
            background-color: #e8f4f8;
            border: 1px solid #5b9bd5;
            border-radius: 5px;
            padding: 6px 12px;
            margin: 4px 4px 4px 0;
            font-size: 9pt;
            line-height: 1.3;
        }
        .eje-transversal-badge {
            display: inline-block;
            background-color: #fff4e6;
            border: 1px solid #f0ad4e;
            border-radius: 5px;
            padding: 6px 12px;
            margin: 4px 4px 4px 0;
            font-size: 9pt;
            line-height: 1.3;
        }
        .subsection-title {
            font-weight: bold;
            font-size: 9pt;
            margin-top: 8px;
            margin-bottom: 6px;
            color: #333;
        }
    </style>
</head>
<body>
    {{-- Página 1: Misión, Visión y Objetivos --}}
    @include('pdf.estrategy_horizontal.page1', ['estrategy' => $estrategy, 'logoPath' => $logoPath ?? null, 'logoRightPath' => $logoRightPath ?? null])

    {{-- Página 1.5: Plan Nacional de Desarrollo (solo para 36101) --}}
    @if($estrategy->partida_presupuestal === '36101')
        <div class="page-break"></div>
        @include('pdf.estrategy_horizontal.page1_5', ['estrategy' => $estrategy, 'logoPath' => $logoPath ?? null, 'logoRightPath' => $logoRightPath ?? null])
    @endif

    {{-- Página 2: Resumen de Medios y Distribución Presupuestal --}}
    <div class="page-break"></div>
    @include('pdf.estrategy_horizontal.page2', ['estrategy' => $estrategy, 'logoPath' => $logoPath ?? null, 'logoRightPath' => $logoRightPath ?? null])

    {{-- Páginas 3+: Una por cada campaña --}}
    @foreach($estrategy->campaigns as $index => $campaign)
        <div class="page-break"></div>
        @include('pdf.estrategy_horizontal.campaign', ['estrategy' => $estrategy, 'campaign' => $campaign, 'campaignNumber' => $index + 1, 'logoPath' => $logoPath ?? null, 'logoRightPath' => $logoRightPath ?? null])
    @endforeach
</body>
</html>
