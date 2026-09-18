<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.xendit.co';

    public function __construct()
    {
        $this->secretKey = config('xendit.secret_key');
    }

    /**
     * Create a Xendit Invoice for the given order.
     * Returns ['invoice_id', 'invoice_url', 'expires_at'] or throws.
     */
    public function createInvoice(Order $order): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/v2/invoices", [
                'external_id'       => $order->order_number,
                'amount'            => (int) $order->amount,
                'description'       => 'Pembayaran Kursus: ' . $order->course->title,
                'payer_email'       => $order->user->email,
                'customer'          => [
                    'given_names' => $order->user->name,
                    'email'       => $order->user->email,
                ],
                'success_redirect_url' => route('payment.success', $order->order_number),
                'failure_redirect_url' => route('payment.failed',  $order->order_number),
                'currency'          => 'IDR',
                'invoice_duration'  => 86400, // 24 hours
                'items'             => [[
                    'name'     => $order->course->title,
                    'quantity' => 1,
                    'price'    => (int) $order->amount,
                    'category' => 'Education',
                ]],
            ]);

        if ($response->failed()) {
            Log::error('Xendit createInvoice failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            throw new \RuntimeException('Gagal membuat invoice pembayaran: ' . ($response->json('message') ?? 'Unknown error'));
        }

        $data = $response->json();

        return [
            'invoice_id'  => $data['id'],
            'invoice_url' => $data['invoice_url'],
            'expires_at'  => now()->addDay(),
        ];
    }

    /**
     * Verify Xendit webhook callback token.
     */
    public function verifyWebhookToken(string $token): bool
    {
        return $token === config('xendit.webhook_token');
    }

    /**
     * Fetch invoice status directly from Xendit API.
     */
    public function getInvoiceStatus(string $invoiceId): string
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

        if ($response->failed()) {
            return 'unknown';
        }

        return strtolower($response->json('status', 'unknown'));
    }
}
