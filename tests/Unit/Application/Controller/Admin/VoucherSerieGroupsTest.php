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
class VoucherSerieGroupsTest extends \OxidTestCase
{

    /**
     * VoucherSerie_Groups::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxvoucherserie', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('VoucherSerie_Groups');
        $this->assertEquals('voucherserie_groups.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof voucherserie);
    }
}
