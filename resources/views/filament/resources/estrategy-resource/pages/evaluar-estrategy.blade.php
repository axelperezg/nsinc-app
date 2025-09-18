<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Información General -->
        <x-filament::section>
            <x-slot name="heading">
                Información General
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Año</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->anio }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Institución</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->institution->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->estado_estrategia }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsable</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->responsable->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Elaboración</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->fecha_elaboracion ? \Carbon\Carbon::parse($estrategy->fecha_elaboracion)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Presupuesto</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($estrategy->presupuesto, 2) }}</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Información Institucional -->
        <x-filament::section>
            <x-slot name="heading">
                Información Institucional
            </x-slot>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Misión</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->mision }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visión</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->vision }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objetivo Institucional</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->objetivo_institucional }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objetivo de la Estrategia</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $estrategy->objetivo_estrategia }}</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Campañas -->
        @if($estrategy->campaigns->count() > 0)
        <x-filament::section>
            <x-slot name="heading">
                Campañas ({{ $estrategy->campaigns->count() }})
            </x-slot>

            <div class="space-y-4">
                @foreach($estrategy->campaigns as $campaign)
                <x-filament::card>
                    <div class="p-4">
                        <h4 class="text-lg font-medium mb-3 text-gray-900 dark:text-white">{{ $campaign->name }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objetivo</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->objetivo }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Público Objetivo</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->publico_objetivo }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Presupuesto Total</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($campaign->televisoras + $campaign->radiodifusoras + $campaign->cine + $campaign->decdmx + $campaign->deedos + $campaign->deextr + $campaign->revistas + $campaign->mediosComplementarios + $campaign->mediosDigitales + $campaign->preEstudios + $campaign->postEstudios + $campaign->disenio + $campaign->produccion + $campaign->preProduccion + $campaign->postProduccion + $campaign->copiado, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Versiones</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->versions->count() }}</p>
                            </div>
                        </div>
                    </div>
                </x-filament::card>
                @endforeach
            </div>
        </x-filament::section>
        @endif

        <!-- Acciones de Evaluación -->
        <x-filament::section>
            <x-slot name="heading">
                Evaluación de la Estrategia
            </x-slot>

            <div class="flex space-x-4">
                <form action="{{ route('estrategy.evaluar', $estrategy->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="estado" value="Aceptada CS">
                    <x-filament::button type="submit" color="success" size="lg">
                        <x-filament::icon name="heroicon-o-check-circle" class="w-4 h-4 mr-2" />
                        Aceptar Estrategia
                    </x-filament::button>
                </form>

                <form action="{{ route('estrategy.evaluar', $estrategy->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="estado" value="Rechazada CS">
                    <x-filament::button type="submit" color="danger" size="lg">
                        <x-filament::icon name="heroicon-o-x-circle" class="w-4 h-4 mr-2" />
                        Rechazar Estrategia
                    </x-filament::button>
                </form>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
