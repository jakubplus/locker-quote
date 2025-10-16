<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\LockerCatalog;

final class LockerQuoteService
{
    /**
     * Zwraca dopasowany gabaryt i cenę lub propozycję kuriera.
     *
     * @param float $length cm
     * @param float $width cm
     * @param float $height cm
     * @return array{fits: bool, locker?: array, courier?: array}
     */
    public function quote(float $length, float $width, float $height): array
    {
        $pkg = $this->sorted([$length, $width, $height]);

        $best = null;
        foreach (LockerCatalog::lockers() as $code => $def) {
            $box = $this->sorted([
                $def['inside']['length'],
                $def['inside']['width'],
                $def['inside']['height'],
            ]);

            // dopasowanie z rotacją: sortujemy oba zestawy i porównujemy
            if ($pkg[0] <= $box[0] && $pkg[1] <= $box[1] && $pkg[2] <= $box[2]) {
                // wybierz najmniejszy pasujący (po objętości)
                $volume = $box[0] * $box[1] * $box[2];
                if ($best === null || $volume < $best['volume']) {
                    $best = [
                        'code' => $code,
                        'inside' => $def['inside'],
                        'price' => $def['price'],
                        'volume' => $volume,
                    ];
                }
            }
        }

        if ($best !== null) {
            unset($best['volume']);
            return ['fits' => true, 'locker' => $best];
        }

        // nie mieści się — zaproponuj kuriera wg prostych reguł
        $sum = $length + $width + $height;
        $maxDim = max($length, $width, $height);

        $courier = [
            'name' => 'Kurier standard',
            'reason' => 'Paczka nie mieści się do żadnej skrytki.',
            'suggestions' => [],
        ];

        // przykładowe proste heurystyki
        if ($maxDim <= 120 && $sum <= 200) {
            $courier['name'] = 'Kurier – paczka standard (do 120 cm najdłuższy bok, suma ≤ 200 cm)';
            $courier['price_estimated'] = 24.99;
        } elseif ($maxDim <= 150 && $sum <= 300) {
            $courier['name'] = 'Kurier – paczka niestandard';
            $courier['price_estimated'] = 39.99;
        } else {
            $courier['name'] = 'Kurier gabarytowy';
            $courier['price_estimated'] = 79.99;
            $courier['suggestions'][] = 'Rozważ podział przesyłki na mniejsze paczki.';
        }

        return ['fits' => false, 'courier' => $courier];
    }

    /** @param array<int,float> $dims */
    private function sorted(array $dims): array
    {
        sort($dims, SORT_NUMERIC);
        return $dims;
    }
}
