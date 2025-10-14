<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * AuthController - Xử lý đăng nhập, đăng xuất
 */
class AuthController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Hiển thị form login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Ghi log login
            $this->logService->logLogin($user);

            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Ghi log logout
        if ($user) {
            $this->logService->logLogout($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect dựa theo role
     */
    private function redirectByRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isInvestigator()) {
            return redirect()->route('investigator.dashboard');
        }

        return redirect('/');
    }
}

