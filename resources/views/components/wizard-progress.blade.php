<div class="wizard-progress-wrapper">
    <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Progreso de tu Estrategia
            </h3>
            <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                {{ $currentStep }} de {{ $totalSteps }} pasos completados
            </span>
        </div>
        
        <!-- Barra de progreso -->
        <div class="relative w-full h-4 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
            <div 
                class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500 ease-out rounded-full"
                style="width: {{ $percentage }}%"
            >
                <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
            </div>
        </div>
        
        <!-- Porcentaje -->
        <div class="mt-2 text-right">
            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ round($percentage) }}%
            </span>
        </div>
        
        <!-- Mensaje motivacional -->
        @if($percentage < 100)
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300 italic">
                💪 ¡Vas muy bien! Continúa completando los siguientes pasos.
            </p>
        @else
            <p class="mt-3 text-sm text-green-600 dark:text-green-400 italic font-semibold">
                🎉 ¡Excelente! Has completado todos los pasos. Ya puedes enviar tu estrategia.
            </p>
        @endif
    </div>
</div>


