<?php
declare(strict_types=1);

namespace App\Domain;

final class LockerCatalog
{
    /**
     * Stały katalog skrytek i cen (cm, PLN)
     * Klucz: kod gabarytu (np. A/B/C)
     */
    public static function lockers(): array
    {
        return [
            'A' => [
                'inside' => ['length' => 64, 'width' => 38, 'height' => 8],   // przykładowe
                'price'  => 12.99,
            ],
            'B' => [
                'inside' => ['length' => 64, 'width' => 38, 'height' => 19],
                'price'  => 14.99,
            ],
            'C' => [
                'inside' => ['length' => 64, 'width' => 38, 'height' => 41],
                'price'  => 17.99,
            ],
        ];
    }
}

