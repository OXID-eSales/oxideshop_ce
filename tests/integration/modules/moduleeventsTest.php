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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__)) . '/basemoduleTestCase.php';
require_once realpath(dirname(__FILE__)) . '/testData/modules/with_events/files/myevents.php';

class Integration_Modules_ModuleEventsTest extends BaseModuleTestCase
{

    /**
     * Test check shop module activation and deactivation several times
     */
    public function testModuleRemoveInSubShop()
    {
        $oConfig = oxRegistry::getConfig();

        $sState = $oConfig->getShopConfVar('sTestEvents');
        $this->assertSame( null, $sState, 'No events should have been executed till now');

        $oEnvironment = new Environment();
        $oEnvironment->prepare(array('with_events'));

        $sState = $oConfig->getShopConfVar('sTestEvents');
        $this->assertEquals( "Activate", $sState, 'onActivate event first time was not called.');

        $oModule = new oxModule();
        $oModule->load( 'with_events' );

        $oModule->deactivate();
        $sState = $oConfig->getShopConfVar('sTestEvents');
        $this->assertEquals( "Deactivate", $sState, 'onDeactivate event first time was not called.');

        $oModule->activate();
        $sState = $oConfig->getShopConfVar('sTestEvents');
        $this->assertEquals( "Activate", $sState, 'onActivate event second time was not called.');

        $oModule->deactivate();
        $sState = $oConfig->getShopConfVar('sTestEvents');
        $this->assertEquals( "Deactivate", $sState, 'onDeactivate event second time was not called.');
    }
}
