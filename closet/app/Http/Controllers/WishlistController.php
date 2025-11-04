<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlists.
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with('items')->get();

        return view('wishlists.index', compact('wishlists'));
    }

    /**
     * Display a specific wishlist.
     */
    public function show(Wishlist $wishlist)
    {
        // Check if wishlist is public or belongs to authenticated user
        if (!$wishlist->is_public && $wishlist->user_id !== Auth::id()) {
            return redirect()->route('wishlists.index')->with('error', 'Acesso negado.');
        }

        $items = $wishlist->items()->get();
        $isOwner = $wishlist->user_id === Auth::id();

        return view('wishlists.show', compact('wishlist', 'items', 'isOwner'));
    }

    /**
     * Display wishlist by token (shareable link).
     */
    public function showByToken($token)
    {
        $wishlist = Wishlist::where('token', $token)->firstOrFail();

        // Check if wishlist is public
        if (!$wishlist->is_public) {
            return redirect()->back()->with('error', 'Esta lista de desejos é privada.');
        }

        $items = $wishlist->items()->get();
        $owner = $wishlist->user;

        return view('wishlists.shared', compact('wishlist', 'items', 'owner'));
    }

    /**
     * Create a new wishlist.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $wishlist = Auth::user()->wishlists()->create($validated);

        return redirect()->route('wishlists.show', $wishlist)->with('success', 'Lista de desejos criada com sucesso!');
    }

    /**
     * Update the specified wishlist.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        // Verify the wishlist belongs to the authenticated user
        if ($wishlist->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $wishlist->update($validated);

        return redirect()->back()->with('success', 'Lista de desejos atualizada com sucesso!');
    }

    /**
     * Delete the specified wishlist.
     */
    public function destroy(Wishlist $wishlist)
    {
        // Verify the wishlist belongs to the authenticated user
        if ($wishlist->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $wishlist->delete();

        return redirect()->route('wishlists.index')->with('success', 'Lista de desejos removida com sucesso!');
    }

    /**
     * Add an item to a wishlist.
     */
    public function addItem(Request $request, Wishlist $wishlist, Item $item)
    {
        // Verify the wishlist belongs to the authenticated user
        if ($wishlist->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $wishlist->items()->syncWithoutDetaching($item->id);

        return redirect()->back()->with('success', 'Item adicionado à lista de desejos!');
    }

    /**
     * Remove an item from a wishlist.
     */
    public function removeItem(Wishlist $wishlist, Item $item)
    {
        // Verify the wishlist belongs to the authenticated user
        if ($wishlist->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $wishlist->items()->detach($item->id);

        return redirect()->back()->with('success', 'Item removido da lista de desejos!');
    }
}

