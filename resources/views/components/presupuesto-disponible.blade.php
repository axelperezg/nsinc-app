<div class="flex items-center space-x-2">
    <span class="{{ $color }} font-semibold text-lg">
        {{ $icono }}${{ number_format($monto, 2) }}
    </span>
    @if($monto < 0)
        <span class="text-red-500 text-sm">(Excedido)</span>
    @endif
</div>
