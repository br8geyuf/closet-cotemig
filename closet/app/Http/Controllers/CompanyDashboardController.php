<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Promotion;
use App\Models\Store;

class CompanyDashboardController extends Controller
{
    /**
     * Exibe o dashboard da empresa logada
     */
    public function index()
    {
        $company = Auth::guard('company')->user();

        // Contadores básicos
        $itemsCount = Item::where('store_id', $company->id)->count();
        $promotionsCount = Promotion::whereHas('store', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->count();

        // Exemplo: vendas (se tiver tabela "sales" ou equivalente)
        $salesCount = 0; // substituir quando implementar vendas

        return view('company.dashboard', compact(
            'itemsCount',
            'promotionsCount',
            'salesCount'
        ));
    }
}
