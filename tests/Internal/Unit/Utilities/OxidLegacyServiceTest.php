<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 28.08.17
 * Time: 15:38
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit\utilities;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Utility\OxidLegacyService;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub;


class OxidLegacyServiceTest extends \PHPUnit_Framework_TestCase
{

    private $configMock;

    public function setUp()
    {

        require_once(__DIR__ . '/../../globaltestfunctions.php');

        $currency = new \stdClass();
        $currency->decimal = 2;

        $this->configMock = $this->getMockBuilder(Config::class)->getMock();
        $this->configMock->method('getConfigParam')->willReturn(true);
        $this->configMock->method('getActShopCurrencyObject')->willReturn($currency);

        Registry::set(Config::class, $this->configMock);
    }

    /** @dataProvider priceData */
    public function testBothViewAndDbHaveNetPrices($shownetprice, $enternetprice, $databaseprice)
    {

        $context = new ContextStub();
        $context->setDisplayNetPrices($shownetprice);
        $context->setDbPricesAreNetPrices($enternetprice);
        $legacyService = new OxidLegacyService($this->configMock, $context);

        $price = $legacyService->getPriceObject($databaseprice, 19.0);

        $this->assertEquals(100.0, $price->getNettoPrice());
        $this->assertEquals(119.0, $price->getBruttoPrice());
    }

    public function priceData()
    {
        return [
            ['shownetprice' => true, 'enternetprice' => true, 'databaseprice' => 100.0],
            ['shownetprice' => true, 'enternetprice' => false, 'databaseprice' => 119.0],
            ['shownetprice' => false, 'enternetprice' => true, 'databaseprice' => 100.0],
            ['shownetprice' => false, 'enternetprice' => false, 'databaseprice' => 119.0]
        ];
    }
}