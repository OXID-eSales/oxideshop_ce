<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderDelegator;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class TemplateLoaderDelegatorTest extends TestCase
{
    use ProphecyTrait;

    public function testIfFrontend(): void
    {
        $context = $this->prophesize(ContextInterface::class);
        $context->isAdmin()->willReturn(false);

        $delegator = new TemplateLoaderDelegator(
            $context->reveal(),
            $this->getAdminLoader(),
            $this->getFrontendLoader()
        );

        $this->assertEquals('frontend_content', $delegator->getContext('front_template'));
        $this->assertTrue($delegator->exists('front_template'));
    }

    public function testIfAdmin(): void
    {
        $context = $this->prophesize(ContextInterface::class);
        $context->isAdmin()->willReturn(true);

        $delegator = new TemplateLoaderDelegator(
            $context->reveal(),
            $this->getAdminLoader(),
            $this->getFrontendLoader()
        );

        $this->assertEquals('admin_content', $delegator->getContext('admin_template'));
        $this->assertTrue($delegator->exists('admin_template'));
    }

    private function getFrontendLoader(): TemplateLoaderInterface
    {
        $frontendLoader = $this->prophesize(TemplateLoaderInterface::class);
        $frontendLoader->getContext('front_template')->willReturn('frontend_content');
        $frontendLoader->exists('front_template')->willReturn(true);

        return $frontendLoader->reveal();
    }

    private function getAdminLoader(): TemplateLoaderInterface
    {
        $adminLoader = $this->prophesize(TemplateLoaderInterface::class);
        $adminLoader->getContext('admin_template')->willReturn('admin_content');
        $adminLoader->exists('admin_template')->willReturn(true);

        return $adminLoader->reveal();
    }
}
