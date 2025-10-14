<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RoleMiddleware - Middleware phân quyền theo role
 * 
 * Usage trong routes:
 * Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('role:admin');
 * Route::get('/investigator/dashboard', [InvestigatorController::class, 'dashboard'])->middleware('role:investigator');
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Kiểm tra role
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Không có quyền truy cập
        abort(403, 'Unauthorized access. You do not have permission to access this resource.');
    }
}

