<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Estrategia de Comunicación Social {{ $estrategy->anio }}</title>
    <style>
        @page {
            size: letter portrait;
            margin-top: 1.5cm;
            margin-bottom: 1.5cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
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
            margin-bottom: 10px;
        }
        .header-logos td {
            vertical-align: middle;
        }
        .logo-left {
            width: 30%;
            text-align: left;
        }
        .logo-right {
            width: 30%;
            text-align: right;
        }
        .logo-center {
            width: 40%;
            text-align: center;
        }
        .title {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .data-table {
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 10px;
        }
        .data-table td, .data-table th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .data-table .label-cell {
            background-color: #d9d9d9;
            font-weight: bold;
            width: 25%;
        }
        .section-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 8px;
            min-height: 60px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }
        .checkbox.checked::after {
            content: 'X';
            display: block;
            text-align: center;
            line-height: 12px;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
        }
        .signature-row {
            width: 100%;
        }
        .signature-cell {
            width: 48%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 40px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 80%;
            padding-top: 5px;
            font-size: 8pt;
        }
        .budget-summary {
            margin: 15px 0;
        }
        .budget-row {
            display: block;
            margin: 5px 0;
        }
        .budget-label {
            display: inline-block;
            width: 60%;
            text-align: left;
            font-weight: bold;
        }
        .budget-value {
            display: inline-block;
            width: 35%;
            text-align: right;
        }
        .gray-header {
            background-color: #d9d9d9;
            font-weight: bold;
            padding: 6px;
            text-align: center;
        }
        .campaign-table {
            width: 100%;
            border: 1px solid #000;
        }
        .campaign-table td, .campaign-table th {
            border: 1px solid #000;
            padding: 4px;
            font-size: 8pt;
        }
        .campaign-left {
            width: 50%;
            vertical-align: top;
        }
        .campaign-right {
            width: 50%;
            vertical-align: top;
        }
        .budget-item {
            padding: 2px 4px;
        }
        .total-row {
            background-color: #d9d9d9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Página 1: Carátula de Estrategia --}}
    @include('pdf.estrategy.page1', ['estrategy' => $estrategy, 'logoPath' => $logoPath ?? null, 'logoRightPath' => $logoRightPath ?? null])

    {{-- Páginas 2+: Una por cada campaña --}}
    @foreach($estrategy->campaigns as $index => $campaign)
        <div class="page-break"></div>
        @include('pdf.estrategy.campaign', ['estrategy' => $estrategy, 'campaign' => $campaign, 'campaignNumber' => $index + 1, 'logoPath' => $logoPath ?? null, 'logoRightPath' => $logoRightPath ?? null])
    @endforeach
</body>
</html>
