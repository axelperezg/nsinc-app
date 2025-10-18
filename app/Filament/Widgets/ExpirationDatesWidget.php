<?php

namespace App\Filament\Widgets;

use App\Helpers\ExpirationDateHelper;
use App\Models\ExpirationDate;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ExpirationDatesWidget extends Widget
{
    protected static string $view = 'filament.widgets.expiration-dates-widget';

    protected int | string | array $columnSpan = 'full';

    // Propiedad pública que recibe el año desde la página
    public ?int $filterYear = null;

    /**
     * Escuchar el evento cuando se actualizan los filtros
     */
    #[On('filtersUpdated')]
    public function updateYear(int $year): void
    {
        $this->filterYear = $year;
    }

    /**
     * Obtener los datos para el widget
     */
    public function getViewData(): array
    {
        $user = Auth::user();

        // Usar el año que se pasó desde la página, o el año actual
        $year = $this->filterYear ?? now()->year;

        // Obtener todas las fechas de vencimiento del año
        $expirationDates = ExpirationDate::getForYear($year);

        // Verificar si hay fechas configuradas
        $hasConfiguration = $expirationDates->isNotEmpty();

        // Validar el estado de cada concepto solo si hay configuración
        $statuses = $hasConfiguration
            ? ExpirationDateHelper::getAllExpirationStatuses($year)
            : [];

        return [
            'year' => $year,
            'expirationDates' => $expirationDates,
            'statuses' => $statuses,
            'user' => $user,
            'hasConfiguration' => $hasConfiguration,
        ];
    }

    /**
     * Obtener el color para cada estado
     */
    public static function getStatusColor(string $status): string
    {
        return match($status) {
            'active' => 'success',
            'warning' => 'warning',
            'expired' => 'danger',
            'restricted' => 'danger',
            'not_started' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Obtener el icono para cada estado
     */
    public static function getStatusIcon(string $status): string
    {
        return match($status) {
            'active' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'expired' => 'heroicon-o-x-circle',
            'restricted' => 'heroicon-o-no-symbol',
            'not_started' => 'heroicon-o-clock',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    /**
     * Determinar si el widget debe mostrarse
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        // Verificar primero si el usuario es de institución
        if (!$user || !$user->role || !in_array($user->role->name, [
            'institution_user',
            'institution_admin',
        ])) {
            return false;
        }

        // Verificar si el widget está activado en configuración
        return \App\Models\Configuration::get('widget.expiration_dates.enabled', true);
    }
}
