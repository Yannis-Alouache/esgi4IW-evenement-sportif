<?php

namespace App\Service;

class DistanceCalculator
{
    private const EARTH_RADIUS = 6371;

    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = (sin($deltaLat / 2) * sin($deltaLat / 2)) +
            cos($lat1) * cos($lat2) *
            (sin($deltaLon / 2) * sin($deltaLon / 2));
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS * $c;
    }
} 