<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateNameResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\LegacyTemplateNameResolver;

class LegacyTemplateNameResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider resolveSmartyDataProvider
     */
    public function testResolveSmartyTemplate($templateName, $response)
    {
        $resolver = new LegacyTemplateNameResolver(
            new TemplateNameResolver(
                $this->getTemplateEngineMock('tpl')
            )
        );

        $this->assertSame($response, $resolver->resolve($templateName));
    }

    public function resolveSmartyDataProvider()
    {
        return [
            [
                'template.tpl',
                'template.tpl'
            ],
            [
                'some/path/template.tpl',
                'some/path/template.tpl'
            ],
            [
                'some/path/template_name.tpl',
                'some/path/template_name.tpl'
            ],
            [
                'some/path/template.name.tpl',
                'some/path/template.name.tpl'
            ],
            [
                '',
                ''
            ]
        ];
    }

    /**
     * @dataProvider resolveTwigDataProvider
     */
    public function testResolveTwigTemplate($response, $templateName)
    {
        $resolver = new LegacyTemplateNameResolver(
            new TemplateNameResolver(
                $this->getTemplateEngineMock('html.twig')
            )
        );

        $this->assertSame($response, $resolver->resolve($templateName));
    }

    public function resolveTwigDataProvider()
    {
        return [
            [
                'template.html.twig',
                'template.tpl'
            ],
            [
                'some/path/template_name.html.twig',
                'some/path/template_name.tpl'
            ],
            [
                'some/path/template.name.html.twig',
                'some/path/template.name.tpl'
            ],
            [
                '',
                ''
            ]
        ];
    }

    /**
     * @param string $extension
     *
     * @return TemplateEngineInterface
     */
    private function getTemplateEngineMock($extension)
    {
        $engine = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $engine->expects($this->any())
            ->method('getDefaultFileExtension')
            ->will($this->returnValue($extension));

        return $engine;
    }
}
