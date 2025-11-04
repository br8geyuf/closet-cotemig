<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Item;
use Illuminate\Http\Request;

class MemoryController extends Controller
{
    /**
     * Lista todas as memórias
     */
    public function index()
    {
        $memories = Memory::all();
        return view('memories.index', compact('memories'));
    }

    /**
     * Mostra o formulário de criação
     */
    public function create()
    {
        $items = Item::all(); // pega todos os itens (ou só do usuário logado se preferir)
        return view('memories.create', compact('items'));
    }

    /**
     * Salva uma nova memória
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'content'     => 'required|string',
        'memory_date' => 'required|date',
        'item_id'     => 'required|exists:items,id',
        'location'    => 'nullable|string|max:255',
        'occasion'    => 'nullable|in:casual,trabalho,festa,viagem,especial,outro',
        'rating'      => 'nullable|integer|min:1|max:5',
        'photos.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'tags'        => 'nullable|string',
        'is_favorite' => 'nullable|boolean',
    ]);

    $validated['user_id'] = auth()->id();

    // 🔖 Tags
    $validated['tags'] = !empty($validated['tags'])
        ? array_map('trim', explode(',', $validated['tags']))
        : [];

    // ⭐ Favorito
    $validated['is_favorite'] = $request->has('is_favorite');

    // 📷 Upload de fotos
    $photos = [];
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('memories', 'public');
            $photos[] = $path;
        }
    }
    $validated['photos'] = $photos;

    Memory::create($validated);

    return redirect()
        ->route('memories.index')
        ->with('success', 'Memória criada com sucesso!');
}


    /**
     * Mostra uma memória específica
     */
    public function show($id)
    {
        $memory = Memory::findOrFail($id);
        return view('memories.show', compact('memory'));
    }

    /**
     * Mostra o formulário de edição
     */
    public function edit($id)
    {
        $memory = Memory::findOrFail($id);
        $items = Item::all(); // também precisa dos itens aqui
        return view('memories.edit', compact('memory', 'items'));
    }

    /**
     * Atualiza uma memória
     */
    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'content'     => 'required|string',
        'memory_date' => 'required|date',
        'item_id'     => 'required|exists:items,id',
        'location'    => 'nullable|string|max:255',
        'occasion'    => 'nullable|in:casual,trabalho,festa,viagem,especial,outro',
        'rating'      => 'nullable|integer|min:1|max:5',
        'photos.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'tags'        => 'nullable|string',
        'is_favorite' => 'nullable|boolean',
    ]);

    $memory = Memory::findOrFail($id);

    // 🔖 Tags
    $validated['tags'] = !empty($validated['tags'])
        ? array_map('trim', explode(',', $validated['tags']))
        : [];

    // ⭐ Favorito
    $validated['is_favorite'] = $request->has('is_favorite');

    // 📷 Fotos (mantém as antigas + adiciona novas)
    $photos = $memory->photos ?? [];
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('memories', 'public');
            $photos[] = $path;
        }
    }
    $validated['photos'] = $photos;

    $memory->update($validated);

    return redirect()
        ->route('memories.index')
        ->with('success', 'Memória atualizada com sucesso!');
}

}
