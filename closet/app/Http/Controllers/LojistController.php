<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lojist;

class LojistController extends Controller
{
    /**
     * Lista todos os lojistas.
     */
    public function index()
    {
        $lojists = Lojist::all();
        return view('lojists.index', compact('lojists'));
    }

    /**
     * Mostra formulário de criação de lojista.
     */
    public function create()
    {
        return view('lojists.create');
    }

    /**
     * Salva um novo lojista no banco.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        Lojist::create($validated);

        return redirect()->route('lojists.index')
                         ->with('success', 'Lojista cadastrado com sucesso!');
    }

    /**
     * Mostra um lojista específico.
     */
    public function show($id)
    {
        $lojist = Lojist::findOrFail($id);
        return view('lojists.show', compact('lojist'));
    }

    /**
     * Mostra formulário de edição.
     */
    public function edit($id)
    {
        $lojist = Lojist::findOrFail($id);
        return view('lojists.edit', compact('lojist'));
    }

    /**
     * Atualiza um lojista.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $lojist = Lojist::findOrFail($id);
        $lojist->update($validated);

        return redirect()->route('lojists.index')
                         ->with('success', 'Lojista atualizado com sucesso!');
    }

    /**
     * Remove um lojista.
     */
    public function destroy($id)
    {
        $lojist = Lojist::findOrFail($id);
        $lojist->delete();

        return redirect()->route('lojists.index')
                         ->with('success', 'Lojista removido com sucesso!');
    }
}
