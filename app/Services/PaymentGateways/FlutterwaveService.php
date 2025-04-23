<?php

namespace App\Services\PaymentGateways;

use Bhekor\LaravelFlutterwave\Facades\Flutterwave; // Use the Facade
use Illuminate\Http\Request; // Import Request for webhook verification
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterwaveService
{
    // No constructor needed if using Facade and config file


    /**
     * Initiate a payment using Standard Checkout.
     *
     * @param array $data Payment details (tx_ref, amount, currency, redirect_url, customer, customizations)
     * @return array Response containing success status, message, and payment link/data
     */
    public function initiatePayment(array $data): array
    {
        try {
            // Use the Facade's method (assuming it matches this signature)
            $response = Flutterwave::initializePayment($data);

            // Check response structure based on package's actual return value
            if (isset($response['status']) && $response['status'] === 'success' && isset($response['data']['link'])) {
                 Log::info('Flutterwave Payment Initiation Success:', $response);
                return [
                    'success' => true,
                    'message' => $response->json('message'),
                    'link' => $response['data']['link']
                ];
            } else {
                 Log::error('Flutterwave Payment Initiation Error:', (array)$response);
                 return ['success' => false, 'message' => $response['message'] ?? 'Failed to initiate Flutterwave payment.'];
            }
        } catch (\Exception $e) {
            Log::error('Flutterwave Payment Initiation Exception: ' . $e->getMessage(), $data);
            return ['success' => false, 'message' => 'Error communicating with Flutterwave.'];
        }
    }

    /**
     * Verify a transaction using the transaction ID provided by Flutterwave.
     *
     * @param string $flutterwaveTransactionId The transaction ID from Flutterwave (usually numeric)
     * @return array Response containing success status, message, and transaction data
     */
    public function verifyTransaction(string $flutterwaveTransactionId): array
    {
         try {
            // Use the Facade's method
            $response = Flutterwave::verifyTransaction($flutterwaveTransactionId);

            // Check response structure based on package's actual return value
            if (isset($response['status']) && $response['status'] === 'success') {
                 Log::info('Flutterwave Transaction Verification Success:', $response);
                 return [
                     'success' => true,
                     'message' => $response['message'] ?? 'Transaction verified.',
                     'data' => $response['data'] ?? []
                 ];
            } else {
                 Log::error('Flutterwave Transaction Verification Error:', (array)$response);
                 return ['success' => false, 'message' => $response['message'] ?? 'Failed to verify Flutterwave transaction.'];
            }
        } catch (\Exception $e) {
             Log::error('Flutterwave Transaction Verification Exception: ' . $e->getMessage(), ['id' => $flutterwaveTransactionId]);
             return ['success' => false, 'message' => 'Error communicating with Flutterwave.'];
        }
    }

    /**
     * Verify the webhook signature using the Facade.
     */
    public function verifyWebhookSignature(): bool
    {
        // Use the Facade's method
        return Flutterwave::verifyWebhook();
    }

    // Add other methods as needed (e.g., accessing Banks, Transfers via Facade)
    // public function getBanks() { return Flutterwave::banks()->nigeria(); }
}
