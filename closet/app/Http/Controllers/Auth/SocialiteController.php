<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Lida com o callback de autenticação do Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                $user = User::where('email', $googleUser->email)->first();

                if ($user) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                } else {
                    $user = User::create([
                        "name" => $googleUser->name,
                        "email" => $googleUser->email,
                        "google_id" => $googleUser->id,
                        "password" => Hash::make(Str::random(24)), // Gera uma senha aleatória
                    ]);
                }
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            // Log do erro para depuração
            // Log::error('Erro ao autenticar com o Google: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Não foi possível autenticar com o Google. Tente novamente.');
        }
    }
}

