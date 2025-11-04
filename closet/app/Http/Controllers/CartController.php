<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('item')->get();
        $total = $cartItems->sum('subtotal');

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request, Item $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'item_id' => $item->id,
            ],
            [
                'quantity' => $validated['quantity'],
                'price_at_addition' => $item->purchase_price,
            ]
        );

        return redirect()->back()->with('success', 'Item adicionado ao carrinho com sucesso!');
    }

    /**
     * Update the quantity of an item in the cart.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        // Verify the cart item belongs to the authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cartItem->update(['quantity' => $validated['quantity']]);

        return redirect()->back()->with('success', 'Quantidade atualizada com sucesso!');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem)
    {
        // Verify the cart item belongs to the authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removido do carrinho com sucesso!');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->back()->with('success', 'Carrinho limpo com sucesso!');
    }

    /**
     * Get cart count (for AJAX requests).
     */
    public function count()
    {
        $count = CartItem::where('user_id', Auth::id())->count();

        return response()->json(['count' => $count]);
    }
}

