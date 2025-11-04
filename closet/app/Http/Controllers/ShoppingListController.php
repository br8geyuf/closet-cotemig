<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function index()
    {
        // retorna a view da lista de compras
        return view('shopping-list.index');
    }
}
