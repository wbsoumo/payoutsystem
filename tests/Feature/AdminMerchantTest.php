<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Merchant;
use App\Models\MerchantUser;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMerchantTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard Super Admin user for testing
        $this->admin = Admin::create([
            'name' => 'Super Administrator',
            'email' => 'super_admin@novexapay.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_merchant_creation_form()
    {
        $response = $this->get(route('admin.merchants.create'));
        $response->assertRedirect(); // Should redirect to login
    }

    public function test_authenticated_admin_can_load_merchant_creation_form()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.merchants.create'));
        $response->assertStatus(200)
                 ->assertViewIs('admin.merchants.create')
                 ->assertSee('Provision Merchant Entity');
    }

    public function test_admin_can_create_merchant_account_successfully()
    {
        $merchantData = [
            'company_name' => 'Wayne Enterprises Ltd',
            'business_name' => 'Wayne Tech',
            'business_type' => 'private_limited',
            'email' => 'finance@waynecorp.com',
            'phone' => '+919999999999',
            'website' => 'https://waynecorp.com',
            'user_name' => 'Bruce Wayne',
            'user_email' => 'bruce@waynecorp.com',
            'user_password' => 'batman1234',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.merchants.store'), $merchantData);

        // Verify redirect to the detail view of the newly created merchant
        $merchant = Merchant::where('email', 'finance@waynecorp.com')->first();
        $this->assertNotNull($merchant);

        $response->assertRedirect(route('admin.merchants.view', $merchant->id))
                 ->assertSessionHas('success', 'Merchant account created successfully.');

        // Verify database records
        $this->assertDatabaseHas('merchants', [
            'id' => $merchant->id,
            'company_name' => 'Wayne Enterprises Ltd',
            'status' => 'active',
            'kyc_status' => 'approved',
        ]);

        $this->assertDatabaseHas('merchant_profiles', [
            'merchant_id' => $merchant->id,
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 0.0000,
        ]);

        $this->assertDatabaseHas('merchant_users', [
            'merchant_id' => $merchant->id,
            'email' => 'bruce@waynecorp.com',
            'status' => 'active',
        ]);

        // Verify password hash
        $user = MerchantUser::where('email', 'bruce@waynecorp.com')->first();
        $this->assertTrue(Hash::check('batman1234', $user->password));
    }

    public function test_merchant_creation_validation_fails_on_duplicate_email()
    {
        // Pre-create a merchant with the target email
        Merchant::create([
            'company_name' => 'Existing Company',
            'business_name' => 'Existing Brand',
            'business_type' => 'other',
            'email' => 'finance@waynecorp.com',
            'phone' => '+919999999999',
            'country' => 'IN',
            'monthly_volume' => '0',
            'status' => 'active',
            'kyc_status' => 'approved',
        ]);

        $merchantData = [
            'company_name' => 'Wayne Enterprises Ltd',
            'business_name' => 'Wayne Tech',
            'business_type' => 'private_limited',
            'email' => 'finance@waynecorp.com', // Duplicate corporate email
            'phone' => '+919999999999',
            'website' => 'https://waynecorp.com',
            'user_name' => 'Bruce Wayne',
            'user_email' => 'bruce@waynecorp.com',
            'user_password' => 'batman1234',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.merchants.create'))
            ->post(route('admin.merchants.store'), $merchantData);

        $response->assertRedirect(route('admin.merchants.create'));
        $response->assertSessionHasErrors('email');
    }
}
