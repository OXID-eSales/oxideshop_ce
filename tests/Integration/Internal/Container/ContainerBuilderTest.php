<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Facts\Edition\EditionSelector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerBuilderTest extends TestCase
{
    public function testWhenCeServicesLoaded()
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $container = $this->makeContainer($context);

        $this->assertSame('CE service!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenPeOverwritesMainServices()
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::PROFESSIONAL);
        $container = $this->makeContainer($context);

        $this->assertSame('Service overwriting for PE!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenEeOverwritesMainServices()
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::ENTERPRISE);
        $container = $this->makeContainer($context);

        $this->assertSame('Service overwriting for EE!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenProjectOverwritesMainServices()
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $context->setGeneratedServicesFilePath(__DIR__ . '/Fixtures/Project/generated_services.yaml');
        $container = $this->makeContainer($context);

        $this->assertSame('Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenProjectOverwritesEditions()
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::ENTERPRISE);
        $context->setConfigurableServicesFilePath(__DIR__ . '/Fixtures/Project/configurable_services.yaml');
        $container = $this->makeContainer($context);

        $this->assertSame('Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    /**
     * @param ContextInterface $context
     * @return Container
     */
    private function makeContainer(ContextInterface $context): Container
    {
        $containerBuilder = new ContainerBuilder($context);
        $container = $containerBuilder->getContainer();
        $container->compile();
        return $container;
    }

    /**
     * @return ContextStub
     */
    private function makeContextStub()
    {
        $context = new ContextStub();
        $context->setCommunityEditionSourcePath(__DIR__ . '/Fixtures/CE');
        $context->setProfessionalEditionRootPath(__DIR__ . '/Fixtures/PE');
        $context->setEnterpriseEditionRootPath(__DIR__ . '/Fixtures/EE');
        $context->setGeneratedServicesFilePath("nonexiting.yaml");
        $context->setConfigurableServicesFilePath('nonexisting.yaml');
        return $context;
    }
}
