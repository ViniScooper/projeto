<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:filter',
            'password' => 'required'
        ]);

        Log::info('Tentativa de login', ['email' => $credentials['email'], 'ip' => $request->ip()]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            Log::info('Login bem-sucedido', ['user_id' => Auth::id()]);
            return redirect()->intended(route('cats'));
        }

        Log::warning('Falha no login', ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'Credenciais inválidas ou conta não existe',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Log::info('Logout realizado', ['user_id' => Auth::id()]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:filter|unique:usuarios',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        Auth::login($user);
        
        Log::info('Novo usuário registrado', ['user_id' => $user->id]);
        
        return redirect()->route('cats')->with('success', 'Registro concluído com sucesso!');
    }
}