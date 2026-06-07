<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\TransactionDocumentProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionDocumentController extends Controller
{
    public function download(Request $request, Order $order, string $type)
    {
        abort_unless(in_array($type, ['invoice', 'receipt', 'handover_note'], true), Response::HTTP_NOT_FOUND);

        $user = $request->user();
        abort_unless(
            $user && ($user->id === $order->user_id || in_array($user->role, ['marketing', 'supervisor', 'owner'], true)),
            Response::HTTP_FORBIDDEN
        );

        $order->load(['user', 'car', 'payment.verifier', 'handledBy', 'approvedBy']);

        abort_unless($order->areTransactionDocumentsReady(), Response::HTTP_FORBIDDEN);

        $pdf = Pdf::loadView($this->viewFor($type), [
            'order' => $order,
            'type' => $type,
            'documentTitle' => $this->titleFor($type),
            'documentProfile' => TransactionDocumentProfile::build($order),
        ]);

        return $pdf->download($this->filenameFor($order, $type));
    }

    private function titleFor(string $type): string
    {
        return match ($type) {
            'receipt' => 'Kwitansi Digital Pembayaran',
            'handover_note' => 'Berita Acara Serah Terima Kendaraan',
            default => 'Faktur Pembelian Unit',
        };
    }

    private function filenameFor(Order $order, string $type): string
    {
        $prefix = match ($type) {
            'receipt' => 'kwitansi-digital',
            'handover_note' => 'berita-acara-serah-terima',
            default => 'faktur-pembelian',
        };

        return $prefix . '-order-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) . '.pdf';
    }

    private function viewFor(string $type): string
    {
        return match ($type) {
            'receipt' => 'documents.receipt',
            'handover_note' => 'documents.handover-note',
            default => 'documents.invoice',
        };
    }
}
