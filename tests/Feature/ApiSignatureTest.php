<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\MerchantApiKey;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiSignatureTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected string $apiKey = 'nvx_pk_live_testapikey123456789012345678';
    protected string $secretKey = 'nvx_sk_live_testsecretkey1234567890123456789012345678';

    protected function setUp(): void
    {
        parent::setUp();

        // Setup mock merchant, wallet and api key
        $this->merchant = Merchant::create([
            'company_name' => 'Test Corp',
            'business_name' => 'TestPay',
            'business_type' => 'pvt_ltd',
            'phone' => '+919876543210',
            'email' => 'test@corp.com',
            'country' => 'India',
            'monthly_volume' => '10l_50l',
            'status' => 'active',
            'kyc_status' => 'approved',
        ]);

        Wallet::create([
            'merchant_id' => $this->merchant->id,
            'balance' => 10000.0000,
            'frozen_balance' => 0.0000,
            'currency' => 'INR',
        ]);

        MerchantApiKey::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Test API Key',
            'api_key_hash' => hash('sha256', $this->apiKey),
            'api_key_preview' => 'nvx_pk_live_test...5678',
            'secret_key_encrypted' => $this->secretKey, // Encryption cast handles automatically
            'webhook_secret_encrypted' => 'whsec_testwebhooksecret1234567890',
            'is_active' => true,
        ]);
    }

    public function test_api_requires_headers()
    {
        $response = $this->getJson('/api/v1/wallet/balance');
        $response->assertStatus(400)
                 ->assertJsonFragment(['success' => false]);
    }

    public function test_api_verifies_valid_signature()
    {
        $timestamp = (string) time();
        $nonce = (string) Str::uuid();
        $body = '[]'; // JSON request default body in tests

        $stringToSign = $timestamp . '.' . $nonce . '.' . $body;
        $signature = hash_hmac('sha256', $stringToSign, $this->secretKey);

        $response = $this->withHeaders([
            'x-api-key' => $this->apiKey,
            'x-signature' => $signature,
            'x-timestamp' => $timestamp,
            'x-nonce' => $nonce,
        ])->getJson('/api/v1/wallet/balance');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'success' => true,
                     'balance' => 10000.0,
                 ]);
    }

    public function test_api_rejects_expired_timestamp()
    {
        $timestamp = (string) (time() - 360); // 6 minutes old
        $nonce = (string) Str::uuid();
        $body = '[]';

        $stringToSign = $timestamp . '.' . $nonce . '.' . $body;
        $signature = hash_hmac('sha256', $stringToSign, $this->secretKey);

        $response = $this->withHeaders([
            'x-api-key' => $this->apiKey,
            'x-signature' => $signature,
            'x-timestamp' => $timestamp,
            'x-nonce' => $nonce,
        ])->getJson('/api/v1/wallet/balance');

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'success' => false,
                     'error' => 'Request expired (timestamp out of window)',
                 ]);
    }

    public function test_api_rejects_replayed_nonce()
    {
        $timestamp = (string) time();
        $nonce = (string) Str::uuid();
        $body = '[]';

        $stringToSign = $timestamp . '.' . $nonce . '.' . $body;
        $signature = hash_hmac('sha256', $stringToSign, $this->secretKey);

        $headers = [
            'x-api-key' => $this->apiKey,
            'x-signature' => $signature,
            'x-timestamp' => $timestamp,
            'x-nonce' => $nonce,
        ];

        // First request succeeds
        $this->withHeaders($headers)->getJson('/api/v1/wallet/balance')->assertStatus(200);

        // Replayed request fails
        $response = $this->withHeaders($headers)->getJson('/api/v1/wallet/balance');
        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'success' => false,
                     'error' => 'Replay attack detected (nonce reused)',
                 ]);
    }
}
