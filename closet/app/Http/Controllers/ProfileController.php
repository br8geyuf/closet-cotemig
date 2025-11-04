<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048', // max 2MB
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Exclui avatar antigo, se existir
            if ($user->avatar_url) {
                Storage::delete($user->avatar_url);
            }

            // Salva a nova imagem
            $path = $request->file('avatar')->store('avatars');

            // Atualiza no banco
            $user->avatar_url = $path;
            $user->save();

            return redirect()->back()->with('success', 'Foto de perfil atualizada com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao atualizar a foto.');
    }
}
