<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Cancelar Estrategia - {{ $this->estrategyOriginal->anio }}
        </x-slot>

        <x-slot name="description">
            Se está creando una cancelación de la estrategia autorizada. Se duplicará con todos sus datos y campañas.
        </x-slot>

        <div class="flex items-center justify-center p-8">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
                <p class="text-lg font-medium text-gray-900">Procesando cancelación...</p>
                <p class="text-sm text-gray-500 mt-2">Por favor espera mientras se crea la nueva estrategia.</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
