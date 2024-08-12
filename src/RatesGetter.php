<?php

namespace Src;

use Exception;

class RatesGetter
{
    const string EXCHANGE_URL = 'https://api.exchangeratesapi.io/latest';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!isset($_ENV['EXCHANGE_ACCESS_KEY'])) {
            throw new Exception('EXCHANGE_ACCESS_KEY is not set');
        }
    }

    public function getRates(): array
    {
        return json_decode(
            file_get_contents(self::EXCHANGE_URL . '?access_key=' . $_ENV['EXCHANGE_ACCESS_KEY']),
            true
        );
    }
}