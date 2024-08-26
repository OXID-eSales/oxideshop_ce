<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Tests\EnvTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Fixtures\Ce\Internal\ServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

final class ContainerBuilderTest extends TestCase
{
    use EnvTrait;

    public function testServiceLoadingOrderWithAllConfigsPresent(): void
    {
        $this->loadEnvFixture(__DIR__, ['OXID_ENV=abc']);
        $context = $this->makeContextStub();
        $context->setEdition(Facts::COMMUNITY);

        $container = (new ContainerBuilder($context))->getContainer();
        $container->compile();

        $decoratorChainOutput =
            'ce.component.module.project_default_env.project_specific_env';
        $this->assertSame(
            $decoratorChainOutput,
            $container->get(ServiceInterface::class)->getNamespace()
        );
    }

    public function testServiceLoadingOrderWithShopAndNoEnvironmentConfig(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(Facts::COMMUNITY);

        $container = (new ContainerBuilder($context))->getContainer();
        $container->compile();


        $decoratorChainOutput =
            'ce.component.module.project_default_env';
        $this->assertSame(
            $decoratorChainOutput,
            $container->get(ServiceInterface::class)->getNamespace()
        );
    }

    public function testServiceLoadingOrderWithEnvironmentAndNoShopConfig(): void
    {
        ContainerFactory::resetContainer();
        $this->loadEnvFixture(__DIR__, ['OXID_ENV=abc']);
        $context = $this->makeContextStub();
        $context->setEdition(Facts::COMMUNITY);

        $container = (new ContainerBuilder($context))->getContainer();
        $container->compile();

        $decoratorChainOutput =
            'ce.component.module.project_default_env.project_specific_env';
        $this->assertSame(
            $decoratorChainOutput,
            $container->get(ServiceInterface::class)->getNamespace()
        );
    }

    public function testServiceLoadingOrderWithNoShopAndNoEnvironmentConfigs(): void
    {
        $this->loadEnvFixture(__DIR__, ['OXID_ENV=xyz']);
        $context = $this->makeContextStub();
        $context->setEdition(Facts::COMMUNITY);

        $container = (new ContainerBuilder($context))->getContainer();
        $container->compile();

        $decoratorChainOutput = 'ce.component.module.project_default_env';
        $this->assertSame(
            $decoratorChainOutput,
            $container->get(ServiceInterface::class)->getNamespace()
        );
    }

    private function makeContextStub(): ContextStub
    {
        $context = new ContextStub();
        $context->setCommunityEditionSourcePath(__DIR__ . '/Fixtures/Ce');
        $context->setGeneratedServicesFilePath(__DIR__ . '/Fixtures/var/generated_services.yaml');
        $context->setActiveModuleServicesFilePath(__DIR__ . '/Fixtures/var/active_module_services.yaml');
        $context->setProjectConfigurationDirectory(__DIR__ . '/Fixtures/var/configuration/');

        return $context;
    }
}
