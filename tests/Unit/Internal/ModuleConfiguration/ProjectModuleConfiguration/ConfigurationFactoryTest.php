<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ModuleConfiguration\ProjectModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration\ConfigurationFactory;

/**
 * @internal
 */
class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $factory = new ConfigurationFactory();
        $factory->create();
    }
}
