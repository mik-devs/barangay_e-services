<?php

namespace App\Services;

class FeeCalculatorService
{
    /**
     
     */
    public function calculateDocumentFee(string $documentType): float
    {
        return match (strtolower(trim($documentType))) {
            'barangay clearance' => 100.00,
            'certificate of residency' => 50.00,
            'certificate of indigency' => 50.00, 
            'business clearance' => 500.00,
            default => 75.00, // Standard default fee para sa mga documents
        };
    }

    /**
     * calculate anf mga bayad 
     */
    public function calculateBookingFee(string $facilityType, int $hours, float $additionalCharges = 0.00): float
    {
        $baseRatePerHour = match (strtolower(trim($facilityType))) {
            'covered court' => 300.00,
            'barangay hall conference room' => 500.00,
            default => 150.00,
        };

        $calculatedTotal = ($baseRatePerHour * max(1, $hours)) + $additionalCharges;

        return (float) $calculatedTotal;
    }
}