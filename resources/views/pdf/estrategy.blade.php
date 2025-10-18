<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estrategia de Comunicación {{ $estrategy->anio }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #9B1B30;
        }
        .header-logo {
            text-align: center;
            margin-bottom: 10px;
        }
        .header-logo img {
            max-width: 150px;
            max-height: 60px;
            height: auto;
        }
        .header h1 {
            color: #9B1B30;
            font-size: 16pt;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }
        .header h2 {
            color: #64748b;
            font-size: 11pt;
            margin: 4px 0;
            font-weight: normal;
        }
        .page-header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #9B1B30;
        }
        .page-header-logo {
            text-align: center;
            margin-bottom: 8px;
        }
        .page-header-logo img {
            max-width: 120px;
            max-height: 50px;
            height: auto;
        }
        .page-header h3 {
            color: #9B1B30;
            font-size: 12pt;
            margin: 0;
            font-weight: bold;
        }
        .campaign-page {
            page-break-before: always;
        }
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #9B1B30;
            color: white;
            padding: 6px 10px;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 4px;
            background-color: #F5F5F5;
            border: 1px solid #cbd5e1;
            width: 30%;
            font-size: 8pt;
        }
        .info-value {
            display: table-cell;
            padding: 4px;
            border: 1px solid #cbd5e1;
            font-size: 8pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-success {
            background-color: #10b981;
            color: white;
        }
        .badge-warning {
            background-color: #f59e0b;
            color: white;
        }
        .badge-info {
            background-color: #9B1B30;
            color: white;
        }
        .badge-danger {
            background-color: #ef4444;
            color: white;
        }
        .campaign {
            border: 2px solid #9B1B30;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #FAFAFA;
            page-break-after: always;
            page-break-inside: avoid;
        }
        .campaign-title {
            font-size: 14pt;
            font-weight: bold;
            color: #9B1B30;
            margin: 15px 0 8px 0;
            padding: 8px;
            background-color: #F5F5F5;
            border-left: 5px solid #9B1B30;
            text-align: center;
        }
        .budget-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .budget-table th {
            background-color: #9B1B30;
            color: white;
            padding: 4px;
            text-align: left;
            font-size: 8pt;
            border: 1px solid #cbd5e1;
        }
        .budget-table td {
            padding: 3px;
            border: 1px solid #cbd5e1;
            font-size: 7.5pt;
        }
        .budget-table tr:nth-child(even) {
            background-color: #FAFAFA;
        }
        .total-row {
            background-color: #F5F5F5 !important;
            font-weight: bold;
        }
        .version-box {
            background-color: #fff;
            border: 1px solid #cbd5e1;
            padding: 5px;
            margin: 3px 0;
            font-size: 8pt;
        }
        .ejes-list {
            margin-left: 15px;
            line-height: 1.5;
            font-size: 8pt;
        }
        .ejes-list li {
            margin-bottom: 3px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #cbd5e1;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <div class="header">
        @if(isset($logoPath) && $logoPath && file_exists($logoPath))
        <div class="header-logo">
            <img src="{{ $logoPath }}" alt="Logo">
        </div>
        @endif
        <h1>ESTRATEGIA ANUAL DE COMUNICACIÓN SOCIAL<br>PARA EL EJERCICIO {{ $estrategy->anio }}</h1>
        <p style="margin: 8px 0; font-size: 9pt;"><strong>Año:</strong> {{ $estrategy->anio }}</p>
        <p style="margin: 5px 0; font-size: 9pt;"><strong>Sector:</strong> {{ $estrategy->institution->sector->name ?? 'N/A' }}</p>
        <h2>{{ $estrategy->institution_name }}</h2>
        <p style="margin: 5px 0; font-size: 8pt;"><strong>Naturaleza Jurídica:</strong> {{ $estrategy->juridical_nature_name }}</p>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="section">
        <div class="section-title">INFORMACIÓN GENERAL</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Año</div>
                <div class="info-value">{{ $estrategy->anio }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Institución</div>
                <div class="info-value">{{ $estrategy->institution_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Naturaleza Jurídica</div>
                <div class="info-value">{{ $estrategy->juridical_nature_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Responsable</div>
                <div class="info-value">{{ $estrategy->responsable_name ?? 'No asignado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Elaboración</div>
                <div class="info-value">{{ $estrategy->fecha_elaboracion ? $estrategy->fecha_elaboracion->format('d/m/Y') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado</div>
                <div class="info-value">
                    @php
                        $badgeClass = match($estrategy->estado_estrategia) {
                            'Autorizada' => 'badge-success',
                            'Enviada a DGNC', 'Enviado a CS', 'Aceptada CS' => 'badge-info',
                            'Observada DGNC' => 'badge-warning',
                            'Rechazada DGNC', 'Rechazada CS' => 'badge-danger',
                            default => 'badge-info'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $estrategy->estado_estrategia }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Concepto</div>
                <div class="info-value">
                    <span class="badge badge-info">{{ $estrategy->concepto }}</span>
                </div>
            </div>
            @if($estrategy->oficio_dgnc)
            <div class="info-row">
                <div class="info-label">Oficio DGNC</div>
                <div class="info-value">{{ $estrategy->oficio_dgnc }}</div>
            </div>
            @endif
            @if($estrategy->fecha_envio_dgnc)
            <div class="info-row">
                <div class="info-label">Fecha Envío DGNC</div>
                <div class="info-value">{{ $estrategy->fecha_envio_dgnc->format('d/m/Y') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Presupuesto Total Anual</div>
                <div class="info-value" style="font-weight: bold; color: #16a34a;">
                    ${{ number_format($estrategy->presupuesto, 2) }}
                </div>
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN INSTITUCIONAL -->
    <div class="section">
        <div class="section-title">INFORMACIÓN INSTITUCIONAL</div>

        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Misión:</strong>
            <p style="margin: 3px 0; text-align: justify; font-size: 8pt;">{{ $estrategy->mision }}</p>
        </div>

        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Visión:</strong>
            <p style="margin: 3px 0; text-align: justify; font-size: 8pt;">{{ $estrategy->vision }}</p>
        </div>

        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Objetivo Institucional:</strong>
            <p style="margin: 3px 0; text-align: justify; font-size: 8pt;">{{ $estrategy->objetivo_institucional }}</p>
        </div>

        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Objetivo de la Estrategia:</strong>
            <p style="margin: 3px 0; text-align: justify; font-size: 8pt;">{{ $estrategy->objetivo_estrategia }}</p>
        </div>
    </div>

    <!-- EJES DEL PLAN NACIONAL DE DESARROLLO -->
    @if($estrategy->ejes_plan_nacional && count($estrategy->ejes_plan_nacional) > 0)
    <div class="section">
        <div class="section-title">EJES DEL PLAN NACIONAL DE DESARROLLO</div>

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

        @if(count($selectedGenerales) > 0)
        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Ejes Generales:</strong>
            <ul class="ejes-list">
                @foreach($selectedGenerales as $eje)
                <li>{{ $eje }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($selectedTransversales) > 0)
        <div>
            <strong style="color: #9B1B30; font-size: 8.5pt;">Ejes Transversales:</strong>
            <ul class="ejes-list">
                @foreach($selectedTransversales as $eje)
                <li>{{ $eje }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    <!-- RESUMEN PRESUPUESTAL GLOBAL -->
    @if($estrategy->campaigns && $estrategy->campaigns->count() > 0)
    @php
        $totalGeneral = 0;
        foreach($estrategy->campaigns as $campaign) {
            $totalGeneral += ($campaign->televisoras ?? 0) + ($campaign->radiodifusoras ?? 0) +
                           ($campaign->mediosDigitales ?? 0) + ($campaign->mediosDigitalesInternet ?? 0) +
                           ($campaign->decdmx ?? 0) + ($campaign->deedos ?? 0) + ($campaign->deextr ?? 0) +
                           ($campaign->revistas ?? 0) + ($campaign->cine ?? 0) +
                           ($campaign->mediosComplementarios ?? 0) + ($campaign->preEstudios ?? 0) +
                           ($campaign->postEstudios ?? 0) + ($campaign->disenio ?? 0) +
                           ($campaign->produccion ?? 0) + ($campaign->preProduccion ?? 0) +
                           ($campaign->postProduccion ?? 0) + ($campaign->copiado ?? 0);
        }
        $porcentajeUtilizado = $estrategy->presupuesto > 0 ? ($totalGeneral / $estrategy->presupuesto) * 100 : 0;
        $presupuestoDisponible = $estrategy->presupuesto - $totalGeneral;
    @endphp

    <div class="section">
        <div class="section-title">RESUMEN PRESUPUESTAL GLOBAL</div>
        <table class="budget-table">
            <tr>
                <td style="font-weight: bold; background-color: #F5F5F5;">Presupuesto Total Anual</td>
                <td style="text-align: right; font-weight: bold;">${{ number_format($estrategy->presupuesto, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #F5F5F5;">Total Asignado a Campañas</td>
                <td style="text-align: right; font-weight: bold;">${{ number_format($totalGeneral, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #F5F5F5;">Porcentaje Utilizado</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($porcentajeUtilizado, 2) }}%</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #F5F5F5;">Presupuesto Disponible</td>
                <td style="text-align: right; font-weight: bold; color: {{ $presupuestoDisponible < 0 ? '#ef4444' : '#16a34a' }};">
                    ${{ number_format($presupuestoDisponible, 2) }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- CAMPAÑAS -->
    @if($estrategy->campaigns && $estrategy->campaigns->count() > 0)
    <div class="page-break"></div>

    @foreach($estrategy->campaigns as $campaign)
    <div class="campaign campaign-page">
        <!-- Encabezado de página de campaña -->
        <div class="page-header">
            @if(isset($logoPath) && $logoPath && file_exists($logoPath))
            <div class="page-header-logo">
                <img src="{{ $logoPath }}" alt="Logo">
            </div>
            @endif
            <h3>PROGRAMA ANUAL DE COMUNICACIÓN SOCIAL<br>PARA EL EJERCICIO FISCAL {{ $estrategy->anio }}</h3>
        </div>

        <div class="campaign-title">CAMPAÑA {{ $loop->iteration }}</div>
        <p style="font-weight: bold; font-size: 10pt; margin: 5px 0; color: #333;">{{ $campaign->name }}</p>

        <!-- Información de la Campaña -->
        <div class="info-grid" style="margin-bottom: 10px;">
            <div class="info-row">
                <div class="info-label">Tipo de Campaña</div>
                <div class="info-value">{{ $campaign->campaignType->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tema Específico</div>
                <div class="info-value">{{ $campaign->temaEspecifco }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Objetivo de Comunicación</div>
                <div class="info-value">{{ $campaign->objetivoComuicacion }}</div>
            </div>
        </div>

        <!-- Versiones -->
        @if($campaign->versions && $campaign->versions->count() > 0)
        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Versiones ({{ $campaign->versions->count() }}):</strong>
            @foreach($campaign->versions as $version)
            <div class="version-box">
                <strong>{{ $version->name }}</strong> -
                Del {{ \Carbon\Carbon::parse($version->fechaInicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($version->fechaFinal)->format('d/m/Y') }}
                ({{ \Carbon\Carbon::parse($version->fechaInicio)->diffInDays(\Carbon\Carbon::parse($version->fechaFinal)) }} días)
            </div>
            @endforeach
        </div>
        @endif

        <!-- Público Objetivo -->
        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Público Objetivo:</strong>
            <div style="margin-left: 10px; margin-top: 3px; font-size: 8pt;">
                @if($campaign->sexo)
                <p style="margin: 2px 0;"><strong>Sexo:</strong> {{ is_array($campaign->sexo) ? implode(', ', $campaign->sexo) : $campaign->sexo }}</p>
                @endif
                @if($campaign->edad)
                <p style="margin: 2px 0;"><strong>Edad:</strong> {{ is_array($campaign->edad) ? implode(', ', $campaign->edad) : $campaign->edad }}</p>
                @endif
                @if($campaign->poblacion)
                <p style="margin: 2px 0;"><strong>Población:</strong> {{ is_array($campaign->poblacion) ? implode(', ', $campaign->poblacion) : $campaign->poblacion }}</p>
                @endif
                @if($campaign->nse)
                <p style="margin: 2px 0;"><strong>NSE:</strong> {{ is_array($campaign->nse) ? implode(', ', $campaign->nse) : $campaign->nse }}</p>
                @endif
                @if($campaign->caracEspecific)
                <p style="margin: 2px 0;"><strong>Características Específicas:</strong> {{ $campaign->caracEspecific }}</p>
                @endif
            </div>
        </div>

        <!-- Medios Utilizados -->
        <div style="margin-bottom: 10px;">
            <strong style="color: #9B1B30; font-size: 8.5pt;">Medios Utilizados:</strong>
            <div style="margin-left: 10px; margin-top: 3px;">
                @if($campaign->tv_oficial) <span class="badge badge-info">TV Oficial</span> @endif
                @if($campaign->radio_oficial) <span class="badge badge-info">Radio Oficial</span> @endif
                @if($campaign->tv_comercial) <span class="badge badge-success">TV Comercial</span> @endif
                @if($campaign->radio_comercial) <span class="badge badge-success">Radio Comercial</span> @endif
            </div>
        </div>

        <!-- Distribución Presupuestal -->
        <div>
            <strong style="color: #9B1B30; font-size: 8.5pt;">Distribución Presupuestal:</strong>
            <table class="budget-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th style="text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $budgetItems = [
                            ['label' => 'Televisoras', 'value' => $campaign->televisoras],
                            ['label' => 'Radiodifusoras', 'value' => $campaign->radiodifusoras],
                            ['label' => 'Radios Comunitarias', 'value' => $campaign->mediosDigitales],
                            ['label' => 'Diarios CDMX', 'value' => $campaign->decdmx],
                            ['label' => 'Diarios Estados', 'value' => $campaign->deedos],
                            ['label' => 'Medios Internacionales', 'value' => $campaign->deextr],
                            ['label' => 'Revistas', 'value' => $campaign->revistas],
                            ['label' => 'Cine', 'value' => $campaign->cine],
                            ['label' => 'Medios Complementarios', 'value' => $campaign->mediosComplementarios],
                            ['label' => 'Medios Digitales', 'value' => $campaign->mediosDigitalesInternet],
                            ['label' => 'Pre-Estudios', 'value' => $campaign->preEstudios],
                            ['label' => 'Post-Estudios', 'value' => $campaign->postEstudios],
                            ['label' => 'Diseño', 'value' => $campaign->disenio],
                            ['label' => 'Producción', 'value' => $campaign->produccion],
                            ['label' => 'Pre-Producción', 'value' => $campaign->preProduccion],
                            ['label' => 'Post-Producción', 'value' => $campaign->postProduccion],
                            ['label' => 'Copiado', 'value' => $campaign->copiado],
                        ];

                        $total = 0;
                    @endphp

                    @foreach($budgetItems as $item)
                        @if($item['value'] > 0)
                        @php $total += $item['value']; @endphp
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td style="text-align: right;">${{ number_format($item['value'], 2) }}</td>
                        </tr>
                        @endif
                    @endforeach

                    <tr class="total-row">
                        <td><strong>TOTAL CAMPAÑA</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($total, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @else
    <div class="section">
        <div class="section-title">CAMPAÑAS DE COMUNICACIÓN</div>
        <p style="text-align: center; color: #64748b; padding: 15px; font-size: 8pt;">No hay campañas registradas para esta estrategia.</p>
    </div>
    @endif

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>NSINC - Sistema de Estrategias de Comunicación</p>
    </div>
</body>
</html>
