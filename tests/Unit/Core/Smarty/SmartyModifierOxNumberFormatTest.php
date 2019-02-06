<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/modifier.oxnumberformat.php';
$oxidEsalesFilePath =  __DIR__ . '/../../../../source/Core/Smarty/Plugin/modifier.oxnumberformat.php';
$oxVmFilePath = __DIR__ . '/../../../../vendor/oxid-esales/oxideshop-ce/source/Core/Smarty/Plugin/modifier.oxnumberformat.php';

if (file_exists($filePath)) {
    require_once $filePath;
} else if (file_exists($oxidEsalesFilePath)){
    require_once $oxidEsalesFilePath;
} else {
    require_once $oxVmFilePath;
}

class SmartyModifierOxNumberFormatTest extends \OxidTestCase
{

    /**
     * Provides number format, number and expected value
     */
    public function Provider()
    {
        return array(
            array("EUR@ 1.00@ ,@ .@ EUR@ 2", 25000, '25.000,00'),
            array("EUR@ 1.00@ ,@ .@ EUR@ 2", 25000.1584, '25.000,16'),
            array("EUR@ 1.00@ ,@ .@ EUR@ 3", 25000.1584, '25.000,158'),
            array("EUR@ 1.00@ ,@ .@ EUR@ 0", 25000000.5584, '25.000.001'),
            array("EUR@ 1.00@ .@ ,@ EUR@ 2", 25000000.5584, '25,000,000.56'),
        );
    }

    /**
     * Tests how oxnumberformat modifier works
     *
     * @dataProvider Provider
     */
    public function testNumberFormatDefaultFormat($sFormat, $mValue, $sExpected)
    {
        $this->assertEquals($sExpected, smarty_modifier_oxnumberformat($sFormat, $mValue));
    }

}
