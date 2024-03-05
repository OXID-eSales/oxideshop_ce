<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Locator;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\AdminTemplateFileLocator;
use PHPUnit\Framework\TestCase;

final class AdminTemplateFileLocatorTest extends TestCase
{
    public function testLocate(): void
    {
        $templateName = 'test_template.tpl';
        $locator = new AdminTemplateFileLocator($this->getConfigMock($templateName));
        $this->assertSame('pathToTpl/' . $templateName, $locator->locate($templateName));
    }

    private function getConfigMock(string $templateName): Config
    {
        $config = $this
            ->getMockBuilder(Config::class)
            ->getMock();
        $config
            ->method('getTemplatePath')
            ->with($templateName, true)
            ->willReturn('pathToTpl/' . $templateName);

        return $config;
    }
}
