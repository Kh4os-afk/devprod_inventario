<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Flux;

class Login extends Component
{
    public $usuariobd;
    public $senhabd;

    public function login()
    {
        $validated = $this->validate([
            'usuariobd' => 'required',
            'senhabd' => 'required',
        ]);

        $validated = array_map('strtoupper', $validated);

        $usuario = User::where('usuariobd', $validated['usuariobd'])
            ->whereRaw('decrypt(senhabd,usuariobd) = ?', [$validated['senhabd']])
            ->first();

        if (!$usuario) {
            Flux::toast(
                heading: 'Erro',
                text: 'Usuário ou senha inválidos.',
                variant: 'danger',
            );
            return;
        }

        Auth::login($usuario);

        session()->regenerate();

        return redirect()->intended();
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();

        session()->regenerateToken();

        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth')
            ->title('Login');
    }
}
