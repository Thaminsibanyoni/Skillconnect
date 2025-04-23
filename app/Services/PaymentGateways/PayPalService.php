<?php

namespace App\Services\PaymentGateways;

use Srmklive\PayPal\Services\PayPal as PayPalClient; // Alias the facade/provider
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected PayPalClient $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        // Load credentials from config automatically by the package service provider
        $this->provider->getAccessToken(); // Authenticate
    }

    /**
     * Create a PayPal order.
     */
    public function createOrder(float $amount, string $currency, string $returnUrl, string $cancelUrl, string $description = 'Order Payment')
    {
        try {
            $response = $this->provider->createOrder([
                "intent" => "CAPTURE", // Or "AUTHORIZE"
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => number_format($amount, 2, '.', '') // Format amount correctly
                        ],
                        'description' => $description,
                    ]
                ],
                "application_context" => [
                    "cancel_url" => $cancelUrl,
                    "return_url" => $returnUrl,
                ]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                // Find the approval link to redirect the user
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return [
                            'success' => true,
                            'approval_link' => $link['href'],
                            'order_id' => $response['id']
                        ];
                    }
                }
                Log::error('PayPal Create Order: No approval link found.', $response);
                return ['success' => false, 'message' => 'Could not find PayPal approval link.'];
            } else {
                Log::error('PayPal Create Order Error:', $response);
                return ['success' => false, 'message' => $response['message'] ?? 'Error creating PayPal order.'];
            }
        } catch (\Exception $e) {
            Log::error('PayPal Create Order Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error communicating with PayPal.'];
        }
    }

    /**
     * Capture the payment for a PayPal order.
     */
    public function captureOrder(string $paypalOrderId)
    {
        try {
            $response = $this->provider->capturePaymentOrder($paypalOrderId);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                 return ['success' => true, 'data' => $response];
            } else {
                 Log::error('PayPal Capture Order Error:', $response);
                 return ['success' => false, 'message' => $response['message'] ?? 'Error capturing PayPal payment.'];
            }
        } catch (\Exception $e) {
             Log::error('PayPal Capture Order Exception: ' . $e->getMessage());
             return ['success' => false, 'message' => 'Error communicating with PayPal.'];
        }
    }

    // Add methods for handling webhooks if needed
}
