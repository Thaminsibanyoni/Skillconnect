<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentGateways\FlutterwaveService;
use App\Services\PaymentGateways\PayFastService;
use App\Services\PaymentGateways\PayPalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    protected $payfastService;
    protected $flutterwaveService;
    protected $paypalService;

    public function __construct(
        PayFastService $payfastService,
        FlutterwaveService $flutterwaveService,
        PayPalService $paypalService
    ) {
        $this->payfastService = $payfastService;
        $this->flutterwaveService = $flutterwaveService;
        $this->paypalService = $paypalService;
    }

    /**
     * Create a Stripe Payment Intent for an order.
     * (Placeholder implementation - Stripe/Cashier was removed)
     */
    public function createIntent(Request $request, Order $order): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->id !== $order->user_id || !in_array($order->status, ['pending', 'accepted'])) {
             return response()->json(['error' => 'Unauthorized or order not payable.'], 403);
        }
        return response()->json([
            'clientSecret' => 'pi_test_secret_'. Str::random(20), // Placeholder
            'amount' => $order->total_amount,
            'currency' => 'usd' // Placeholder currency
        ]);
    }

    /**
     * Show the page for a seeker to pay for a specific order.
     */
    public function showOrderPaymentPage(Request $request, Order $order): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || $user->id !== $order->user_id || !in_array($order->status, ['pending', 'accepted'])) {
             return redirect()->route('seeker.orders.index')->with('error', 'Order cannot be paid at this time.');
        }
        if (empty($order->total_amount) || $order->total_amount <= 0) {
             return redirect()->route('seeker.orders.index')->with('error', 'Order amount is not set.');
        }
        return view('payments.order', compact('order'));
    }


    // --- PayFast Methods ---

    public function redirectToPayFast(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || $user->id !== $order->user_id || !in_array($order->status, ['pending', 'accepted'])) {
             return redirect()->back()->with('error', 'Order is not valid for payment.');
        }
        try {
            $response = $this->payfastService->purchase([
                'amount' => $order->total_amount,
                'currency' => 'ZAR',
                'transactionId' => 'ORDER_' . $order->id . '_' . time(),
                'description' => 'Payment for Order #' . $order->id,
                'returnUrl' => route('payment.payfast.return'),
                'cancelUrl' => route('payment.payfast.cancel'),
                'notifyUrl' => route('payment.payfast.notify'),
                'firstName' => $user->name,
                'email' => $user->email,
            ])->send();

            if ($response->isRedirect()) {
                return $response->getRedirectResponse();
            } else {
                Log::error('PayFast Redirect Error: ' . $response->getMessage(), ['order_id' => $order->id]);
                return redirect()->back()->with('error', 'Could not initiate PayFast payment: ' . $response->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('PayFast Purchase Exception: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->back()->with('error', 'An error occurred while initiating payment.');
        }
    }

    public function handlePayFastITN(Request $request)
    {
        Log::info('PayFast ITN Received:', $request->all());
        try {
            // TODO: Implement PayFast ITN validation logic here first!
            $isValid = true; // Placeholder - REMOVE THIS IN PRODUCTION

            $payfastData = $request->all();
            $transactionId = $payfastData['m_payment_id'] ?? null;
            $pfPaymentId = $payfastData['pf_payment_id'] ?? null;
            $paymentStatus = $payfastData['payment_status'] ?? null;

            if ($isValid && $paymentStatus === 'COMPLETE') {
                Log::info('PayFast ITN Success:', $payfastData);

                if (Str::startsWith($transactionId, 'ORDER_')) {
                    $orderId = explode('_', $transactionId)[1] ?? null;
                    $order = $orderId ? Order::find($orderId) : null;
                    if ($order) {
                        $this->processCompletedOrderPayment($order, 'payfast', $pfPaymentId, $payfastData['amount_gross'] ?? $order->total_amount);
                    } else {
                        Log::warning('PayFast ITN: Order not found.', ['m_payment_id' => $transactionId]);
                    }
                } elseif (Str::startsWith($transactionId, 'SUB_')) {
                    $planSlug = $payfastData['custom_str1'] ?? null;
                    $userId = $payfastData['custom_int1'] ?? null;
                    $user = $userId ? User::find($userId) : null;
                    $plan = $planSlug ? SubscriptionPlan::where('slug', $planSlug)->first() : null;

                    if ($user && $plan) {
                        $this->activateSubscription($user, $plan, 'payfast', $pfPaymentId, $payfastData['amount_gross'] ?? $plan->price);
                    } else {
                         Log::warning('PayFast ITN: User or Plan not found for subscription.', ['m_payment_id' => $transactionId, 'data' => $payfastData]);
                    }
                } else {
                     Log::warning('PayFast ITN: Unknown transaction type.', ['m_payment_id' => $transactionId]);
                }
            } else {
                Log::error('PayFast ITN Failed/Invalid:', ['status' => $paymentStatus, 'data' => $payfastData]);
            }
        } catch (\Exception $e) {
            Log::error('PayFast ITN Exception: ' . $e->getMessage(), $request->all());
             return response('Error processing ITN', 500);
        }
        return response('OK', 200);
    }

    public function handlePayFastReturn(Request $request): View
    {
        $ref = $request->query('ref');
        if ($ref && Str::startsWith($ref, 'SUB_')) {
             return view('payments.success', ['message' => 'Subscription payment successful! Activation may take a moment.']);
        }
        return view('payments.success');
    }

    public function handlePayFastCancel(Request $request): View
    {
        return view('payments.cancelled');
    }

    // --- Flutterwave Methods ---

    public function redirectToFlutterwave(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || $user->id !== $order->user_id || !in_array($order->status, ['pending', 'accepted'])) {
             return redirect()->back()->with('error', 'Order is not valid for payment.');
        }
        $txRef = 'ORDER_' . $order->id . '_' . time();
        $paymentData = [
            'tx_ref' => $txRef,
            'amount' => $order->total_amount,
            'currency' => 'ZAR',
            'redirect_url' => route('payment.flutterwave.callback'),
            'customer' => [ 'email' => $user->email, 'name' => $user->name ],
            'customizations' => [ 'title' => config('app.name').' Payment', 'description' => 'Payment for Order #'.$order->id ],
            'meta' => [ 'order_id' => $order->id, 'user_id' => $user->id, 'type' => 'order' ]
        ];
        $response = $this->flutterwaveService->initiatePayment($paymentData);
        if ($response['success'] && isset($response['link'])) {
            return redirect()->away($response['link']);
        } else {
            Log::error('Flutterwave Redirect Error: ' . ($response['message'] ?? 'Unknown Error'), ['order_id' => $order->id]);
            return redirect()->back()->with('error', 'Could not initiate Flutterwave payment: ' . ($response['message'] ?? 'Unknown Error'));
        }
    }

    public function handleFlutterwaveCallback(Request $request): View|RedirectResponse
    {
        $status = $request->input('status');
        $txRef = $request->input('tx_ref');
        $transactionId = $request->input('transaction_id');
        Log::info('Flutterwave Callback Received:', $request->all());

        if ($status === 'successful' && $transactionId) {
            $verification = $this->flutterwaveService->verifyTransaction($transactionId);
            if ($verification['success'] && $verification['data']['status'] === 'successful') {
                $meta = $verification['data']['meta'] ?? [];
                $paymentType = $meta['type'] ?? null;
                $userId = $meta['user_id'] ?? null;
                $user = $userId ? User::find($userId) : null;

                if ($paymentType === 'order') {
                    $orderId = $meta['order_id'] ?? null;
                    $order = $orderId ? Order::find($orderId) : null;
                    if ($order) {
                        $this->processCompletedOrderPayment($order, 'flutterwave', $transactionId, $verification['data']['amount']);
                        return view('payments.success');
                    } else {
                        Log::warning('Flutterwave Callback: Order not found.', ['tx_ref' => $txRef]);
                        return view('payments.success')->with('warning', 'Payment successful, but order status update failed.');
                    }
                } elseif ($paymentType === 'subscription') {
                    $planSlug = $meta['plan_slug'] ?? null;
                    $plan = $planSlug ? SubscriptionPlan::where('slug', $planSlug)->first() : null;
                    if ($user && $plan) {
                         $this->activateSubscription($user, $plan, 'flutterwave', $transactionId, $verification['data']['amount']);
                         return view('payments.success', ['message' => 'Subscription activated successfully!']);
                    } else {
                         Log::warning('Flutterwave Callback: User or Plan not found for subscription.', ['tx_ref' => $txRef, 'data' => $request->all()]);
                         return view('payments.success')->with('warning', 'Payment successful, but subscription activation failed.');
                    }
                } else {
                     Log::warning('Flutterwave Callback: Unknown payment type in metadata.', ['tx_ref' => $txRef, 'data' => $request->all()]);
                     return view('payments.success')->with('warning', 'Payment successful, but processing failed.');
                }
            } else {
                 Log::error('Flutterwave Callback: Verification failed.', ['verification' => $verification, 'data' => $request->all()]);
                 return view('payments.failed');
            }
        } elseif ($status === 'cancelled') {
            return view('payments.cancelled');
        } else {
             Log::error('Flutterwave Callback: Payment failed or status unknown.', $request->all());
             return view('payments.failed');
        }
    }

    public function handleFlutterwaveWebhook(Request $request)
    {
        Log::info('Flutterwave Webhook Received:', $request->all());
        if (!$this->flutterwaveService->verifyWebhookSignature()) {
             Log::error('Flutterwave Webhook: Invalid signature.');
             return response('Invalid signature', 401);
        }
        $eventData = $request->input('data');
        $eventType = $request->input('event');

        if ($eventType === 'charge.completed' && isset($eventData['status']) && $eventData['status'] === 'successful') {
            $transactionId = $eventData['id'];
            $txRef = $eventData['tx_ref'];
            $meta = $eventData['meta'] ?? [];
            $paymentType = $meta['type'] ?? null;
            $userId = $meta['user_id'] ?? null;
            $user = $userId ? User::find($userId) : null;

            $verification = $this->flutterwaveService->verifyTransaction((string)$transactionId);
            if (!$verification['success'] || $verification['data']['status'] !== 'successful') {
                 Log::error('Flutterwave Webhook: Verification failed.', ['verification' => $verification, 'data' => $eventData]);
                 return response('Verification failed', 400);
            }

            if ($paymentType === 'order') {
                $orderId = $meta['order_id'] ?? null;
                $order = $orderId ? Order::find($orderId) : null;
                if ($order) {
                    $this->processCompletedOrderPayment($order, 'flutterwave', $transactionId, $eventData['amount']);
                } else {
                     Log::warning('Flutterwave Webhook: Order not found.', ['tx_ref' => $txRef]);
                }
            } elseif ($paymentType === 'subscription') {
                 $planSlug = $meta['plan_slug'] ?? null;
                 $plan = $planSlug ? SubscriptionPlan::where('slug', $planSlug)->first() : null;
                 if ($user && $plan) {
                     $this->activateSubscription($user, $plan, 'flutterwave', $transactionId, $eventData['amount']);
                 } else {
                     Log::warning('Flutterwave Webhook: User or Plan not found for subscription.', ['tx_ref' => $txRef, 'data' => $eventData]);
                 }
            } else {
                 Log::warning('Flutterwave Webhook: Unknown payment type in metadata.', ['tx_ref' => $txRef, 'data' => $eventData]);
            }
        } else {
             Log::info('Flutterwave Webhook: Received non-charge.completed or unsuccessful event.', $request->all());
        }
        return response('OK', 200);
    }


    // --- PayPal Methods ---

    public function payWithPayPal(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || $user->id !== $order->user_id || !in_array($order->status, ['pending', 'accepted'])) {
             return redirect()->back()->with('error', 'Order is not valid for payment.');
        }
        try {
            $description = "Payment for Order #{$order->id}";
            $response = $this->paypalService->createOrder(
                $order->total_amount,
                config('paypal.currency', 'USD'),
                route('payment.paypal.success', ['ref' => 'ORDER_'.$order->id]),
                route('payment.paypal.cancel', ['ref' => 'ORDER_'.$order->id]),
                $description
            );

            if ($response['success'] && isset($response['approval_link'])) {
                return redirect()->away($response['approval_link']);
            } else {
                 Log::error('PayPal Initiation Error: ' . ($response['message'] ?? 'Unknown Error'), ['order_id' => $order->id, 'response' => $response]);
                 return redirect()->back()->with('error', 'Could not initiate PayPal payment: ' . ($response['message'] ?? 'Unknown Error'));
            }
        } catch (\Exception $e) {
             Log::error('PayPal Initiation Exception: ' . $e->getMessage(), ['order_id' => $order->id]);
             return redirect()->back()->with('error', 'An error occurred while initiating PayPal payment.');
        }
    }

    public function handlePayPalSuccess(Request $request): View|RedirectResponse
    {
        Log::info('PayPal Success Callback Received:', $request->all());
        $paypalOrderID = $request->input('token');
        $ref = $request->query('ref');
        $type = $request->query('type');

        if (!$paypalOrderID) {
             Log::error('PayPal Success Callback: Missing PayPal Order ID (token).', $request->all());
             return redirect()->route('home')->with('error', 'PayPal payment details missing.');
        }
        try {
            $response = $this->paypalService->captureOrder($paypalOrderID);

            if ($response['success'] && isset($response['data']['status']) && $response['data']['status'] === 'COMPLETED') {
                Log::info('PayPal Capture Success:', $response['data']);
                $capture = $response['data']['purchase_units'][0]['payments']['captures'][0] ?? null;
                $amount = $capture['amount']['value'] ?? null;
                $paypalTransactionId = $capture['id'] ?? $paypalOrderID;

                if ($type === 'subscription') {
                    $planSlug = $request->query('plan_slug');
                    // User might not be logged in on return, need to associate payment with user differently
                    // For simplicity, assume user is logged in. Production needs robust user identification.
                    $user = Auth::user();
                    $plan = $planSlug ? SubscriptionPlan::where('slug', $planSlug)->first() : null;

                    if ($user && $plan) {
                        $this->activateSubscription($user, $plan, 'paypal', $paypalTransactionId, $amount ?? $plan->price);
                        return view('payments.success', ['message' => 'Subscription activated successfully!']);
                    } else {
                        Log::warning('PayPal Success: User or Plan not found for subscription.', ['paypal_order_id' => $paypalOrderID, 'ref' => $ref]);
                        return view('payments.success')->with('warning', 'Payment successful, but subscription activation failed.');
                    }
                } elseif (Str::startsWith($ref, 'ORDER_')) {
                    $orderId = explode('_', $ref)[1] ?? null;
                    $order = $orderId ? Order::find($orderId) : null;
                    if ($order) {
                         $this->processCompletedOrderPayment($order, 'paypal', $paypalTransactionId, $amount ?? $order->total_amount);
                         return view('payments.success');
                    } else {
                         Log::warning('PayPal Success: Order not found.', ['paypal_order_id' => $paypalOrderID, 'ref' => $ref]);
                         return view('payments.success')->with('warning', 'Payment successful, but order status update failed.');
                    }
                } else {
                     Log::warning('PayPal Success: Unknown reference type.', ['paypal_order_id' => $paypalOrderID, 'ref' => $ref]);
                     return view('payments.success')->with('warning', 'Payment successful, but processing failed.');
                }
            } else {
                 Log::error('PayPal Capture Failed:', $response);
                 return view('payments.failed')->with('error', $response['message'] ?? 'PayPal payment capture failed.');
            }
        } catch (\Exception $e) {
             Log::error('PayPal Capture Exception: ' . $e->getMessage(), ['paypal_order_id' => $paypalOrderID]);
             return view('payments.failed')->with('error', 'An error occurred while capturing PayPal payment.');
        }
    }

    public function handlePayPalCancel(Request $request): View
    {
        Log::info('PayPal Cancel Callback Received:', $request->all());
        return view('payments.cancelled');
    }

    // --- Subscription Payment Methods ---

    public function paySubscriptionWithPayFast(SubscriptionPlan $plan)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'provider') {
             return redirect()->route('provider.subscription.index')->with('error', 'Only providers can subscribe.');
        }

        $subscriptionAttemptId = 'SUB-' . $plan->slug . '-' . uniqid();

        try {
            $response = $this->payfastService->purchase([
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'transactionId' => $subscriptionAttemptId,
                'description' => "Subscription: {$plan->name}",
                'returnUrl' => route('payment.payfast.return', ['ref' => $subscriptionAttemptId]), // Pass ref
                'cancelUrl' => route('payment.payfast.cancel', ['ref' => $subscriptionAttemptId]),
                'notifyUrl' => route('payment.payfast.notify'),
                'firstName' => $user->name,
                'email' => $user->email,
                'custom_int1' => $user->id, // Pass user ID
                'custom_str1' => $plan->slug, // Pass plan slug
            ])->send();

            if ($response->isRedirect()) {
                return $response->getRedirectResponse();
            } else {
                Log::error('PayFast Subscription Redirect Error: ' . $response->getMessage(), ['plan_id' => $plan->id, 'user_id' => $user->id]);
                return redirect()->route('provider.subscription.index')->with('error', 'Could not initiate PayFast payment: ' . $response->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('PayFast Subscription Exception: ' . $e->getMessage(), ['plan_id' => $plan->id, 'user_id' => $user->id]);
            return redirect()->route('provider.subscription.index')->with('error', 'An error occurred while initiating subscription payment.');
        }
    }

    public function paySubscriptionWithFlutterwave(SubscriptionPlan $plan)
    {
        $user = Auth::user();
         if (!$user || $user->role !== 'provider') {
             return redirect()->route('provider.subscription.index')->with('error', 'Only providers can subscribe.');
        }
        $txRef = 'SUB-' . $plan->slug . '-' . uniqid();

        $paymentData = [
            'tx_ref' => $txRef,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'redirect_url' => route('payment.flutterwave.callback'),
            'payment_options' => 'card',
            'customer' => [ 'email' => $user->email, 'name' => $user->name ],
            'customizations' => [ 'title' => config('app.name').' Subscription', 'description' => "Subscription to {$plan->name}" ],
             'meta' => [ 'plan_slug' => $plan->slug, 'user_id' => $user->id, 'type' => 'subscription' ]
        ];

        $response = $this->flutterwaveService->initiatePayment($paymentData);

        if ($response['success'] && isset($response['link'])) {
            return redirect()->away($response['link']);
        } else {
            Log::error('Flutterwave Subscription Redirect Error: ' . ($response['message'] ?? 'Unknown Error'), ['plan_id' => $plan->id, 'user_id' => $user->id]);
            return redirect()->route('provider.subscription.index')->with('error', 'Could not initiate Flutterwave payment: ' . ($response['message'] ?? 'Unknown Error'));
        }
    }

    public function paySubscriptionWithPayPal(SubscriptionPlan $plan)
    {
        $user = Auth::user();
         if (!$user || $user->role !== 'provider') {
             return redirect()->route('provider.subscription.index')->with('error', 'Only providers can subscribe.');
        }

        try {
            $description = "Subscription to {$plan->name}";
            $reference = 'SUB-' . $plan->slug;
            $response = $this->paypalService->createOrder(
                $plan->price,
                $plan->currency,
                route('payment.paypal.success', ['ref' => $reference, 'type' => 'subscription', 'plan_slug' => $plan->slug]),
                route('payment.paypal.cancel', ['ref' => $reference]),
                $description
            );

            if ($response['success'] && isset($response['approval_link'])) {
                return redirect()->away($response['approval_link']);
            } else {
                 Log::error('PayPal Subscription Initiation Error: ' . ($response['message'] ?? 'Unknown Error'), ['plan_id' => $plan->id, 'user_id' => $user->id]);
                 return redirect()->route('provider.subscription.index')->with('error', 'Could not initiate PayPal payment: ' . ($response['message'] ?? 'Unknown Error'));
            }
        } catch (\Exception $e) {
             Log::error('PayPal Subscription Initiation Exception: ' . $e->getMessage(), ['plan_id' => $plan->id, 'user_id' => $user->id]);
             return redirect()->route('provider.subscription.index')->with('error', 'An error occurred while initiating PayPal payment.');
        }
    }

    // --- Helper Methods ---

    private function processCompletedOrderPayment(Order $order, string $method, string $reference, $amount): void
    {
        if ($order->status === 'completed') {
            Log::info("Order #{$order->id} already completed. Skipping duplicate processing.");
            return;
        }

        // Use DB Transaction for atomicity
        DB::transaction(function () use ($order, $method, $reference, $amount) {
            $order->status = 'completed';
            $order->save(); // Triggers observer for commission calculation

            // Reload order to get commission amount set by observer
            $order->refresh();

            Transaction::updateOrCreate(
                ['transaction_reference' => $reference, 'payment_method' => $method, 'type' => 'payment'],
                [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'status' => 'completed',
                ]
            );

            // Credit Provider Wallet
            if ($order->provider && !is_null($order->commission_amount)) {
                $providerEarning = $amount - $order->commission_amount;
                if ($providerEarning > 0) {
                    $wallet = $order->provider->wallet()->firstOrCreate(['user_id' => $order->provider_id]);
                    $wallet->increment('balance', $providerEarning);

                    // Create a transaction record for the provider's earning
                    Transaction::create([
                        'user_id' => $order->provider_id, // Belongs to the provider
                        'order_id' => $order->id,
                        'type' => 'earning', // New transaction type
                        'amount' => $providerEarning,
                        'status' => 'completed',
                        'payment_method' => $method,
                        'transaction_reference' => $reference . '_earning', // Make reference unique
                        'description' => "Earning from Order #{$order->id}"
                    ]);
                    Log::info("Credited provider #{$order->provider_id} wallet with {$providerEarning} for order #{$order->id}.");
                } else {
                     Log::info("Provider earning is zero or negative for order #{$order->id}. No wallet credit.");
                }
            } else {
                 Log::warning("Could not credit provider wallet for order #{$order->id}. Provider or commission missing.", [
                    'provider_exists' => !empty($order->provider),
                    'commission_calculated' => !is_null($order->commission_amount)
                 ]);
            }
        });

        Log::info("Order #{$order->id} marked as completed via {$method}.");
    }

    private function activateSubscription(User $user, SubscriptionPlan $plan, string $method, string $reference, $amount): void
    {
        if ($user->subscription_plan === $plan->slug && $user->subscription_status === 'active') {
             Log::info("User #{$user->id} already has active subscription '{$plan->slug}'. Skipping duplicate activation.");
             return;
        }
        $expiresAt = Carbon::now();
        if ($plan->interval === 'month') { $expiresAt->addMonths($plan->interval_count); }
        elseif ($plan->interval === 'year') { $expiresAt->addYears($plan->interval_count); }
        elseif ($plan->interval === 'week') { $expiresAt->addWeeks($plan->interval_count); }
        elseif ($plan->interval === 'day') { $expiresAt->addDays($plan->interval_count); }
        else { $expiresAt->addMonth(); }

        // Use DB Transaction
        DB::transaction(function () use ($user, $plan, $method, $reference, $amount, $expiresAt) {
            $user->update([
                'subscription_plan' => $plan->slug,
                'subscription_status' => 'active',
                'subscription_expires_at' => $expiresAt,
            ]);

            Transaction::updateOrCreate(
                ['transaction_reference' => $reference, 'payment_method' => $method, 'type' => 'subscription'],
                [
                    'user_id' => $user->id,
                    'order_id' => null,
                    'amount' => $amount,
                    'status' => 'completed',
                    'description' => "Subscription payment for plan: {$plan->name}"
                ]
            );
        });

        Log::info("Subscription '{$plan->slug}' activated for user #{$user->id} via {$method}. Expires: {$expiresAt->toDateString()}");
    }
}
