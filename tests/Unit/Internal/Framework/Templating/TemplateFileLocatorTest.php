<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateFileLocator;

class TemplateFileLocatorTest extends \PHPUnit\Framework\TestCase
{
    public function testLocate()
    {
        $templateName = 'test_template.tpl';
        $locator = new TemplateFileLocator($this->getConfigMock($templateName));
        $this->assertSame('pathToTpl/' . $templateName, $locator->locate($templateName));
    }

    /**
     * @return Config
     */
    private function getConfigMock($templateName)
    {
        $config = $configMock = $this
            ->getMockBuilder(Config::class)
            ->getMock();
        $config->expects($this->any())
            ->method('getTemplatePath')
            ->with($templateName, false)
            ->will($this->returnValue('pathToTpl/' . $templateName));

        return $config;
    }
}
