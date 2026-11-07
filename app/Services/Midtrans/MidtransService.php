<?php

namespace App\Services\Midtrans;

use App\Models\Registrant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper tipis untuk Midtrans Snap API (sandbox / production).
 *
 * Semua kredensial dibaca dari konfigurasi `midtrans`.
 */
class MidtransService
{
    public function isConfigured(): bool
    {
        return ! empty(config('midtrans.server_key'));
    }

    private function serverKey(): string
    {
        return (string) config('midtrans.server_key');
    }

    private function snapUrl(): string
    {
        return rtrim((string) config('midtrans.snap_url'), '/');
    }

    private function apiUrl(): string
    {
        return rtrim((string) config('midtrans.api_url'), '/');
    }

    private function clientKey(): string
    {
        return (string) config('midtrans.client_key');
    }

    private function auth(): string
    {
        // Midtrans Snap API memakai Basic Auth dengan server key sebagai username dan password kosong.
        return 'Basic '.base64_encode($this->serverKey().':');
    }

    public function clientKeyValue(): string
    {
        return $this->clientKey();
    }

    /**
     * Buat transaksi Snap; simpan token & redirect URL di registrant.
     *
     * @return array{order_id: string, token: string, redirect_url: string}
     *
     * @throws \RuntimeException
     */
    public function createSnapTransaction(Registrant $registrant): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Midtrans belum dikonfigurasi (server key kosong).');
        }

        $registrant->loadMissing(['event', 'ticketTier', 'addons.addon']);

        if (! $registrant->event || ! $registrant->ticketTier) {
            throw new \RuntimeException('Data event/tiket tidak lengkap untuk membuat transaksi Midtrans.');
        }

        $orderId = $registrant->participant_token ?? 'PART-'.strtoupper(uniqid());
        $total = (int) round((float) $registrant->total);

        $customerEmail = $this->customerEmail($registrant);
        $customerPhone = trim((string) $registrant->phone);

        $items = [
            [
                'id' => $registrant->ticket_tier_id,
                'price' => (int) round((float) $registrant->ticketTier->price),
                'quantity' => 1,
                'name' => 'Tiket '.$registrant->ticketTier->name,
            ],
        ];

        foreach ($registrant->addons as $addon) {
            $items[] = [
                'id' => $addon->addon_id ?? (string) $addon->id,
                'price' => (int) round((float) $addon->price),
                'quantity' => (int) $addon->qty,
                'name' => 'Add-on '.($addon->addon?->name ?? 'Add-on'),
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'item_details' => $items,
            'customer_details' => array_filter([
                'first_name' => $registrant->full_name,
                'email' => $customerEmail,
                'phone' => $customerPhone !== '' ? $customerPhone : null,
            ], fn ($v) => $v !== null && $v !== ''),
            'callbacks' => [
                'finish' => url('/participant/'.$orderId.'/done'),
            ],
            'expiry' => [
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => $this->auth(),
        ])->post($this->snapUrl().'/transactions', $payload);

        $body = $response->json();

        if ($response->failed() || empty($body['token'])) {
            Log::error('Midtrans Snap transaction failed', [
                'order_id' => $orderId,
                'http_status' => $response->status(),
                'body' => $body,
            ]);

            throw new \RuntimeException(
                'Gagal membuat transaksi Midtrans: '.($body['error_messages'][0] ?? $body['message'] ?? $response->body())
            );
        }

        $registrant->midtrans_order_id = $orderId;
        $registrant->midtrans_snap_token = $body['token'];
        $registrant->midtrans_snap_redirect_url = $body['redirect_url'] ?? null;
        $registrant->midtrans_status = 'pending';
        $registrant->save();

        return [
            'order_id' => $orderId,
            'token' => $body['token'],
            'redirect_url' => $body['redirect_url'] ?? null,
        ];
    }

    /**
     * Probe koneksi ke Midtrans Snap API dengan satu transaksi kecil — membuktikan seluruh rantai (kredensial → jaringan → API) bekerja.
     *
     * @return array{http_status: int, order_id: string, token: string|null, redirect_url: string|null, message: string|null}
     */
    public function probeConnection(int $amount = 1000): array
    {
        if (! $this->isConfigured()) {
            return ['http_status' => 0, 'order_id' => '', 'token' => null, 'redirect_url' => null, 'message' => 'Server key belum dikonfigurasi.'];
        }

        $amount = max(1, (int) $amount);
        $orderId = 'PROBE-'.strtoupper(uniqid());

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [[
                'id' => 'PROBE',
                'price' => $amount,
                'quantity' => 1,
                'name' => 'Uji Koneksi Midtrans',
            ]],
            'customer_details' => [
                'first_name' => 'Probe',
                'email' => 'probe@mail.com',
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => $this->auth(),
        ])->post($this->snapUrl().'/transactions', $payload);

        $body = $response->json() ?: [];
        $token = $body['token'] ?? null;

        return [
            'http_status' => $response->status(),
            'order_id' => $orderId,
            'token' => $token,
            'redirect_url' => $body['redirect_url'] ?? null,
            'message' => $body['error_messages'][0] ?? $body['message'] ?? null,
        ];
    }

    /**
     * Email pelanggan menghadap Midtrans: harus valid & bukan domain reserved.
     * Jika tidak memenuhi, fallback ke alamat aman agar transaksi tidak ditolak Midtrans.
     */
    public function customerEmail(Registrant $registrant): string
    {
        $email = mb_strtolower(trim((string) $registrant->email));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && ! $this->isReservedDomain($email)) {
            return $email;
        }

        $hash = strtolower(substr(md5($registrant->id.'|'.$registrant->participant_token), 0, 12));

        return 'customer-'.$hash.'@mail.com';
    }

    /**
     * TLD reserved (RFC 2606/6761) yang Midtrans tolak sebagai "format email invalid".
     */
    private function isReservedDomain(string $email): bool
    {
        $dot = strrpos($email, '.');

        if ($dot === false) {
            return true;
        }

        $tld = mb_strtolower(substr($email, $dot + 1));

        return in_array($tld, ['test', 'example', 'invalid', 'localhost', 'internal'], true);
    }

    /**
     * Verifikasi notifikasi webhook Midtrans (signature_key) dan terapkan ke registrant.
     *
     * @return array{status: string, reference: string|null}
     *
     * @throws \RuntimeException jika signature tidak valid.
     */
    public function applyNotification(array $notification): array
    {
        if (! $this->verifyNotificationSignature($notification)) {
            throw new \RuntimeException('Signature Midtrans tidak valid.');
        }

        $orderId = (string) ($notification['order_id'] ?? '');
        $status = (string) ($notification['transaction_status'] ?? '');

        $registrant = Registrant::where('participant_token', $orderId)
            ->orWhere('midtrans_order_id', $orderId)
            ->first();

        if (! $registrant) {
            throw new \RuntimeException('Registrant tidak ditemukan untuk order_id "'.$orderId.'".');
        }

        $registrant->midtrans_order_id = $orderId;
        $registrant->midtrans_status = $status;

        match ($status) {
            'capture', 'settlement' => $this->markPaidFromMidtrans($registrant, $notification),
            'pending' => $this->markPendingFromMidtrans($registrant, $notification),
            'deny', 'cancel', 'expire' => $this->markCancelledFromMidtrans($registrant, $notification),
            default => null,
        };

        $registrant->save();

        return [
            'status' => $status,
            'reference' => $notification['transaction_id'] ?? null,
        ];
    }

    /**
     * Sinkronisasi status transaksi langsung dari server Midtrans (dipakai sisi klien lokal).
     *
     * @throws \RuntimeException
     */
    public function syncStatus(Registrant $registrant): array
    {
        $orderId = (string) ($registrant->midtrans_order_id ?: $registrant->participant_token);

        if ($orderId === '') {
            throw new \RuntimeException('Tidak ada order_id Midtrans untuk registrant ini.');
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $this->auth(),
        ])->get($this->apiUrl().'/'.$orderId.'/status');

        $body = $response->json();

        if ($response->failed() || empty($body['transaction_status'])) {
            throw new \RuntimeException('Gagal mengambil status Midtrans: '.$response->status());
        }

        // terapkan hasil API status sama seperti webhook
        return $this->applyNotification($body);
    }

    /**
     * signature_key Midtrans = SHA512(order_id + status_code + gross_amount + server_key).
     */
    public function verifyNotificationSignature(array $notification): bool
    {
        $orderId = (string) ($notification['order_id'] ?? '');
        $statusCode = (string) ($notification['status_code'] ?? '');
        $grossAmount = (string) ($notification['gross_amount'] ?? '');
        $signatureKey = (string) ($notification['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey());

        return hash_equals($expected, $signatureKey);
    }

    private function markPaidFromMidtrans(Registrant $registrant, array $payload): void
    {
        $registrant->is_paid = true;
        $registrant->is_pending = false;
        $registrant->is_rejected = false;
        $registrant->status = 'paid';
        $registrant->paid_at = $registrant->paid_at ?? now();
        $registrant->paid_by = $payload['payment_type'] ?? 'midtrans';

        $transactionId = $payload['transaction_id'] ?? null;

        if ($transactionId) {
            $registrant->exchange_id = $transactionId;
        }
    }

    private function markPendingFromMidtrans(Registrant $registrant, array $payload): void
    {
        $registrant->is_paid = false;
        $registrant->is_pending = true;
        $registrant->status = 'pending';
        $registrant->paid_by = $payload['payment_type'] ?? 'midtrans';
    }

    private function markCancelledFromMidtrans(Registrant $registrant, array $payload): void
    {
        $registrant->is_paid = false;
        $registrant->is_pending = false;
        $registrant->is_rejected = true;
        $registrant->status = 'cancelled';
        $registrant->paid_by = $payload['payment_type'] ?? 'midtrans';
    }
}
