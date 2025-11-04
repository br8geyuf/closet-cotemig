<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WardrobeController extends Controller
{
    /**
     * Aplica middleware para autenticação de ambos
     */
    public function __construct()
    {
        // Usa o middleware 'auth' com ambos guards
        $this->middleware('auth:web,company');
    }

    /**
     * Redireciona para a dashboard correspondente
     */
    public function index()
    {
        if (Auth::guard('web')->check()) {
            // Usuário normal (cliente)
            return redirect()->route('dashboard'); // ou route específica do cliente
        }

        if (Auth::guard('company')->check()) {
            // Empresa
            return redirect()->route('company.dashboard');
        }

        // Caso não esteja logado
        return redirect()->route('login');
    }
}
