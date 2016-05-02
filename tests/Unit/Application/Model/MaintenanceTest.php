<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Model;

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
        $oList = $this->getMock('oxArticleList', array('updateUpcomingPrices'));
        $oList->expects($this->once())->method('updateUpcomingPrices')->with($this->equalTo(true));

        oxTestModules::addModuleObject('oxarticlelist', $oList);

        $oMaintenance = oxNew("oxMaintenance");
        $oMaintenance->execute();
    }
}