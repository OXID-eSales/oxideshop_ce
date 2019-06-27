<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \oxField;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \Smarty;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxcontent.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/function.oxcontent.php';
}

class PluginSmartyOxContentTest extends \OxidTestCase
{
    public function testGetContentWhenShopIsNotProductiveAndContentDoesNotExist()
    {
        oxTestModules::addFunction("oxconfig", "getActiveShop", "{ \$oShop = oxNew('oxShop');; \$oShop->oxshops__oxproductive = new oxField();  return \$oShop;}");

        $aParams['ident'] = 'testident';
        $oSmarty = new Smarty();

        $sText = "<b>content not found ! check ident(" . $aParams['ident'] . ") !</b>";

        $this->assertEquals($sText, smarty_function_oxcontent($aParams, $oSmarty));
        //$this->assertEquals( $sText, $oSmarty->oxidcache );
    }

    public function testGetContentNoParamsPassedShopIsProductive()
    {
        $this->assertEquals("<b>content not found ! check ident() !</b>", smarty_function_oxcontent(array(), new stdClass()));
    }

    public function testGetContentLoadByIdent()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $aParams['ident'] = 'oxsecurityinfo';
        $oSmarty = $this->getMock("Smarty", array("fetch"));
        $oSmarty->expects($this->once())->method('fetch')
            ->with($this->equalTo('ox:oxsecurityinfooxcontent0' . $sShopId))
            ->will($this->returnValue('testvalue'));

        $message = "Content not found! check ident(" . $aParams['ident'] . ") !";

        $this->assertEquals('testvalue', smarty_function_oxcontent($aParams, $oSmarty), $message);
    }

    public function testGetContentLoadByIdentLangChange()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $aParams['ident'] = 'oxsecurityinfo';
        $oSmarty = $this->getMock("smarty", array("fetch"));
        $oSmarty->expects($this->once())->method('fetch')
            ->with($this->equalTo('ox:oxsecurityinfooxcontent1' . $sShopId))
            ->will($this->returnValue('testvalue'));

        $message = "Content not found! check ident(" . $aParams['ident'] . ") !";

        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');

        $this->assertEquals('testvalue', smarty_function_oxcontent($aParams, $oSmarty), $message);
    }

    public function testGetContentLoadByOxId()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;
        $aParams['oxid'] = 'f41427a099a603773.44301043';
        $aParams['assign'] = true;

        $oSmarty = $this->getMock("smarty", array("fetch", "assign"));
        $oSmarty->expects($this->once())->method('fetch')->with($this->equalTo('ox:f41427a099a603773.44301043oxcontent0' . $sShopId))->will($this->returnValue('testvalue'));
        $oSmarty->expects($this->once())->method('assign')->with($this->equalTo(true));

        smarty_function_oxcontent($aParams, $oSmarty);
    }
}
