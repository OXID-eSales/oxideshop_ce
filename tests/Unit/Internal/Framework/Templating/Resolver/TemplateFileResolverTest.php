<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolver;
use PHPUnit\Framework\TestCase;

final class TemplateFileResolverTest extends TestCase
{
    public function testGetFileNameWithoutExtensionWithEmptyName(): void
    {
        $this->expectException(InvalidTemplateNameException::class);

        (new TemplateFileResolver('tpl'))->getFilename('');
    }

    /** @dataProvider smartyTemplateNameFileDataProvider */
    public function testGetFilenameSmartyTemplate($templateName, $expectedFilename): void
    {
        $filename = (new TemplateFileResolver('tpl'))->getFilename($templateName);

        $this->assertEquals($expectedFilename, $filename);
    }

    /** @dataProvider twigTemplateNameFileDataProvider */
    public function testGetFilenameTwigTemplate($templateName, $expectedFilename): void
    {
        $filename = (new TemplateFileResolver('html.twig'))->getFilename($templateName);

        $this->assertEquals($expectedFilename, $filename);
    }

    public function smartyTemplateNameFileDataProvider(): array
    {
        return [
            [
                'template',
                'template.tpl',
            ],
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
                'some/path/template.name.html',
                'some/path/template.name.html'
            ],
        ];
    }

    public function twigTemplateNameFileDataProvider(): array
    {
        return [
            [
                'template',
                'template.html.twig',
            ],
            [
                'template.html.twig',
                'template.html.twig'
            ],
            [
                'some/path/template_name.html.twig',
                'some/path/template_name.html.twig'
            ],
            [
                'some/path/template.name.xml.twig',
                'some/path/template.name.xml.twig',
            ],
            [
                'some/path/template.name.html.twig',
                'some/path/template.name.html.twig'
            ],
            [
                'some/path/template.name.html',
                'some/path/template.name.html'
            ],
        ];
    }
}
