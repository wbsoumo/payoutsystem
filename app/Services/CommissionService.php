<?php

namespace App\Services;

use App\Models\Commission;

class CommissionService
{
    public function calculateCommission(string $merchantId, float $amount): array
    {
        // 1. Resolve Commission rule: Merchant Override first, then Global
        $commissionRule = Commission::where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->where('effective_date', '<=', now()->toDateString())
            ->orderBy('effective_date', 'desc')
            ->first();

        if (!$commissionRule) {
            $commissionRule = Commission::whereNull('merchant_id')
                ->where('is_active', true)
                ->where('effective_date', '<=', now()->toDateString())
                ->orderBy('effective_date', 'desc')
                ->first();
        }

        // If no commission rule exists, default to 0
        if (!$commissionRule) {
            return [
                'commission' => 0.0,
                'gst' => 0.0,
                'total' => 0.0,
                'rule_name' => 'Default (0%)',
            ];
        }

        $calculatedCharge = 0.0;

        switch ($commissionRule->type) {
            case 'fixed':
                $calculatedCharge = (float) $commissionRule->fixed_charge;
                break;

            case 'percentage':
                $calculatedCharge = $amount * ((float) $commissionRule->percentage_charge / 100);
                break;

            case 'slab':
                $slabs = $commissionRule->slab_rates;
                if (is_array($slabs)) {
                    foreach ($slabs as $slab) {
                        $min = (float) ($slab['min'] ?? 0);
                        $max = isset($slab['max']) && $slab['max'] !== '' ? (float) $slab['max'] : INF;

                        if ($amount >= $min && $amount <= $max) {
                            $type = $slab['type'] ?? 'percentage';
                            $val = (float) ($slab['value'] ?? 0);

                            if ($type === 'fixed') {
                                $calculatedCharge = $val;
                            } else {
                                $calculatedCharge = $amount * ($val / 100);
                            }
                            break;
                        }
                    }
                }
                break;
        }

        // Apply Min / Max constraints
        if ($commissionRule->min_charge !== null && $calculatedCharge < (float) $commissionRule->min_charge) {
            $calculatedCharge = (float) $commissionRule->min_charge;
        }

        if ($commissionRule->max_charge !== null && $calculatedCharge > (float) $commissionRule->max_charge) {
            $calculatedCharge = (float) $commissionRule->max_charge;
        }

        // Calculate GST
        $gstRate = (float) $commissionRule->gst_rate;
        $gstAmount = $calculatedCharge * ($gstRate / 100);

        return [
            'commission' => round($calculatedCharge, 4),
            'gst' => round($gstAmount, 4),
            'total' => round($calculatedCharge + $gstAmount, 4),
            'rule_name' => $commissionRule->name,
        ];
    }
}
