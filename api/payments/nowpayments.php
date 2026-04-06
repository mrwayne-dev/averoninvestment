<?php
/* =====================================================================
   api/payments/nowpayments.php
   NowPayments API wrapper class.

   Handles:
     createPayment()          — initiate a crypto deposit
     getPaymentStatus()       — poll payment status
     getMinimumPaymentAmount() — fetch minimum crypto amount
     verifyIPN()              — static, verifies webhook HMAC signature
   ===================================================================== */

class NowPayments
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = NOWPAYMENTS_API_KEY;
        $this->baseUrl = rtrim(NOWPAYMENTS_API_URL, '/');
    }

    // ── Private HTTP client ───────────────────────────────────────────

    /**
     * Make an HTTP request to the NowPayments REST API.
     *
     * @param string $method   GET | POST
     * @param string $endpoint e.g. '/payment'
     * @param array  $data     Body (POST) or query params (GET)
     * @return array Decoded JSON response
     * @throws RuntimeException on cURL failure, empty response, or HTTP 4xx/5xx
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            // In production SSL peer verification is always on.
            // In development the php.ini curl.cainfo path may point to a
            // stale drive letter (e.g. D: when Laragon is on C:), so we
            // skip peer verification locally to avoid that cURL error.
            CURLOPT_SSL_VERIFYPEER => (defined('APP_ENV') && APP_ENV !== 'development'),
            CURLOPT_SSL_VERIFYHOST => (defined('APP_ENV') && APP_ENV !== 'development') ? 2 : 0,
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $this->apiKey,
                'Content-Type: application/json',
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_URL,        $url);
            curl_setopt($ch, CURLOPT_POST,       true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            // GET — append query string if data provided
            $fullUrl = !empty($data) ? $url . '?' . http_build_query($data) : $url;
            curl_setopt($ch, CURLOPT_URL,           $fullUrl);
            curl_setopt($ch, CURLOPT_HTTPGET,        true);
        }

        $response  = curl_exec($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError !== '') {
            throw new RuntimeException('cURL error: ' . $curlError);
        }

        if ($response === false || $response === '') {
            throw new RuntimeException(
                'Empty response from NowPayments API. HTTP ' . $httpCode
            );
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException(
                'Non-JSON response from NowPayments. HTTP ' . $httpCode
                . ': ' . substr($response, 0, 300)
            );
        }

        // 4xx / 5xx — throw with API's own message
        if ($httpCode >= 400) {
            $msg = $decoded['message'] ?? ($decoded['error'] ?? 'HTTP ' . $httpCode);
            throw new RuntimeException('NowPayments API error (' . $httpCode . '): ' . $msg);
        }

        return $decoded;
    }

    // ── Public methods ────────────────────────────────────────────────

    /**
     * Create a new crypto payment.
     *
     * @param float  $amountUsd   USD amount to charge
     * @param string $currency    Pay currency (btc, eth, usdttrc20, usdterc20)
     * @param string $orderId     Internal order reference (DEP-{timestamp}-{user_id})
     * @param string $callbackUrl Webhook URL for IPN notifications
     *
     * @return array {
     *   payment_id, pay_address, pay_amount, pay_currency,
     *   payment_status, qr_code_url
     * }
     */
    public function createPayment(
        float  $amountUsd,
        string $currency,
        string $orderId,
        string $callbackUrl
    ): array {
        $result = $this->request('POST', '/payment', [
            'price_amount'     => $amountUsd,
            'price_currency'   => 'usd',
            'pay_currency'     => strtolower($currency),
            'order_id'         => $orderId,
            'ipn_callback_url' => $callbackUrl,
        ]);

        // Fallback: build Google Charts QR if API did not return one
        if (empty($result['qr_code_url']) && !empty($result['pay_address'])) {
            $result['qr_code_url'] = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='
                . urlencode($result['pay_address']);
        }

        return $result;
    }

    /**
     * Create a NowPayments hosted invoice (payment page).
     * User is redirected to invoice_url to complete payment via NowPayments UI.
     *
     * @param float  $amountUsd      USD amount to charge
     * @param string $currency       Pay currency (btc, eth, usdttrc20, usdterc20)
     * @param string $orderId        Internal order reference (DEP-{timestamp}-{user_id})
     * @param string $ipnCallbackUrl Webhook URL for IPN notifications
     * @param string $successUrl     Redirect URL after successful payment
     * @param string $cancelUrl      Redirect URL if user cancels
     *
     * @return array { id, token_id, invoice_url }
     */
    public function createInvoice(
        float  $amountUsd,
        string $currency,
        string $orderId,
        string $ipnCallbackUrl,
        string $successUrl,
        string $cancelUrl
    ): array {
        return $this->request('POST', '/invoice', [
            'price_amount'      => $amountUsd,
            'price_currency'    => 'usd',
            'pay_currency'      => strtolower($currency),
            'order_id'          => $orderId,
            'order_description' => 'Averon Investment deposit',
            'ipn_callback_url'  => $ipnCallbackUrl,
            'success_url'       => $successUrl,
            'cancel_url'        => $cancelUrl,
        ]);
    }

    /**
     * Fetch the current status of a payment.
     *
     * @return array {
     *   payment_status, pay_amount, actually_paid, outcome_amount
     * }
     */
    public function getPaymentStatus(string $paymentId): array
    {
        return $this->request('GET', '/payment/' . rawurlencode($paymentId));
    }

    /**
     * Fetch the minimum acceptable crypto amount for a given currency.
     *
     * @param string $currency e.g. 'btc'
     * @return float Minimum amount in crypto units
     */
    public function getMinimumPaymentAmount(string $currency): float
    {
        $result = $this->request('GET', '/min-amount', [
            'currency_from' => strtolower($currency),
            'currency_to'   => 'usd',
        ]);

        return (float) ($result['min_amount'] ?? 0.0);
    }

    /**
     * Verify a NowPayments IPN webhook signature.
     * Compares HMAC-SHA512 of the raw payload with the provided header value.
     *
     * @param string $payload   Raw request body (file_get_contents('php://input'))
     * @param string $signature Value of HTTP_X_NOWPAYMENTS_SIG header
     * @return bool True if signature is valid
     */
    public static function verifyIPN(string $payload, string $signature): bool
    {
        if (empty($signature) || empty($payload)) {
            return false;
        }

        $expected = hash_hmac('sha512', $payload, NOWPAYMENTS_IPN_SECRET);

        // Use hash_equals to prevent timing attacks
        return hash_equals($expected, strtolower($signature));
    }
}
