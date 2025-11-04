<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function pix(Request $request)
    {
        $items = json_decode($request->input('items'), true);
        $total = $request->input('total');

        // Aqui você pode gerar o QR Code Pix com alguma API de pagamento (ex: Gerencianet, MercadoPago, PagSeguro)
        // Por enquanto, vamos apenas enviar os dados para uma view Pix.

        return view('checkout.pix', compact('items', 'total'));
    }
}
