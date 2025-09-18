<div class="flex items-center space-x-2">
    <span class="{{ $color }} font-semibold text-lg">
        {{ $icono }}{{ number_format($porcentaje, 2) }}%
    </span>
    @if($porcentaje > 100)
        <span class="text-red-500 text-sm">(Excedido)</span>
    @elseif($porcentaje > 80)
        <span class="text-orange-500 text-sm">(Alto)</span>
    @elseif($porcentaje > 0)
        <span class="text-green-500 text-sm">(Óptimo)</span>
    @else
        <span class="text-gray-500 text-sm">(Sin asignar)</span>
    @endif
</div>
