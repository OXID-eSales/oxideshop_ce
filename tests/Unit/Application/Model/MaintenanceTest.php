<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxTestModules;

class MaintenanceTest extends \OxidTestCase
{

    /**
     * Test case for oxMaintenance::execute()
     *
     * @return null
     */
    public function testExecute()
    {
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, array('updateUpcomingPrices'));
        $oList->expects($this->once())->method('updateUpcomingPrices')->with($this->equalTo(true));

        oxTestModules::addModuleObject('oxarticlelist', $oList);

        $oMaintenance = oxNew("oxMaintenance");
        $oMaintenance->execute();
    }
}
