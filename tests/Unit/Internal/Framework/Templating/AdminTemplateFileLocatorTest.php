<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\AdminTemplateFileLocator;

class AdminTemplateFileLocatorTest extends \PHPUnit\Framework\TestCase
{
    public function testLocate()
    {
        $templateName = 'test_template.tpl';
        $locator = new AdminTemplateFileLocator($this->getConfigMock($templateName));
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
            ->with($templateName, true)
            ->will($this->returnValue('pathToTpl/' . $templateName));

        return $config;
    }
}
