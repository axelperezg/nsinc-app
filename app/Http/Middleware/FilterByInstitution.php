<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterByInstitution
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role && $user->role->name === 'super_admin') {
                // Super admin puede ver todo
                session(['user_can_view_all' => true]);
                session(['user_institution_id' => null]);
            } else {
                // Usuario normal solo ve su institución
                session(['user_can_view_all' => false]);
                session(['user_institution_id' => $user->institution_id]);
            }
        }
        
        return $next($request);
    }
}
