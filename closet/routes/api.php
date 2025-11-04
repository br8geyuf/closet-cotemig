<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ChatController; // Adicionado

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui ficam as rotas da API.
| Já deixei uma rota de teste e um CRUD de items.
|
*/

// rota de teste (ver se API está rodando)
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando ✅']);
});

// CRUD de items (gera todas as rotas REST automaticamente)
Route::resource('items', ItemController::class);

// rota autenticada (já estava no seu código)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get("/companies/{company}", [App\Http\Controllers\CompanyApiController::class, "show"]);


Route::get("/marketplace/items", [App\Http\Controllers\MarketplaceApiController::class, "index"]);


Route::get("/marketplace/items/{item}", [App\Http\Controllers\MarketplaceApiController::class, "show"]);


Route::post("/analytics/event", [App\Http\Controllers\AnalyticsApiController::class, "storeEvent"]);
Route::get("/recommendations/{item_id}", [App\Http\Controllers\RecommendationController::class, "getRecommendations"]);
Route::get("/analytics/report", [App\Http\Controllers\AnalyticsApiController::class, "getReport"]);


Route::post("/shopify/webhook/products/create", [App\Http\Controllers\ShopifyIntegrationController::class, "handleProductCreate"]);
Route::post("/shopify/webhook/products/update", [App\Http\Controllers\ShopifyIntegrationController::class, "handleProductUpdate"]);
Route::post("/shopify/webhook/products/delete", [App\Http\Controllers\ShopifyIntegrationController::class, "handleProductDelete"]);

// Rotas de Chat
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/start/{user_id}', [ChatController::class, 'startChat']);
    // Outras rotas de chat podem ser adicionadas aqui, como listar mensagens, enviar, etc.
});
