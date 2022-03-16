<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator\RandomTokenGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder as Container;

final class RandomTokenGeneratorBridgeTest extends TestCase
{
    private RandomTokenGeneratorBridgeInterface $bridge;
    private Container $container;
    /** @var ObjectProphecy|SystemSecurityCheckerInterface */
    private ObjectProphecy $systemSecurityChecker;
    /** @var ObjectProphecy|RandomTokenGeneratorInterface */
    private ObjectProphecy $randomTokenGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        ContainerFactory::getInstance()->resetContainer();
        $this->container = (new ContainerBuilder(new BasicContextStub()))->getContainer();
        $this->mockDependencies();
        $this->container->compile();

        $this->bridge = $this->container->get(RandomTokenGeneratorBridgeInterface::class);
    }

    public function testGetHexTokenWithSourceOfRandomnessWillCallExpectedMethod(): void
    {
        $length = 3;
        $this->systemSecurityChecker
            ->isCryptographicallySecure()
            ->willReturn(true);
        $this->randomTokenGenerator
            ->getHexToken($length)
            ->willReturn('123');

        $this->bridge->getHexTokenWithFallback($length);

        $this->randomTokenGenerator
            ->getHexToken($length)
            ->shouldHaveBeenCalled();
    }

    public function testGetAlphanumericTokenWillCallExpectedMethod(): void
    {
        $length = 3;
        $this->randomTokenGenerator
            ->getAlphanumericToken($length)
            ->willReturn('xyz');

        $this->bridge->getAlphanumericToken($length);

        $this->randomTokenGenerator
            ->getAlphanumericToken($length)
            ->shouldHaveBeenCalled();
    }

    public function testGetHexTokenWillCallExpectedMethod(): void
    {
        $length = 3;
        $this->randomTokenGenerator
            ->getHexToken($length)
            ->willReturn('123');

        $this->bridge->getHexToken($length);

        $this->randomTokenGenerator
            ->getHexToken($length)
            ->shouldHaveBeenCalled();
    }

    protected function mockDependencies(): void
    {
        $this->systemSecurityChecker = $this->prophesize(SystemSecurityCheckerInterface::class);
        $this->container->set(SystemSecurityCheckerInterface::class, $this->systemSecurityChecker->reveal());
        $this->container->autowire(SystemSecurityCheckerInterface::class);

        $this->randomTokenGenerator = $this->prophesize(RandomTokenGeneratorInterface::class);
        $this->container->set(RandomTokenGeneratorInterface::class, $this->randomTokenGenerator->reveal());
        $this->container->autowire(RandomTokenGeneratorInterface::class);
    }
}
