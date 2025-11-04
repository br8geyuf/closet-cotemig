<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class MarketplaceApiController extends Controller
{
    /**
     * Lista todos os itens disponíveis no marketplace.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {        $items = Item::paginate(10);
        return response()->json($items);
    }

    /**
     * Exibe os detalhes de um item específico no marketplace.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Item $item)
    {
        $item->load('user', 'category', 'company', 'store');
        return response()->json($item);
    }
}


