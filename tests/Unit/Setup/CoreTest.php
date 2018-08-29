<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Setup;

use OxidEsales\EshopCommunity\Setup\Core;

/**
 * SetupCoreTest tests
 */
class CoreTest extends \OxidTestCase
{
    /**
     * Test get instance.
     */
    public function testGetInstance()
    {
        $oSetupCore = new Core();
        $this->assertTrue($oSetupCore->getInstance("Core") instanceof Core);
    }
}
