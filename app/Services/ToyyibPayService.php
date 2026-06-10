<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Thin wrapper around the ToyyibPay (FPX) REST API.
 *
 * Credentials are read from the PaymentGateway settings row, falling back to
 * the TOYYIBPAY_SECRET_KEY / TOYYIB_CODE environment variables.
 *
 * @see https://toyyibpay.com/apireference/
 */
class ToyyibPayService
{
    protected ?PaymentGateway $gateway;

    public function __construct(?PaymentGateway $gateway = null)
    {
        $this->gateway = $gateway ?: PaymentGateway::current();
    }

    public function isConfigured(): bool
    {
        return ! empty($this->secretKey()) && ! empty($this->categoryCode());
    }

    public function secretKey(): ?string
    {
        return $this->gateway?->secret_key ?: env('TOYYIBPAY_SECRET_KEY');
    }

    public function categoryCode(): ?string
    {
        return $this->gateway?->category_code ?: env('TOYYIB_CODE');
    }

    public function isSandbox(): bool
    {
        if ($this->gateway) {
            return (bool) $this->gateway->is_sandbox;
        }

        return (bool) env('TOYYIBPAY_SANDBOX', true);
    }

    public function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://dev.toyyibpay.com'
            : 'https://toyyibpay.com';
    }

    /**
     * Create a bill and return its bill code, or null on failure.
     *
     * @param  float  $amount        Amount in RM (Ringgit).
     * @param  array  $payer         ['name' => ..., 'email' => ..., 'phone' => ...]
     */
    public function createBill(
        float $amount,
        string $billName,
        string $billDescription,
        string $returnUrl,
        string $callbackUrl,
        array $payer,
        string $externalReference = 'WMS'
    ): ?string {
        if (! $this->isConfigured()) {
            return null;
        }

        $payload = [
            'userSecretKey'           => $this->secretKey(),
            'categoryCode'            => $this->categoryCode(),
            'billName'                => Str::limit($billName, 30, ''),
            'billDescription'         => Str::limit($billDescription, 100, ''),
            'billPriceSetting'        => 1,
            'billPayorInfo'           => 1,
            // ToyyibPay expects the amount in cents.
            'billAmount'              => (int) round($amount * 100),
            'billReturnUrl'           => $returnUrl,
            'billCallbackUrl'         => $callbackUrl,
            'billExternalReferenceNo' => $externalReference,
            'billTo'                  => Str::limit($payer['name'] ?? '', 30, ''),
            'billEmail'               => $payer['email'] ?? '',
            'billPhone'               => $payer['phone'] ?? '0000000000',
            'billSplitPayment'        => 0,
            'billSplitPaymentArgs'    => '',
            'billPaymentChannel'      => 2,
            'billContentEmail'        => 'Thank you for subscribing!',
            'billChargeToCustomer'    => 1,
        ];

        $response = Http::asForm()->post($this->baseUrl() . '/index.php/api/createBill', $payload);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        // Success response is an array like [{"BillCode":"xxxxxxxx"}].
        if (is_array($data) && isset($data[0]['BillCode'])) {
            return $data[0]['BillCode'];
        }

        return null;
    }

    /**
     * Build the hosted payment page URL for a given bill code.
     */
    public function billUrl(string $billCode): string
    {
        return $this->baseUrl() . '/' . $billCode;
    }

    /**
     * Query the transaction(s) attached to a bill code.
     *
     * Returns the decoded array of transactions, or an empty array on failure.
     */
    public function getBillTransactions(string $billCode): array
    {
        $response = Http::asForm()->post($this->baseUrl() . '/index.php/api/getBillTransactions', [
            'billCode' => $billCode,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    /**
     * Whether the bill has been paid (status "1" = success at ToyyibPay).
     */
    public function isBillPaid(string $billCode): bool
    {
        foreach ($this->getBillTransactions($billCode) as $transaction) {
            if (($transaction['billpaymentStatus'] ?? null) === '1') {
                return true;
            }
        }

        return false;
    }
}
