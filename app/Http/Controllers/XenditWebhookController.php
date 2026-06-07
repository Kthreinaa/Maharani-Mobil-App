<?php

namespace App\Http\Controllers;

use App\Services\XenditPaymentLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class XenditWebhookController extends Controller
{
    public function invoice(Request $request, XenditPaymentLinkService $xendit): JsonResponse
    {
        abort_unless($xendit->hasValidWebhookToken($request), 403);

        $payload = $request->all();
        if (is_array($payload)) {
            $xendit->handleInvoiceWebhook($payload);
        }

        return response()->json(['received' => true]);
    }
}
