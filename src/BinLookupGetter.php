<?php

namespace Src;

class BinLookupGetter
{
    const string BINLOOKUP_URL = 'https://lookup.binlist.net/';

    public function getBinData(string $bin): ?object
    {
        return json_decode(file_get_contents(self::BINLOOKUP_URL . $bin));
    }
}