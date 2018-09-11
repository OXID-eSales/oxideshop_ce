<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty;

class SmartyContextTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTemplateEngineDebugMode()
    {

    }

    private function getContainer()
    {
        $factory = ContainerFactory::getInstance();

        return $factory->getContainer();
    }
}
