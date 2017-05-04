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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__)) . '/basemoduleTestCase.php';
require_once realpath(dirname(__FILE__)) . '/testData/modules/with_events/files/myevents.php';

class Integration_Modules_ModuleEventsTest extends BaseModuleTestCase
{

    /**
     * Test check shop module activation first time
     */
    public function testModuleActivate()
    {
        $oConfig = oxRegistry::getConfig();

        $sState = $oConfig->getConfigParam('sTestActivateEvent');
        $this->assertSame(null, $sState, 'No events should have been executed till now');

        $oEnvironment = new Environment();
        $oEnvironment->prepare(array('with_events'));

        $sState = $oConfig->getConfigParam('sTestActivateEvent');
        $this->assertEquals("Activate", $sState, 'onActivate event was not called.');
    }

    /**
     * Test check shop module activation second time
     */
    public function testModuleActivateSecondTime()
    {
        $oConfig = oxRegistry::getConfig();

        $oEnvironment = new Environment();
        $oEnvironment->prepare(array('with_events'));

        $oModule = new oxModule();
        $oModule->load('with_events');

        $this->_deactivateModule($oModule);
        $oConfig->setConfigParam('sTestActivateEvent', '_removed_');

        $this->_activateModule($oModule);

        $sState = $oConfig->getConfigParam('sTestActivateEvent');
        $this->assertEquals("Activate", $sState, 'onActivate event was not called on second activation.');
    }

    /**
     * Test check shop module deactivation
     */
    public function testModuleDeactivate()
    {
        $oConfig = oxRegistry::getConfig();

        $sState = $oConfig->getConfigParam('sTestDeactivateEvent');
        $this->assertSame(null, $sState, 'No events should have been executed till now');

        $oEnvironment = new Environment();
        $oEnvironment->prepare(array('with_events'));

        $oModule = new oxModule();
        $oModule->load('with_events');

        $this->_deactivateModule($oModule);

        $sState = $oConfig->getConfigParam('sTestDeactivateEvent');
        $this->assertEquals("Deactivate", $sState, 'onDeactivate event was not called.');
    }
}
 