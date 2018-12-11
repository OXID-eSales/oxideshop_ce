<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Utility\FactsContext;
use OxidEsales\Facts\Edition\EditionSelector;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    public function testWhenCeServicesLoaded()
    {
        $context = $this->makeContextStub();
        $context->method('getEdition')->willReturn(EditionSelector::COMMUNITY);
        $container = $this->makeContainer($context);

        $this->assertSame('CE service!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenPeOverwritesMainServices()
    {
        $facts = $this->makeContextStub();
        $facts->method('getEdition')->willReturn(EditionSelector::PROFESSIONAL);
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for PE!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenEeOverwritesMainServices()
    {
        $facts = $this->makeContextStub();
        $facts->method('getEdition')->willReturn(EditionSelector::ENTERPRISE);
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for EE!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenProjectOverwritesMainServices()
    {
        $facts = $this->makeContextStub();
        $facts->method('getEdition')->willReturn(EditionSelector::COMMUNITY);
        $facts->method('getSourcePath')->willReturn(__DIR__ . '/Fixtures/Project');
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for Project!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenProjectOverwritesEditions()
    {
        $facts = $this->makeContextStub();
        $facts->method('getEdition')->willReturn(EditionSelector::ENTERPRISE);
        $facts->method('getSourcePath')->willReturn(__DIR__ . '/Fixtures/Project');
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for Project!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    /**
     * @param FactsContext $context
     * @return \Symfony\Component\DependencyInjection\Container
     */
    private function makeContainer(FactsContext $context): \Symfony\Component\DependencyInjection\Container
    {
        $containerBuilder = new ContainerBuilder($context);
        $container = $containerBuilder->getContainer();
        $container->compile();
        return $container;
    }

    /**
     * @return FactsContext|\PHPUnit\Framework\MockObject\MockObject
     */
    private function makeContextStub()
    {
        $facts = $this->getMockBuilder(FactsContext::class)->getMock();
        $facts->method('getCommunityEditionSourcePath')->willReturn(__DIR__ . '/Fixtures/CE');
        $facts->method('getProfessionalEditionRootPath')->willReturn(__DIR__ . '/Fixtures/PE');
        $facts->method('getEnterpriseEditionRootPath')->willReturn(__DIR__ . '/Fixtures/EE');
        return $facts;
    }
}
