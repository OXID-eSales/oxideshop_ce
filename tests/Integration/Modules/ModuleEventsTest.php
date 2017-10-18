<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxRegistry;

require_once __DIR__ . '/TestData/modules/with_events/files/myevents.php';

class ModuleEventsTest extends BaseModuleTestCase
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

        $oModule = oxNew('oxModule');
        $oModule->load('with_events');

        $this->deactivateModule($oModule);
        $oConfig->setConfigParam('sTestActivateEvent', '_removed_');

        $this->activateModule($oModule);

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

        $oModule = oxNew('oxModule');
        $oModule->load('with_events');

        $this->deactivateModule($oModule);

        $sState = $oConfig->getConfigParam('sTestDeactivateEvent');
        $this->assertEquals("Deactivate", $sState, 'onDeactivate event was not called.');
    }
}
