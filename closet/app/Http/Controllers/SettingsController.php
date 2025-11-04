<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * 🛎️ Página de notificações — exibe notificações do usuário logado (cliente ou empresa).
     */
    public function notifications(Request $request)
    {
        // Verifica se há usuário autenticado em qualquer guard
        $user = auth()->guard('web')->user() ?? auth()->guard('company')->user();

        // Se ninguém estiver logado, redireciona com aviso
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Você precisa estar logado para ver suas notificações.');
        }

        // Busca notificações mais recentes com paginação (10 por página)
        $notifications = method_exists($user, 'notifications')
            ? $user->notifications()->latest()->paginate(10)
            : collect();

        return view('settings.notifications', compact('notifications'));
    }

    /**
     * ⚡ Página de atividades
     */
    public function activities()
    {
        return view('settings.activities');
    }

    /**
     * 🔒 Página de privacidade
     */
    public function privacy()
    {
        return view('settings.privacy');
    }

    /**
     * 🚫 Página de bloqueados
     */
    public function blocked()
    {
        return view('settings.blocked');
    }

    /**
     * 🧩 Página de permissões
     */
    public function permissions()
    {
        return view('settings.permissions');
    }

    /**
     * ♿ Página de acessibilidade
     */
    public function accessibility()
    {
        return view('settings.accessibility');
    }
}
