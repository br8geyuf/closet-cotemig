<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    /**
     * Exibe o formulário de edição do perfil da empresa.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $company = Auth::guard('company')->user();
        return view('company.profile.edit', compact('company'));
    }

    /**
     * Atualiza o perfil da empresa no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $company = Auth::guard('company')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $company->name = $request->name;
        $company->description = $request->description;

        if ($request->hasFile('logo')) {
            // Deleta o logo antigo se existir
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $company->logo = $path;
        }

        $company->save();

        return redirect()->route('company.profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }
}

