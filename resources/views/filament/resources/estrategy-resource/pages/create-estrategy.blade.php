<x-filament-panels::page>
    <div x-data="{
        lastSaved: null,
        autoSave() {
            $wire.saveDraft()
            this.lastSaved = new Date()
        }
    }"
    x-init="
        setInterval(() => autoSave(), 30000);
        @this.on('draft-saved', () => {
            lastSaved = new Date();
        });
    ">

        {{-- Banner informativo del Wizard --}}
        <div class="mb-6">
            <div class="rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur">
                            <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Nueva Estrategia de Comunicación</h3>
                            <p class="text-sm text-blue-100 mt-1">Completa los 6 pasos para crear tu estrategia anual</p>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center gap-2 text-white">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <span class="text-sm font-medium">Guía paso a paso</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Indicador de auto-guardado --}}
        <div class="mb-4" x-show="lastSaved">
            <div class="rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-3 border border-green-200 dark:border-green-700 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-400 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium text-green-800 dark:text-green-200">
                        💾 Guardado automáticamente a las
                        <span class="font-bold" x-text="lastSaved ? new Date(lastSaved).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : ''"></span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Consejos útiles --}}
        <div class="mb-6">
            <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 p-4 border border-amber-200 dark:border-amber-700">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-100 mb-1">💡 Consejos para completar tu estrategia:</h4>
                        <ul class="text-xs text-amber-800 dark:text-amber-200 space-y-1 list-disc list-inside">
                            <li>Navega entre pasos usando los botones "Siguiente" y "Anterior"</li>
                            <li>Puedes saltar pasos si necesitas revisar información específica</li>
                            <li>Tus datos se guardan automáticamente cada 30 segundos</li>
                            <li>Completa todos los campos requeridos en cada paso para continuar</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de Filament con Wizard --}}
        <x-filament-panels::form wire:submit="create">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </div>

    {{-- Estilos personalizados para el wizard --}}
    <style>
        /* Mejoras visuales para el wizard */
        .fi-fo-wizard {
            @apply rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800;
        }

        .fi-fo-wizard-step-label {
            @apply font-semibold;
        }

        /* Animación suave para transiciones entre pasos */
        .fi-fo-wizard-step {
            transition: all 0.3s ease-in-out;
        }

        /* Resaltar paso activo */
        .fi-fo-wizard-step[aria-current="step"] {
            @apply scale-105;
        }
    </style>
</x-filament-panels::page>
