<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JioPayService
{
    /**
     * Send payout request to Jiopay production gateway
     */
    public function transfer(array $payoutDetails): array
    {
        $mid = Setting::get('jiopay_mid', 'YOUR_BHARAT_MID');
        $key = Setting::get('jiopay_key', 'YOUR_BHARAT_KEY');
        $entityId = Setting::get('jiopay_entity_id', '3173ad0e-xxxx-xxxxxx-9c57830b2d07');
        $customerId = Setting::get('jiopay_customer_id', 'CUST10001');

        $payload = [
            'bharat_mid' => $mid,
            'bharat_key' => $key,
            'entityId' => $entityId,
            'customerId' => $customerId,
            'order_id' => $payoutDetails['order_id'],
            'beneficiary_name' => $payoutDetails['beneficiary_name'],
            'account_number' => $payoutDetails['account_number'],
            'ifsc' => $payoutDetails['ifsc'],
            'amount' => (string) $payoutDetails['amount'],
        ];

        try {
            Log::info("Dispatching payout request to Jiopay upstream: {$payload['order_id']}");

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.bharat4upe.com/api/payout/jiopay_route/transfer', $payload);

            if ($response->failed()) {
                Log::error("Jiopay gateway HTTP error: Code " . $response->status() . " - " . $response->body());
                return [
                    'status' => 'failed',
                    'message' => 'Upstream connection error',
                    'response' => $response->json() ?? ['raw' => $response->body()],
                ];
            }

            $data = $response->json();
            Log::info("Jiopay response payload: " . json_encode($data));

            if (isset($data['status']) && $data['status'] === true) {
                return [
                    'status' => 'success',
                    'provider_reference_id' => $data['data']['provider_txn_id'] ?? 'mock_prov_' . time(),
                    'message' => $data['msg'] ?? 'Payout Initiated',
                    'response' => $data,
                ];
            }

            return [
                'status' => 'failed',
                'message' => $data['msg'] ?? 'Declined by upstream provider',
                'response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error("Jiopay transfer exception occurred: " . $e->getMessage());
            return [
                'status' => 'failed',
                'message' => $e->getMessage(),
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }
}
