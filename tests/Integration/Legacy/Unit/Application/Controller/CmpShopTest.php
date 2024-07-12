<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxException;
use OxidEsales\EshopCommunity\Core\Registry;
use \oxTestModules;

/**
 * oxcmp_shop tests
 */
class CmpShopTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxcmp_shop::render()
     */
    public function testRenderNoActiveShop()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getClassKey"]);
        $oView->expects($this->once())->method('getClassKey')->willReturn("test");

        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxactive = new oxField(0);

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['showOfflinePage']);
        $oUtils->expects($this->once())->method('showOfflinePage');
        Registry::set(\OxidEsales\Eshop\Core\Utils::class, $oUtils);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam", "getActiveView", "getActiveShop"]);
        $oConfig->expects($this->once())->method('getActiveView')->willReturn($oView);
        $oConfig->method('getConfigParam')->willReturn(false);
        $oConfig->expects($this->once())->method('getActiveShop')->willReturn($oShop);

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\ShopComponent::class, ["getConfig", "isAdmin"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oCmp->expects($this->once())->method('isAdmin')->willReturn(false);

        $oCmp->render();
    }
}
