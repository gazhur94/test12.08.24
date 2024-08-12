<?php

use PHPUnit\Framework\TestCase;
use Src\CommissionsCalculator;
use Src\RatesGetter;
use Src\BinLookupGetter;

class CommissionsCalculatorTest extends TestCase
{
    private RatesGetter $ratesGetter;
    private BinLookupGetter $binLookupGetter;
    private CommissionsCalculator $calculator;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->ratesGetter = $this->createMock(RatesGetter::class);
        $this->binLookupGetter = $this->createMock(BinLookupGetter::class);
        $this->calculator = new CommissionsCalculator($this->ratesGetter, $this->binLookupGetter);
    }

    function testFileCorrectlyForEuCountry()
    {
        $this->ratesGetter->method('getRates')->willReturn(['rates' => ['USD' => 1.2]]);
        $this->binLookupGetter->method('getBinData')->willReturn((object)['country' => (object)['alpha2' => 'DE']]);

        $content = '{"bin":"45717360","amount":"100.00","currency":"USD"}';
        $result = $this->calculator->processFile($content);

        $this->assertEquals([0.83], $result);
    }

    function testFileCorrectlyForNonEuCountry()
    {
        $this->ratesGetter->method('getRates')->willReturn(['rates' => ['USD' => 1.2]]);
        $this->binLookupGetter->method('getBinData')->willReturn((object)['country' => (object)['alpha2' => 'US']]);

        $content = '{"bin":"45717360","amount":"100.00","currency":"USD"}';
        $result = $this->calculator->processFile($content);

        $this->assertEquals([1.67], $result);
    }

    function testInvalidBinDataGracefully()
    {
        $this->ratesGetter->method('getRates')->willReturn(['rates' => ['USD' => 1.2]]);
        $this->binLookupGetter->method('getBinData')->willReturn(null);

        $content = '{"bin":"45717360","amount":"100.00","currency":"USD"}';
        $result = $this->calculator->processFile($content);

        $this->assertEquals(['Error: Invalid bin or something went wrong with bin lookup'], $result);
    }

    function testEmptyContentGracefully()
    {
        $this->ratesGetter->method('getRates')->willReturn(['rates' => ['USD' => 1.2]]);
        $this->binLookupGetter->method('getBinData')->willReturn((object)['country' => (object)['alpha2' => 'DE']]);

        $content = '';
        $result = $this->calculator->processFile($content);

        $this->assertEquals([], $result);
    }

    function testMissingCurrencyRateGracefully()
    {
        $this->ratesGetter->method('getRates')->willReturn(['rates' => []]);
        $this->binLookupGetter->method('getBinData')->willReturn((object)['country' => (object)['alpha2' => 'DE']]);

        $content = '{"bin":"45717360","amount":"100.00","currency":"USD"}';
        $result = $this->calculator->processFile($content);

        $this->assertEquals([1.0], $result);
    }
}