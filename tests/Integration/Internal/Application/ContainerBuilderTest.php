<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    public function testWhenCeServicesLoaded()
    {
        $facts = $this->getMockBuilder(Facts::class)->getMock();
        $facts->method('getCommunityEditionSourcePath')->willReturn(__DIR__ . '/Fixtures/CE');
        $facts->method('isProfessional')->willReturn(false);
        $facts->method('isEnterprise')->willReturn(false);
        $container = $this->makeContainer($facts);

        $this->assertSame('CE service!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenPeOverwritesMainServices()
    {
        $facts = $this->getMockBuilder(Facts::class)->getMock();
        $facts->method('getCommunityEditionSourcePath')->willReturn(__DIR__ . '/Fixtures/CE');
        $facts->method('getProfessionalEditionRootPath')->willReturn(__DIR__ . '/Fixtures/PE');
        $facts->method('isProfessional')->willReturn(true);
        $facts->method('isEnterprise')->willReturn(false);
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for PE!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenEeOverwritesMainServices()
    {
        $facts = $this->getMockBuilder(Facts::class)->getMock();
        $facts->method('getCommunityEditionSourcePath')->willReturn(__DIR__ . '/Fixtures/CE');
        $facts->method('getProfessionalEditionRootPath')->willReturn(__DIR__ . '/Fixtures/PE');
        $facts->method('getEnterpriseEditionRootPath')->willReturn(__DIR__ . '/Fixtures/EE');
        $facts->method('isProfessional')->willReturn(false);
        $facts->method('isEnterprise')->willReturn(true);
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for EE!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenProjectOverwritesMainServices()
    {
        $facts = $this->getMockBuilder(Facts::class)->getMock();
        $facts->method('isProfessional')->willReturn(false);
        $facts->method('isEnterprise')->willReturn(false);
        $facts->method('getCommunityEditionSourcePath')->willReturn(__DIR__ . '/Fixtures/CE');
        $facts->method('getSourcePath')->willReturn(__DIR__ . '/Fixtures/Project');
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for Project!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenProjectOverwritesEditions()
    {
        $facts = $this->getMockBuilder(Facts::class)->getMock();
        $facts->method('getCommunityEditionSourcePath')->willReturn(__DIR__ . '/Fixtures/CE');
        $facts->method('getProfessionalEditionRootPath')->willReturn(__DIR__ . '/Fixtures/PE');
        $facts->method('getEnterpriseEditionRootPath')->willReturn(__DIR__ . '/Fixtures/EE');
        $facts->method('isEnterprise')->willReturn(true);
        $facts->method('getSourcePath')->willReturn(__DIR__ . '/Fixtures/Project');
        $container = $this->makeContainer($facts);

        $this->assertSame('Service overwriting for Project!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    /**
     * @param $facts
     * @return \Symfony\Component\DependencyInjection\Container
     */
    protected function makeContainer($facts): \Symfony\Component\DependencyInjection\Container
    {
        $containerBuilder = new ContainerBuilder($facts);
        $container = $containerBuilder->getContainer();
        $container->compile();
        return $container;
    }
}
