<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for PriceAlarm_Send class
 */
class PriceAlarmSendTest extends \PHPUnit\Framework\TestCase
{

    /**
     * PriceAlarm_Send::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PriceAlarmSend::class, ["setupNavigation"]);
        $oView->expects($this->once())->method('setupNavigation');
        $this->assertSame('pricealarm_done', $oView->render());
    }

    /**
     * PriceAlarm_Send::SetupNavigation() test case
     */
    public function testSetupNavigation()
    {
        // testing..
        $sNode = "pricealarm_list";
        $this->setRequestParameter("menu", $sNode);
        $this->setRequestParameter('actedit', 1);

        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getTabs", "getActiveTab"]);
        $oNavigation->method('getActiveTab')->willReturn("testEdit");
        $oNavigation->expects($this->once())->method('getTabs')->with($sNode, 1)->willReturn("editTabs");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PriceAlarmSend::class, ["getNavigation"]);
        $oView->expects($this->once())->method('getNavigation')->willReturn($oNavigation);

        $oView->setupNavigation($sNode);
        $this->assertSame("editTabs", $oView->getViewDataElement("editnavi"));
        $this->assertSame("testEdit", $oView->getViewDataElement("actlocation"));
        $this->assertSame("testEdit", $oView->getViewDataElement("default_edit"));
        $this->assertSame(1, $oView->getViewDataElement("actedit"));
    }

    /**
     * PriceAlarm_Send::SendeMail() test case
     */
    public function testSendeMail()
    {
        $oAlarm = $this->getMock(\OxidEsales\Eshop\Application\Model\PriceAlarm::class, ['save', 'load']);
        $oAlarm->expects($this->once())->method('load')
            ->with("paid")
            ->willReturn(true);
        $oAlarm->expects($this->once())->method('save')
            ->willReturn(true);

        oxTestModules::addModuleObject('oxpricealarm', $oAlarm);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['sendPricealarmToCustomer']);
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')
            ->with("info@example.com", $this->isInstanceOf(\OxidEsales\EshopCommunity\Application\Model\PriceAlarm::class))
            ->willReturn(true);

        oxTestModules::addModuleObject('oxemail', $oEmail);

        // testing..
        $oView = oxNew('PriceAlarm_Send');
        $oView->sendeMail("info@example.com", "", "paid", "");
    }
}
