<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Smarty\Extension\SmartyDefaultTemplateHandler;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;

class SmartyDefaultTemplateHandlerTest extends \PHPUnit\Framework\TestCase
{
    const RESOURCE_NAME = 'smartyTemplate.tpl';
    const RESOURCE_CONTENT = "The new contents of the file";
    const RESOURCE_TIMESTAMP = 1;

    /**
     * @dataProvider smartyDefaultTemplateHandlerDataProvider
     *
     * @param string $resourceType  The Type of the given file.
     * @param mixed  $givenResource The template to test.
     * @param mixed  $expReturn     The expected return value of the method call.
     * @param mixed  $expContent    The expected content after smarty reads the template.
     * @param int    $expTimestamp  The expected timestamp of the template.
     */
    public function testSmartyDefaultTemplateHandler($resourceType, $givenResource, $expReturn, $expContent, $expTimestamp)
    {
        $resourceName = self::RESOURCE_NAME;
        $resourceContent = self::RESOURCE_CONTENT;
        $resourceTimestamp = self::RESOURCE_TIMESTAMP;

        $smarty = new \Smarty();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';

        $handler = $this->getSmartyDefaultTemplateHandler($givenResource);
        $return = $handler->handleTemplate(
            $resourceType,
            $resourceName,
            $resourceContent,
            $resourceTimestamp,
            $smarty
        );

        $this->assertSame($expReturn, $return);
        $this->assertSame($expContent, $resourceContent);
        $this->assertSame($expTimestamp, $resourceTimestamp);
    }

    public function smartyDefaultTemplateHandlerDataProvider()
    {
        $templateDir = $this->getTemplateDirectory();
        $template = $templateDir . self::RESOURCE_NAME;
        $returnContent = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}]'."\n".'[{$title}]';

        return [
            ['content', self::RESOURCE_NAME, false, self::RESOURCE_CONTENT, self::RESOURCE_TIMESTAMP],
            ['file', self::RESOURCE_NAME, false, self::RESOURCE_CONTENT, self::RESOURCE_TIMESTAMP],
            ['file', $templateDir, false, self::RESOURCE_CONTENT, self::RESOURCE_TIMESTAMP],
            ['file', $template, true, $returnContent, filemtime($template)],
        ];
    }

    private function getSmartyDefaultTemplateHandler($template)
    {
        $smartyContextMock = $this
        ->getMockBuilder(SmartyContextInterface::class)
        ->getMock();

        $smartyContextMock
            ->method('getTemplatePath')
            ->willReturn($template);

        return new SmartyDefaultTemplateHandler($smartyContextMock);
    }

    private function getTemplateDirectory()
    {
        return __DIR__ . '/../Fixtures/';
    }
}