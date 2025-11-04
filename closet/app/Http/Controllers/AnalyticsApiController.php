<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsApiController extends Controller
{
    /**
     * Armazena um novo evento de analytics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            "event_type" => "required|string|max:255",
            "payload" => "nullable|array",
        ]);

        $event = Event::create([
            "user_id" => auth()->id(),
            "company_id" => auth()->guard("company")->id(),
            "event_type" => $request->event_type,
            "payload" => $request->payload,
        ]);

        return response()->json($event, 201);
    }

    /**
     * Retorna um relatório de analytics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReport(Request $request)
    {
        $request->validate([
            "event_type" => "required|string|max:255",
            "start_date" => "nullable|date",
            "end_date" => "nullable|date",
        ]);

        $query = Event::where("event_type", $request->event_type);

        if ($request->start_date) {
            $query->where("created_at", ">=", $request->start_date);
        }

        if ($request->end_date) {
            $query->where("created_at", "<=", $request->end_date);
        }

        $report = $query->select(DB::raw("DATE(created_at) as date"), DB::raw("count(*) as total"))
                        ->groupBy("date")
                        ->orderBy("date", "asc")
                        ->get();

        return response()->json($report);
    }
}

