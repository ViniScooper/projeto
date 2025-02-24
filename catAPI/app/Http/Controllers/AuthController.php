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
            'cpf' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        Log::info('Tentativa de login', ['cpf' => $credentials['cpf'], 'ip' => $request->ip()]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Log::info('Login bem-sucedido', ['user_id' => Auth::id()]);
            return redirect()->intended('/cats');
        }

        Log::warning('Falha no login', ['cpf' => $credentials['cpf']]);
        return back()->withErrors([
            'cpf' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Log::info('Logout realizado', ['user_id' => Auth::id()]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:usuarios',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'phone' => 'required|string|max:15',
            'sexo' => 'required|string|in:masculino,feminino',
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
            'cpf' => $request->cpf,
            'email' => $request->email,
            'phone' => $request->phone,
            'sexo' => $request->sexo,
            'password' => Hash::make($request->password)
        ]);

        Auth::login($user);
        
        Log::info('Novo usuário registrado', ['user_id' => $user->id]);
        
        return redirect()->route('login');
    }
}