<?php

namespace App\Http\Controllers;

use App\Models\RecentView;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentViewController extends Controller
{
    /**
     * Record a view of an item.
     */
    public function record(Item $item)
    {
        if (Auth::check()) {
            // Delete existing view for this item if it exists
            RecentView::where('user_id', Auth::id())
                ->where('item_id', $item->id)
                ->delete();

            // Create a new view record
            RecentView::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get recent views for the authenticated user.
     */
    public function getRecent($limit = 10)
    {
        if (!Auth::check()) {
            return response()->json(['items' => []]);
        }

        $recentViews = RecentView::where('user_id', Auth::id())
            ->with('item')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json(['items' => $recentViews->pluck('item')]);
    }

    /**
     * Display recent views in profile.
     */
    public function index()
    {
        $recentItems = RecentView::where('user_id', Auth::id())
            ->with('item')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->pluck('item');

        return view('recent-views.index', compact('recentItems'));
    }

    /**
     * Clear all recent views.
     */
    public function clear()
    {
        RecentView::where('user_id', Auth::id())->delete();

        return redirect()->back()->with('success', 'Histórico de visualizações limpo!');
    }
}

