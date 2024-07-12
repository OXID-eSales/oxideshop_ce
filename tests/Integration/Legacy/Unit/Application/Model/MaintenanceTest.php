<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxTestModules;

class MaintenanceTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test case for oxMaintenance::execute()
     */
    public function testExecute()
    {
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['updateUpcomingPrices']);
        $oList->expects($this->once())->method('updateUpcomingPrices')->with(true);

        oxTestModules::addModuleObject('oxarticlelist', $oList);

        $oMaintenance = oxNew("oxMaintenance");
        $oMaintenance->execute();
    }
}
