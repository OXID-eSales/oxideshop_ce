<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Extension\SmartyDefaultTemplateHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartyDefaultTemplateHandlerTest extends \PHPUnit\Framework\TestCase
{
    private $resourceName = 'smartyTemplate.tpl';
    private $resourceContent = "The new contents of the file";
    private $resourceTimeStamp = 1;

    /**
     * If it is not template file or it is not valid,
     * content and timestamp should not be changed.
     *
     * @dataProvider smartyDefaultTemplateHandlerDataProvider
     *
     * @param string $resourceType  The Type of the given file.
     * @param mixed  $givenResource The template to test.
     */
    public function testSmartyDefaultTemplateHandlerWithoutExistingFile($resourceType, $givenResource)
    {
        $resourceName = $this->resourceName;
        $resourceContent = $this->resourceContent;
        $resourceTimestamp = $this->resourceTimeStamp;

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

        $this->assertFalse($return);
        $this->assertSame($this->resourceContent, $resourceContent);
        $this->assertSame($this->resourceTimeStamp, $resourceTimestamp);
    }

    public function smartyDefaultTemplateHandlerDataProvider()
    {
        return [
            ['content', $this->resourceName],
            ['file', $this->resourceName],
            ['file', $this->getTemplateDirectory()]
        ];
    }

    public function testSmartyDefaultTemplateHandler()
    {
        $resourceName = $this->resourceName;
        $resourceContent = $this->resourceContent;
        $resourceTimestamp = $this->resourceTimeStamp;

        $smarty = new \Smarty();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';

        $template = $this->getTemplateDirectory() . $resourceName;
        $returnContent = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}]' . "\n" . '[{$title}]';

        $handler = $this->getSmartyDefaultTemplateHandler($template);
        $return = $handler->handleTemplate(
            'file',
            $resourceName,
            $resourceContent,
            $resourceTimestamp,
            $smarty
        );

        $this->assertTrue($return);
        $this->assertSame($returnContent, $resourceContent);
        $this->assertSame(filemtime($template), $resourceTimestamp);
    }

    private function getSmartyDefaultTemplateHandler($template)
    {
        $context = $this
        ->getMockBuilder(SmartyContextInterface::class)
        ->getMock();

        $context
            ->method('getTemplatePath')
            ->willReturn($template);

        return new SmartyDefaultTemplateHandler($context);
    }

    private function getTemplateDirectory()
    {
        return __DIR__ . '/../Fixtures/';
    }
}
