<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Setup\Parameters\SetupParametersFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\InputInterface;

final class SetupParametersFactoryTest extends TestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    public function testCreate(): void
    {
        $setupLanguage = 'de';
        $input = $this->prophesize(InputInterface::class);
        $input->getOption('language')->willReturn($setupLanguage);

        $parameters = $this->get(SetupParametersFactoryInterface::class)
            ->create(
                $input->reveal()
            );

        $context = $this->get(ContextInterface::class);
        $this->assertEquals($context->getCacheDirectory(), $parameters->getCacheDir());
        $this->assertEquals($context->getCacheDirectory(), $parameters->getCacheDir());
        $this->assertEquals($context->getDatabaseUrl(), $parameters->getDbConfig()->getDatabaseUrl());
        $this->assertEquals($context->getShopBaseUrl(), $parameters->getShopBaseUrl()->getUrl());
        $this->assertEquals($setupLanguage, $parameters->getLanguage()->getCode());
    }
}
