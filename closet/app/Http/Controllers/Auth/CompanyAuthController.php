<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\Company;

class CompanyAuthController extends Controller
{
    /**
     * Mostra o formulário de login
     */
    public function showLoginForm()
    {
        return view('company.login');
    }

    /**
     * Faz login da empresa
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('company')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('company.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem.',
        ])->onlyInput('email');
    }

    /**
     * Mostra o formulário de cadastro
     */
    public function showRegisterForm()
    {
        return view('company.register');
    }

    /**
     * Registra nova empresa
     */
    public function register(Request $request)
    {
        $request->merge([
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
        ]);

        $request->validate([
            'name'     => 'required|string|max:255',
            'cnpj'     => 'required|string|size:14|unique:companies',
            'email'    => 'required|email|unique:companies',
            'password' => 'required|confirmed|min:6',
        ]);

        $company = Company::create([
            'name'     => $request->name,
            'cnpj'     => $request->cnpj,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('company')->login($company);

        return redirect()->route('company.dashboard');
    }

    /**
     * Mostra formulário "Esqueci minha senha"
     */
    public function showForgotPasswordForm()
    {
        return view('company.forgot-password');
    }

    /**
     * Envia link de recuperação de senha
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('companies')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Mostra formulário de redefinição de senha
     */
    public function showResetPasswordForm($token)
    {
        return view('company.reset-password', ['token' => $token]);
    }

    /**
     * Reseta a senha da empresa
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::broker('companies')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($company, $password) {
                $company->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                Auth::guard('company')->login($company);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('company.dashboard')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Mostra o dashboard da empresa
     */
    public function dashboard()
    {
        $company = Auth::guard('company')->user();

        $itemsCount = $company->items()->count();
        $promotionsCount = $company->promotions()->count();
        $salesCount = 0; // placeholder até ter tabela de vendas

        return view('company.dashboard', compact('itemsCount', 'promotionsCount', 'salesCount'));
    }

    /**
     * Faz logout da empresa
     */
    public function logout(Request $request)
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('company.login');
    }

    /**
     * Mostra o formulário de edição do perfil da empresa
     */
    public function profile()
    {
        $company = Auth::guard('company')->user();
        return view('company.profile', compact('company'));
    }

    /**
     * Atualiza os dados do perfil da empresa
     */
    public function updateProfile(Request $request)
    {
        $company = Auth::guard('company')->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'cnpj'  => 'required|string|size:14|unique:companies,cnpj,' . $company->id,
        ]);

        $company->update([
            'name'  => $request->name,
            'email' => $request->email,
            'cnpj'  => preg_replace('/\D/', '', $request->cnpj),
        ]);

        return redirect()->route('company.profile')->with('success', 'Perfil atualizado com sucesso!');
    }
}
