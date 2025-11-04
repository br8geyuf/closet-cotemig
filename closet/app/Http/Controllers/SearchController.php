<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Store;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query()->with(['store', 'category']);

        // 🔍 Texto digitado
        if ($search = $request->input('query')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', fn($cat) => $cat->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('store', fn($store) => $store->where('name', 'like', "%{$search}%"));
            });
        }

        // 🏷️ Filtro por categoria
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // 🏪 Filtro por loja
        if ($request->filled('store')) {
            $query->where('store_id', $request->input('store'));
        }

        // 💰 Faixa de preço
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // 🚚 Filtro por frete grátis (caso tenha um campo no modelo)
        if ($request->boolean('free_shipping')) {
            $query->where('free_shipping', true);
        }

        // 🔢 Paginação
        $items = $query->paginate(24)->withQueryString();

        // Listas para os filtros
        $categories = Category::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();

        return view('search.index', compact('items', 'categories', 'stores'))
            ->with('query', $request->input('query'));
    }
}
