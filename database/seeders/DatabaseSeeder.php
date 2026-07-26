<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Commission;
use App\Models\ContactRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create permissions
        $permissions = [
            'manage_merchants' => 'Manage Merchants Directory',
            'manage_wallets' => 'Wallet Adjustments & Auditing',
            'manage_commissions' => 'Commission Engine Settings',
            'view_audit_logs' => 'View System Security Logs',
            'view_api_logs' => 'View API Traffic Logs',
            'manage_support_tickets' => 'Manage Support Desk',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $displayName) {
            $permissionModels[$name] = Permission::create([
                'name' => $name,
                'display_name' => $displayName,
            ]);
        }

        // 2. Create roles
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
        ]);
        $superAdminRole->permissions()->attach(array_values(collect($permissionModels)->pluck('id')->toArray()));

        $supportRole = Role::create([
            'name' => 'support_agent',
            'display_name' => 'Support Desk Agent',
        ]);
        $supportRole->permissions()->attach([
            $permissionModels['manage_support_tickets']->id,
            $permissionModels['view_api_logs']->id,
        ]);

        // 3. Create administrator accounts
        $admin = Admin::create([
            'name' => 'Super Admin User',
            'email' => 'admin@novexapay.com',
            'password' => Hash::make('admin123'), // Master Password
            'role' => 'super_admin',
            'status' => 'active',
        ]);
        $admin->roles()->attach($superAdminRole->id);

        $supportStaff = Admin::create([
            'name' => 'Support Staff User',
            'email' => 'support@novexapay.com',
            'password' => Hash::make('support123'),
            'role' => 'support_admin',
            'status' => 'active',
        ]);
        $supportStaff->roles()->attach($supportRole->id);

        // 4. Create default global commission rate
        Commission::create([
            'name' => 'Standard Global Commission Rate',
            'merchant_id' => null, // Scope: Global
            'type' => 'percentage',
            'fixed_charge' => 0.0000,
            'percentage_charge' => 1.80, // 1.8%
            'min_charge' => 5.0000, // min ₹5 charge
            'max_charge' => 25.0000, // max ₹25 charge
            'gst_rate' => 18.00, // 18% GST
            'effective_date' => '2026-01-01',
            'is_active' => true,
        ]);

        // 5. Create some sample access requests
        ContactRequest::create([
            'company_name' => 'Stark Industries Ltd',
            'business_name' => 'StarkPay',
            'full_name' => 'Tony Stark',
            'email' => 'tony@stark.com',
            'phone' => '+919988776655',
            'country' => 'India',
            'monthly_volume' => 'above_2cr',
            'business_type' => 'pvt_ltd',
            'website' => 'https://stark.com',
            'message' => 'Need high volume payouts API to disburse vendor invoices.',
            'status' => 'pending',
        ]);

        ContactRequest::create([
            'company_name' => 'Wayne Enterprises Inc',
            'business_name' => 'Wayne Solutions',
            'full_name' => 'Bruce Wayne',
            'email' => 'bruce@wayne.com',
            'phone' => '+918877665544',
            'country' => 'India',
            'monthly_volume' => '50l_2cr',
            'business_type' => 'pvt_ltd',
            'website' => 'https://wayne.com',
            'message' => 'Looking for secure corporate wallet ledger mapping.',
            'status' => 'pending',
        ]);
    }
}
