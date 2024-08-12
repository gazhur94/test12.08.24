<?php

use Src\CommissionsCalculator;
use Src\RatesGetter;
use Src\BinLookupGetter;
use Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';
error_reporting(E_ERROR | E_PARSE);

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $calculator = new CommissionsCalculator(new RatesGetter(), new BinLookupGetter());
    $result = $calculator->processFile(file_get_contents($argv[1]));

    foreach ($result as $row) {
        echo $row . PHP_EOL;
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
