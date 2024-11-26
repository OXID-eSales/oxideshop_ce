<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidTestCase;
use oxTestModules;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\Test\TestLogger;
use Smarty;

$filePath = Registry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxcontent.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once __DIR__ . '/../../../../source/Core/Smarty/Plugin/function.oxcontent.php';
}

final class PluginSmartyOxContentTest extends OxidTestCase
{
    public function testGetContentWhenShopIsNotProductiveAndContentDoesNotExist(): void
    {
        oxTestModules::addFunction(
            "oxconfig",
            "getActiveShop",
            "{ \$oShop = oxNew('oxShop');; \$oShop->oxshops__oxproductive = new oxField();  return \$oShop;}"
        );

        $aParams['ident'] = 'testident';
        $oSmarty = new Smarty();

        $sText = "<b>content not found ! check ident(" . $aParams['ident'] . ") !</b>";

        $this->assertEquals($sText, smarty_function_oxcontent($aParams, $oSmarty));
    }

    public function testGetContentNoParamsPassedShopIsProductive(): void
    {
        $smarty = $this->createMock(Smarty::class);
        $this->assertEquals(
            "<b>content not found ! check ident(undefined) !</b>",
            smarty_function_oxcontent(array(), $smarty)
        );
    }

    public function testGetContentLoadByIdent(): void
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $aParams['ident'] = 'oxsecurityinfo';
        $oSmarty = $this->getMock("Smarty", array("fetch"));
        $oSmarty->expects($this->once())->method('fetch')
            ->with($this->equalTo('ox:oxsecurityinfooxcontent0' . $sShopId))
            ->willReturn('testvalue');

        $message = "Content not found! check ident(" . $aParams['ident'] . ") !";

        $this->assertEquals('testvalue', smarty_function_oxcontent($aParams, $oSmarty), $message);
    }

    public function testGetContentLoadByIdentLangChange(): void
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $aParams['ident'] = 'oxsecurityinfo';
        $oSmarty = $this->getMock("smarty", array("fetch"));
        $oSmarty->expects($this->once())->method('fetch')
            ->with($this->equalTo('ox:oxsecurityinfooxcontent1' . $sShopId))
            ->willReturn('testvalue');

        $message = "Content not found! check ident(" . $aParams['ident'] . ") !";

        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');

        $this->assertEquals('testvalue', smarty_function_oxcontent($aParams, $oSmarty), $message);
    }

    public function testGetContentLoadByOxId(): void
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;
        $aParams['oxid'] = 'f41427a099a603773.44301043';
        $aParams['assign'] = true;

        /** @var MockObject|Smarty $oSmarty */
        $oSmarty = $this->createPartialMock("smarty", ['fetch', 'assign']);
        $oSmarty->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('ox:f41427a099a603773.44301043oxcontent0' . $sShopId))
            ->willReturn('testvalue');
        $oSmarty->expects($this->once())->method('assign')->with($this->equalTo(true));

        smarty_function_oxcontent($aParams, $oSmarty);
    }

    public function testWithBrokenContent(): void
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;
        $aParams['oxid'] = 'f41427a099a603773.44301043';
        $aParams['assign'] = false;

        $logger = new TestLogger();
        \OxidEsales\Eshop\Core\Registry::set('logger', $logger);

        /** @var MockObject|Smarty $oSmarty */
        $oSmarty = $this->createPartialMock("smarty", ['fetch']);
        $oSmarty->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('ox:f41427a099a603773.44301043oxcontent0' . $sShopId))
            ->willThrowException(new \Error('fetch failed'));

        $this->assertEmpty(smarty_function_oxcontent($aParams, $oSmarty));
    }
}
