<?php

namespace App\Services;

class CommissionService
{
    /**
     * Returns [commission_amount, vendor_amount]
     */
    public function calculate(float $lineTotal, float $ratePercent): array
    {
        $commissionAmount = round($lineTotal * ($ratePercent / 100), 2);
        $vendorAmount     = round($lineTotal - $commissionAmount, 2);
        return [$commissionAmount, $vendorAmount];
    }
}
