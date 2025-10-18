<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Fechas de Vencimiento - Año {{ $this->getViewData()['year'] }}
        </x-slot>

        <x-slot name="description">
            Estado actual de las fechas límite para registro, modificación y observaciones
        </x-slot>

        <div class="space-y-4">
            @php
                $data = $this->getViewData();
                $statuses = $data['statuses'];
                $hasConfiguration = $data['hasConfiguration'];
                $year = $data['year'];
            @endphp

            @if(!$hasConfiguration)
                {{-- Mensaje cuando no hay fechas configuradas --}}
                <div class="rounded-lg border border-blue-300 bg-blue-50 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-filament::icon
                                icon="heroicon-o-information-circle"
                                class="h-8 w-8 text-blue-600"
                            />
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-blue-900">
                                No hay fechas de vencimiento configuradas
                            </h3>

                            <p class="mt-2 text-sm text-blue-700">
                                No se encontraron fechas de vencimiento para el año <strong>{{ $year }}</strong>.
                            </p>

                            <p class="mt-2 text-sm text-blue-700">
                                Para configurar las fechas de vencimiento, contacte al administrador del sistema o acceda al módulo de <strong>Fechas de Vencimiento</strong> si tiene los permisos necesarios.
                            </p>

                            <div class="mt-4 rounded-md border border-blue-200 bg-blue-100 p-3">
                                <p class="text-xs font-medium text-blue-800">
                                    <strong>Nota importante:</strong> Mientras no existan fechas configuradas, se permitirán todas las acciones de creación y modificación de estrategias por defecto.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Mostrar fechas de vencimiento cuando están configuradas --}}
                @foreach($statuses as $concept => $status)
                <div class="rounded-lg border p-4 {{ match($status['level']) {
                    'success' => 'border-green-300 bg-green-50',
                    'warning' => 'border-yellow-300 bg-yellow-50',
                    'danger' => 'border-red-300 bg-red-50',
                    'info' => 'border-blue-300 bg-blue-50',
                    default => 'border-gray-300 bg-gray-50',
                } }}">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-filament::icon
                                :icon="match($status['level']) {
                                    'success' => 'heroicon-o-check-circle',
                                    'warning' => 'heroicon-o-exclamation-triangle',
                                    'danger' => 'heroicon-o-x-circle',
                                    'info' => 'heroicon-o-information-circle',
                                    default => 'heroicon-o-question-mark-circle',
                                }"
                                class="h-6 w-6 {{ match($status['level']) {
                                    'success' => 'text-green-600',
                                    'warning' => 'text-yellow-600',
                                    'danger' => 'text-red-600',
                                    'info' => 'text-blue-600',
                                    default => 'text-gray-600',
                                } }}"
                            />
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold {{ match($status['level']) {
                                'success' => 'text-green-900',
                                'warning' => 'text-yellow-900',
                                'danger' => 'text-red-900',
                                'info' => 'text-blue-900',
                                default => 'text-gray-900',
                            } }}">
                                {{ $concept }}
                            </h3>

                            <p class="mt-1 text-sm {{ match($status['level']) {
                                'success' => 'text-green-700',
                                'warning' => 'text-yellow-700',
                                'danger' => 'text-red-700',
                                'info' => 'text-blue-700',
                                default => 'text-gray-700',
                            } }}">
                                {{ $status['message'] }}
                            </p>

                            @if($status['expiration'])
                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs {{ match($status['level']) {
                                    'success' => 'text-green-600',
                                    'warning' => 'text-yellow-600',
                                    'danger' => 'text-red-600',
                                    'info' => 'text-blue-600',
                                    default => 'text-gray-600',
                                } }}">
                                    <div>
                                        <strong>Inicio:</strong> {{ $status['expiration']->fecha_inicio->format('d/m/Y') }}
                                    </div>
                                    <div>
                                        <strong>Límite:</strong> {{ $status['expiration']->fecha_limite->format('d/m/Y') }}
                                    </div>
                                    <div>
                                        <strong>Día Previo:</strong> {{ $status['expiration']->fecha_diaPrevio->format('d/m/Y') }}
                                    </div>
                                    <div>
                                        <strong>Restrictiva:</strong> {{ $status['expiration']->fecha_restrictiva->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ match($status['level']) {
                                'success' => 'bg-green-100 text-green-800',
                                'warning' => 'bg-yellow-100 text-yellow-800',
                                'danger' => 'bg-red-100 text-red-800',
                                'info' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-800',
                            } }}">
                                @if($status['allowed'])
                                    Permitido
                                @else
                                    Bloqueado
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-4 text-xs text-gray-500">
                    <p><strong>Nota:</strong> Las fechas de vencimiento controlan cuándo se pueden crear o modificar estrategias.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li><strong>Registro:</strong> Aplica para crear nuevas estrategias</li>
                        <li><strong>Modificación:</strong> Aplica para modificar o cancelar estrategias autorizadas</li>
                        <li><strong>Observación:</strong> Aplica para solventar estrategias observadas por DGNC</li>
                    </ul>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
