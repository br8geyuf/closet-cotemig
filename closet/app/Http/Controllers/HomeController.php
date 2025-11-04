<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('items')->get(); // pega categorias com número de itens
        $items = Item::latest()->take(8)->get(); // pega últimos 8 itens cadastrados

        return view('home', compact('categories', 'items'));
    }
}
