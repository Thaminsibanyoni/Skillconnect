<?php

namespace App\Services\PaymentGateways;

use Omnipay\Omnipay;
use Omnipay\Common\GatewayInterface;

class PayFastService
{
    protected GatewayInterface $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayFast');

        $this->gateway->setMerchantId(config('services.payfast.merchant_id'));
        $this->gateway->setMerchantKey(config('services.payfast.merchant_key'));
        $this->gateway->setPassphrase(config('services.payfast.passphrase'));
        $this->gateway->setTestMode((bool) config('services.payfast.test_mode'));
    }

    public function purchase(array $parameters): \Omnipay\Common\Message\ResponseInterface
    {
        // Parameters typically include:
        // 'amount', 'currency', 'transactionId', 'returnUrl', 'cancelUrl', 'notifyUrl'
        // 'card' (optional, for specific integrations)
        // 'description' or 'items'
        // 'clientIp', etc.
        return $this->gateway->purchase($parameters)->send();
    }

    public function completePurchase(array $parameters): \Omnipay\Common\Message\ResponseInterface
    {
        // Parameters usually come from the ITN callback request
        return $this->gateway->completePurchase($parameters)->send();
    }

    // Add other methods as needed (e.g., fetchTransaction, refund)

    public function getGateway(): GatewayInterface
    {
        return $this->gateway;
    }
}
