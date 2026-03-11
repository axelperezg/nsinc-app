<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'todos' => Tab::make('Todos')
                ->badge(fn () => \App\Models\User::count()),
        ];

        $roles = Role::orderBy('display_name')->get();

        foreach ($roles as $role) {
            $tabs[$role->name] = Tab::make($role->display_name)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', $role->id))
                ->badge(fn () => \App\Models\User::where('role_id', $role->id)->count());
        }

        return $tabs;
    }
}
