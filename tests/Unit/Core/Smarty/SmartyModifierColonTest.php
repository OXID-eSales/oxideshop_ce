<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/modifier.colon.php';
$oxidEsalesFilePath =  __DIR__ . '/../../../../source/Core/Smarty/Plugin/modifier.colon.php';
$oxVmFilePath = __DIR__ . '/../../../../vendor/oxid-esales/oxideshop-ce/source/Core/Smarty/Plugin/modifier.colon.php';

if (file_exists($filePath)) {
    require_once $filePath;
} else if (file_exists($oxidEsalesFilePath)){
    require_once $oxidEsalesFilePath;
} else {
    require_once $oxVmFilePath;
}

class SmartyModifierColonTest extends \OxidTestCase
{

    /**
     * provides data to testColons
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(':', 'Name:'), // normal colon
            array(' :', 'Name :') // french, for example, has space before colon
        );
    }

    /**
     * Test colon smarty modifier
     *
     * @dataProvider provider
     */
    public function testColons($sTranslation, $sResult)
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("translateString"));
        $oLang->expects($this->any())->method("translateString")->with($this->equalTo('COLON'))->will($this->returnValue($sTranslation));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Language::class, $oLang);

        $this->assertEquals($sResult, smarty_modifier_colon('Name'));
    }
}
