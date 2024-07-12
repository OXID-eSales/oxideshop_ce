<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\VoucherSerie;
use \oxTestModules;

/**
 * Tests for VoucherSerie_Groups class
 */
class VoucherSerieGroupsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * VoucherSerie_Groups::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxvoucherserie', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('VoucherSerie_Groups');
        $this->assertSame('voucherserie_groups', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\VoucherSerie::class, $aViewData['edit']);
    }
}
