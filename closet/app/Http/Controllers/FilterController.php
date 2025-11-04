<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FilterController extends Controller
{
    /**
     * Exibe a página de filtro de busca.
     */
    public function index()
    {
        // Aqui você pode enviar dados para a view (ex: categorias, lojas, etc)
        return view('filter.index');
    }

    /**
     * Processa o filtro (caso queira buscar no banco).
     */
    public function search(Request $request)
    {
        $query = $request->input('q'); // termo de busca
        // Exemplo: buscar em Items, Stores etc
        // $results = Item::where('name', 'like', "%{$query}%")->get();

        return response()->json([
            'success' => true,
            'query' => $query,
            // 'results' => $results,
        ]);
    }
}
