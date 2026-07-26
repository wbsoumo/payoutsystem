<?php

namespace App\Services;

use App\Models\ContactRequest;
use App\Models\Merchant;
use App\Models\MerchantProfile;
use App\Models\MerchantUser;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MerchantService
{
    protected WalletService $walletService;
    protected AuditLogService $auditLogService;

    public function __construct(WalletService $walletService, AuditLogService $auditLogService)
    {
        $this->walletService = $walletService;
        $this->auditLogService = $auditLogService;
    }

    public function createMerchantFromEnquiry(string $enquiryId, ?string $adminId = null): ?Merchant
    {
        return DB::transaction(function () use ($enquiryId, $adminId) {
            $enquiry = ContactRequest::find($enquiryId);
            if (!$enquiry || $enquiry->status !== 'pending') {
                return null;
            }

            // 1. Create Merchant
            $merchant = Merchant::create([
                'company_name' => $enquiry->company_name,
                'business_name' => $enquiry->business_name,
                'business_type' => $enquiry->business_type,
                'website' => $enquiry->website,
                'phone' => $enquiry->phone,
                'email' => $enquiry->email,
                'country' => $enquiry->country,
                'monthly_volume' => $enquiry->monthly_volume,
                'status' => 'pending', // Requires KYC submission
                'kyc_status' => 'pending',
            ]);

            // 2. Create Profile
            MerchantProfile::create([
                'merchant_id' => $merchant->id,
                'address_line1' => $enquiry->country,
            ]);

            // 3. Create Wallet
            Wallet::create([
                'merchant_id' => $merchant->id,
                'balance' => 0.0000,
                'frozen_balance' => 0.0000,
                'currency' => 'INR',
            ]);

            // 4. Create Merchant User
            $tempPassword = Str::random(12);
            $merchantUser = MerchantUser::create([
                'merchant_id' => $merchant->id,
                'name' => $enquiry->full_name,
                'email' => $enquiry->email,
                'password' => Hash::make($tempPassword),
                'status' => 'active',
            ]);

            // 5. Update Enquiry Status
            $enquiry->update([
                'status' => 'converted',
                'converted_merchant_id' => $merchant->id,
            ]);

            // Log Admin activity
            $this->auditLogService->log(
                'admin',
                $adminId,
                $merchant->id,
                'merchant_converted',
                "Converted contact request {$enquiryId} to merchant account. Temp Password: {$tempPassword}",
                ['merchant_id' => $merchant->id, 'email' => $enquiry->email]
            );

            // In production, we'd trigger a welcome email containing their credentials and password reset link.
            // For now, we'll store this in logs or handle it.

            return $merchant;
        });
    }

    public function createMerchantDirectly(array $data, ?string $adminId = null): Merchant
    {
        return DB::transaction(function () use ($data, $adminId) {
            // 1. Create Merchant
            $merchant = Merchant::create([
                'company_name' => $data['company_name'],
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'] ?? 'other',
                'website' => $data['website'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'],
                'country' => $data['country'] ?? 'IN',
                'monthly_volume' => $data['monthly_volume'] ?? '0',
                'status' => 'active', 
                'kyc_status' => 'approved', 
            ]);

            // 2. Create Profile
            MerchantProfile::create([
                'merchant_id' => $merchant->id,
                'address_line1' => $data['country'] ?? 'IN',
            ]);

            // 3. Create Wallet
            Wallet::create([
                'merchant_id' => $merchant->id,
                'balance' => 0.0000,
                'frozen_balance' => 0.0000,
                'currency' => 'INR',
            ]);

            // 4. Create Merchant User
            $merchantUser = MerchantUser::create([
                'merchant_id' => $merchant->id,
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'password' => Hash::make($data['user_password']),
                'status' => 'active',
            ]);

            // Log Admin activity
            $this->auditLogService->log(
                'admin',
                $adminId,
                $merchant->id,
                'merchant_created_directly',
                "Created merchant account directly. User: {$data['user_email']}",
                ['merchant_id' => $merchant->id, 'email' => $data['email']]
            );

            return $merchant;
        });
    }
}
