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

<<<<<<< HEAD
        (new TemplateFileResolver('tpl'))->getFilename('');
=======
        (new TemplateFileResolver(['tpl'], 'tpl'))->getFilename('');
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver
    }

    /** @dataProvider smartyTemplateNameFileDataProvider */
    public function testGetFilenameSmartyTemplate($templateName, $expectedFilename): void
    {
<<<<<<< HEAD
        $filename = (new TemplateFileResolver('tpl'))->getFilename($templateName);
=======
        $filename = (new TemplateFileResolver(['tpl'], 'tpl'))->getFilename($templateName);
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver

        $this->assertEquals($expectedFilename, $filename);
    }

    /** @dataProvider twigTemplateNameFileDataProvider */
    public function testGetFilenameTwigTemplate($templateName, $expectedFilename): void
    {
<<<<<<< HEAD
        $filename = (new TemplateFileResolver('html.twig'))->getFilename($templateName);
=======
        $filename = (new TemplateFileResolver(['tpl', 'html.twig'], 'html.twig'))->getFilename($templateName);
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver

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
<<<<<<< HEAD
                'template.html.twig',
                'template.html.twig'
            ],
            [
                'some/path/template_name.html.twig',
                'some/path/template_name.html.twig'
            ],
            [
                'some/path/template.name.html.twig',
=======
                'template.tpl',
                'template.html.twig'
            ],
            [
                'some/path/template_name.tpl',
                'some/path/template_name.html.twig'
            ],
            [
                'some/path/template.name.tpl',
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver
                'some/path/template.name.html.twig'
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
                'some/path/template.name.html.twig',
                'some/path/template.name.html.twig'
            ],
        ];
    }
}
