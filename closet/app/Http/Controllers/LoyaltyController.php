<?php

namespace App\Http\Controllers;

use App\Models\UserPoints;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    /**
     * Display the loyalty program dashboard.
     */
    public function index()
    {
        $userPoints = Auth::user()->points ?? UserPoints::create(['user_id' => Auth::id()]);
        $transactions = PointTransaction::where('user_id', Auth::id())
            ->recent()
            ->limit(20)
            ->get();

        return view('loyalty.index', compact('userPoints', 'transactions'));
    }

    /**
     * Get the current points balance (for AJAX requests).
     */
    public function getBalance()
    {
        $userPoints = Auth::user()->points ?? UserPoints::create(['user_id' => Auth::id()]);

        return response()->json([
            'balance' => $userPoints->balance,
        ]);
    }

    /**
     * Get transaction history.
     */
    public function getTransactions($limit = 50)
    {
        $transactions = PointTransaction::where('user_id', Auth::id())
            ->recent()
            ->limit($limit)
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    /**
     * Redeem points for a discount.
     */
    public function redeemDiscount(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $userPoints = Auth::user()->points ?? UserPoints::create(['user_id' => Auth::id()]);

        if ($userPoints->redeemPoints($validated['points'], 'Desconto resgatado')) {
            return redirect()->back()->with('success', 'Pontos resgatados com sucesso!');
        }

        return redirect()->back()->with('error', 'Saldo de pontos insuficiente.');
    }

    /**
     * Display available rewards.
     */
    public function rewards()
    {
        $userPoints = Auth::user()->points ?? UserPoints::create(['user_id' => Auth::id()]);

        // Define available rewards
        $rewards = [
            ['points' => 100, 'discount' => 10, 'description' => 'R$ 10 de desconto'],
            ['points' => 250, 'discount' => 25, 'description' => 'R$ 25 de desconto'],
            ['points' => 500, 'discount' => 60, 'description' => 'R$ 60 de desconto'],
            ['points' => 1000, 'discount' => 150, 'description' => 'R$ 150 de desconto'],
        ];

        return view('loyalty.rewards', compact('userPoints', 'rewards'));
    }
}

