<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Facts\Edition\EditionSelector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerBuilderTest extends TestCase
{
    public function testWhenCeServicesLoaded(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $container = $this->makeContainer($context);

        $this->assertSame('CE service!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testBuilderCanWorkWithBasicContext(): void
    {
        $context = $this->makeBasicContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $container = $this->makeContainer($context);

        $this->assertSame('CE service!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenPeOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::PROFESSIONAL);
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for PE!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenEeOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::ENTERPRISE);
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for EE!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenProjectOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $context->setGeneratedServicesFilePath(__DIR__ . '/Fixtures/Project/generated_services.yaml');
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenProjectOverwritesEditions(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::ENTERPRISE);
        $context->setConfigurableServicesFilePath(__DIR__ . '/Fixtures/Project/configurable_services.yaml');
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenShopRelatedConfigOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $context->setShopConfigurableServicesFilePath(
            __DIR__ . '/Fixtures/Project/shop_configurable_services.yaml'
        );
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    private function makeContainer(BasicContextInterface $context): Container
    {
        $containerBuilder = new ContainerBuilder($context);
        $container = $containerBuilder->getContainer();
        $container->compile();
        return $container;
    }

    private function makeContextStub(): ContextStub
    {
        $context = new ContextStub();
        $context->setCommunityEditionSourcePath(__DIR__ . '/Fixtures/CE');
        $context->setProfessionalEditionRootPath(__DIR__ . '/Fixtures/PE');
        $context->setEnterpriseEditionRootPath(__DIR__ . '/Fixtures/EE');
        $context->setGeneratedServicesFilePath('nonexisting.yaml');
        $context->setConfigurableServicesFilePath('nonexisting.yaml');
        $context->setShopConfigurableServicesFilePath('nonexisting.yaml');
        $context->setActiveModuleServicesFilePath('nonexisting.yaml');
        return $context;
    }

    private function makeBasicContextStub(): BasicContextStub
    {
        $context = new BasicContextStub();
        $context->setCommunityEditionSourcePath(__DIR__ . '/Fixtures/CE');
        $context->setProfessionalEditionRootPath(__DIR__ . '/Fixtures/PE');
        $context->setEnterpriseEditionRootPath(__DIR__ . '/Fixtures/EE');
        $context->setGeneratedServicesFilePath('nonexisting.yaml');
        $context->setConfigurableServicesFilePath('nonexisting.yaml');
        $context->setShopConfigurableServicesFilePath('nonexisting.yaml');
        $context->setActiveModuleServicesFilePath('nonexisting.yaml');
        return $context;
    }
}
