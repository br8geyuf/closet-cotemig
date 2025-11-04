<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RecommendationController extends Controller
{
    public function getRecommendations(Request $request, $item_id)
    {
        $item = Item::find($item_id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // Fetch all items to pass to the Python script
        $items = Item::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description, // Assuming description is used for recommendations
            ];
        })->toArray();

        $items_json = json_encode($items);

        $process = new Process(['python3', base_path('recommendation_system.py'), $item_id, $items_json]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = json_decode($process->getOutput(), true);

        return response()->json($output);
    }
}

