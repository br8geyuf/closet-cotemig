<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Item; // Assumindo que os produtos do Shopify serão mapeados para o modelo Item

class ShopifyIntegrationController extends Controller
{
    /**
     * Lida com o webhook de criação de produto do Shopify.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleProductCreate(Request $request)
    {
        Log::info("Shopify Webhook: Product Created", $request->all());

        // Aqui você implementaria a lógica para criar um novo Item no seu banco de dados
        // com base nos dados recebidos do Shopify.
        // Exemplo simplificado:
        // Item::create([
        //     'name' => $request->input('title'),
        //     'description' => $request->input('body_html'),
        //     'shopify_product_id' => $request->input('id'),
        //     // Mapear outros campos relevantes
        // ]);

        return response()->json(["message" => "Product create webhook received and processed."], 200);
    }

    /**
     * Lida com o webhook de atualização de produto do Shopify.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleProductUpdate(Request $request)
    {
        Log::info("Shopify Webhook: Product Updated", $request->all());

        // Aqui você implementaria a lógica para atualizar um Item existente no seu banco de dados
        // com base nos dados recebidos do Shopify.
        // Exemplo simplificado:
        // $item = Item::where('shopify_product_id', $request->input('id'))->first();
        // if ($item) {
        //     $item->update([
        //         'name' => $request->input('title'),
        //         'description' => $request->input('body_html'),
        //         // Mapear outros campos relevantes
        //     ]);
        // }

        return response()->json(["message" => "Product update webhook received and processed."], 200);
    }

    /**
     * Lida com o webhook de exclusão de produto do Shopify.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleProductDelete(Request $request)
    {
        Log::info("Shopify Webhook: Product Deleted", $request->all());

        // Aqui você implementaria a lógica para excluir um Item do seu banco de dados
        // com base no ID do produto recebido do Shopify.
        // Exemplo simplificado:
        // Item::where('shopify_product_id', $request->input('id'))->delete();

        return response()->json(["message" => "Product delete webhook received and processed."], 200);
    }
}

