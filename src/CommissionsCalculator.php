<?php

namespace Src;

use Exception;

class CommissionsCalculator
{
    const array EU_COUNTRIES = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];

    const float EU_COEFFICIENT = 0.01;

    const float NON_EU_COEFFICIENT = 0.02;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly RatesGetter $ratesGetter,
        private readonly BinLookupGetter $binLookupGetter
    ) {
    }

    public function processFile(string $content): array
    {
        $rates = $this->ratesGetter->getRates();

        $result = [];

        foreach (explode("\n", $content) as $row) {
            if (empty($row)) break;

            list($bin, $amount, $currency) = array_map(
                fn($col) => trim(explode(':', $col)[1], '"}'), explode(",", $row)
            );

            $binData = $this->binLookupGetter->getBinData($bin);

            if (!$binData) {
                $result[] = 'Error: Invalid bin or something went wrong with bin lookup';
                continue;
            }

            $coefficient = in_array($binData->country->alpha2, self::EU_COUNTRIES)
                ? self::EU_COEFFICIENT
                : self::NON_EU_COEFFICIENT;

            $rate = $rates['rates'][$currency] ?? 0;

            $result[] = round(($rate ? $amount / $rate : $amount) * $coefficient, 2);
        }

        return $result;
    }
}