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
                 ->assertJsonFragment([
                     'success' => false,
                     'error' => 'Missing security headers (x-api-key, x-api-secret, x-merchant-id required)'
                 ]);
    }

    public function test_api_verifies_valid_credentials()
    {
        $response = $this->withHeaders([
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->secretKey,
            'x-merchant-id' => $this->merchant->id,
        ])->getJson('/api/v1/wallet/balance');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'success' => true,
                     'balance' => 10000.0,
                     'deposit_upi_id' => 'novexapay@yesbank',
                 ]);
    }

    public function test_api_rejects_invalid_secret()
    {
        $response = $this->withHeaders([
            'x-api-key' => $this->apiKey,
            'x-api-secret' => 'invalid_secret_key_123',
            'x-merchant-id' => $this->merchant->id,
        ])->getJson('/api/v1/wallet/balance');

        $response->assertStatus(401)
                 ->assertJsonFragment([
                     'success' => false,
                     'error' => 'Invalid API Secret Key',
                 ]);
    }

    public function test_api_rejects_unmatched_merchant_id()
    {
        $response = $this->withHeaders([
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->secretKey,
            'x-merchant-id' => '019f9dc9-a3aa-7076-b865-1f4ca42e790d', // Unmatched ID
        ])->getJson('/api/v1/wallet/balance');

        $response->assertStatus(401)
                 ->assertJsonFragment([
                     'success' => false,
                     'error' => 'API Key does not match the provided Merchant ID',
                 ]);
    }

    public function test_beneficiaries_crud()
    {
        $headers = [
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->secretKey,
            'x-merchant-id' => $this->merchant->id,
        ];

        // 1. Create a beneficiary
        $createResponse = $this->withHeaders($headers)->postJson('/api/v1/beneficiaries', [
            'name' => 'Soumojit Saha',
            'bank_name' => 'State Bank of India',
            'account_number' => '999988887777',
            'ifsc' => 'SBIN0003242',
            'logo_url' => 'https://taskbazi.xyz/logo/sbi.co.in'
        ]);

        $createResponse->assertStatus(200)
                       ->assertJsonFragment([
                           'success' => true,
                           'message' => 'Beneficiary saved successfully.'
                       ]);

        $this->assertDatabaseHas('merchant_beneficiaries', [
            'merchant_id' => $this->merchant->id,
            'name' => 'Soumojit Saha',
            'account_number' => '999988887777'
        ]);

        // 2. Fetch beneficiaries
        $getResponse = $this->withHeaders($headers)->getJson('/api/v1/beneficiaries');
        $getResponse->assertStatus(200)
                    ->assertJsonCount(1, 'beneficiaries')
                    ->assertJsonFragment([
                        'name' => 'Soumojit Saha',
                        'account' => '999988887777'
                    ]);

        $beneficiaryId = $getResponse->json('beneficiaries.0.id');

        // 3. Delete beneficiary
        $deleteResponse = $this->withHeaders($headers)->deleteJson("/api/v1/beneficiaries/{$beneficiaryId}");
        $deleteResponse->assertStatus(200)
                       ->assertJsonFragment([
                           'success' => true,
                           'message' => 'Beneficiary deleted successfully.'
                       ]);

        $this->assertDatabaseMissing('merchant_beneficiaries', [
            'id' => $beneficiaryId
        ]);
    }

    public function test_get_notifications()
    {
        \App\Models\AuditLog::create([
            'user_type' => 'merchant',
            'user_id' => $this->merchant->id,
            'merchant_id' => $this->merchant->id,
            'action' => 'security_login',
            'description' => 'Merchant logged in successfully.',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->withHeaders([
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->secretKey,
            'x-merchant-id' => $this->merchant->id,
        ])->getJson('/api/v1/notifications');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'success' => true,
                 ])
                 ->assertJsonCount(1, 'notifications');
    }

    public function test_profile_endpoints()
    {
        $user = \App\Models\MerchantUser::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Soumojit Saha',
            'email' => 'soumojit@novexapay.com',
            'password' => \Illuminate\Support\Facades\Hash::make('secret123'),
        ]);

        $profile = \App\Models\MerchantProfile::create([
            'merchant_id' => $this->merchant->id,
            'gst' => '27AAACS1234A1Z1',
            'pan' => 'AAACS1234A',
            'bank_name' => 'State Bank of India',
            'bank_account_number' => '1234567890',
            'bank_ifsc' => 'SBIN0001020',
            'bank_holder_name' => 'Soumojit Saha',
        ]);

        $headers = [
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->secretKey,
            'x-merchant-id' => $this->merchant->id,
        ];

        // 1. Get profile details
        $getResponse = $this->withHeaders($headers)->getJson('/api/v1/profile');
        $getResponse->assertStatus(200)
                    ->assertJsonFragment([
                        'success' => true,
                        'name' => 'Soumojit Saha',
                    ]);

        // 2. Update profile details
        $updateResponse = $this->withHeaders($headers)->postJson('/api/v1/profile/update', [
            'name' => 'Soumojit Edited',
            'email' => 'edited@novexapay.com',
            'phone' => '+919999888877',
        ]);
        $updateResponse->assertStatus(200)
                       ->assertJsonFragment([
                           'success' => true,
                           'message' => 'Profile updated successfully.'
                       ]);

        // 3. Update password
        $passwordResponse = $this->withHeaders($headers)->postJson('/api/v1/profile/password', [
            'current_password' => 'secret123',
            'new_password' => 'newsecret123',
        ]);
        $passwordResponse->assertStatus(200)
                         ->assertJsonFragment([
                             'success' => true,
                             'message' => 'Password changed successfully.'
                         ]);

        // 4. Update notifications
        $notifResponse = $this->withHeaders($headers)->postJson('/api/v1/profile/notifications', [
            'email' => false,
            'push' => true,
            'sms' => true,
        ]);
        $notifResponse->assertStatus(200)
                      ->assertJsonFragment([
                          'success' => true,
                          'message' => 'Notification preferences updated successfully.'
                      ]);
    }

    public function test_auth_login()
    {
        $user = \App\Models\MerchantUser::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Soumojit Saha',
            'email' => 'login_test@novexapay.com',
            'password' => \Illuminate\Support\Facades\Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login_test@novexapay.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'success' => true,
                     'merchant_id' => $this->merchant->id,
                 ]);

        $this->assertDatabaseHas('merchant_api_keys', [
            'merchant_id' => $this->merchant->id,
            'name' => 'Mobile Session Key',
        ]);
    }
}
