<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty\Legacy;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy\LegacySmartyEngine;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LegacySmartyEngineFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTemplateEngine()
    {
        $factory = $this->getCompiledTestContainer()->get('smarty.smarty_engine_factory');

        $this->assertInstanceOf(LegacySmartyEngine::class, $factory->getTemplateEngine());
    }

    /**
     * @return ContainerBuilder
     */
    private function getCompiledTestContainer(): ContainerBuilder
    {
        $container = (new TestContainerFactory())->create();
        $container->compile();

        return $container;
    }
}
