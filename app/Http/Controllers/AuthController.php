<?php

namespace App\Http\Controllers;

use App\Support\MockUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.login', ['pageTitle' => 'Entrar na moderação']);
    }

    public function login(Request $request, MockUsers $users): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Autenticação fictícia: qualquer e-mail/senha não vazios entram.
        $user = $users->authenticate($data['email'], $data['password']);
        if (! $user) {
            return back()->withErrors(['global' => 'Informe e-mail e senha.'])->withInput();
        }

        $request->session()->put('user', $user);

        // Redireciona para ?redirect quando existir, senão para a moderação.
        $redirect = $request->input('redirect') ?: route('dashboard');

        return redirect($redirect);
    }

    public function showRegister(): View
    {
        return view('pages.register', ['pageTitle' => 'Criar conta']);
    }

    public function register(Request $request, MockUsers $users): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $request->session()->put('user', $users->register($data['name'], $data['email']));

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('user');

        return redirect()->route('login');
    }
}
