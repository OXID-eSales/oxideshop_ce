<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TemplateRendererTest extends TestCase
{
    private string $someExistingTemplate = 'page/list/list';
    private string $someTemplateContent = 'some content';


    public function testRenderTemplateWithoutOptionalFileExtensionArgument(): void
    {
        $result = $this
            ->getContainerWithoutOptionalArgument()
            ->get(TemplateRendererInterface::class)
            ->renderTemplate($this->someExistingTemplate);

        $this->assertEquals($result, $this->someTemplateContent);
    }

    public function testExistsWithoutOptionalFileExtensionArgument(): void
    {
        $result = $this
            ->getContainerWithoutOptionalArgument()
            ->get(TemplateRendererInterface::class)
            ->exists($this->someExistingTemplate);

        $this->assertTrue($result);
    }

    private function getContainerWithoutOptionalArgument(): ContainerBuilder
    {
        $container = (new ContainerBuilderFactory())
            ->create()
            ->getContainer();
        $rendererMock = $this->createConfiguredMock(
            TemplateRendererInterface::class,
            [
                'renderTemplate' => $this->someTemplateContent,
                'exists' => true,
            ]
        );
        $container->set(TemplateRendererInterface::class, $rendererMock);
        $container->autowire(TemplateRendererInterface::class, TemplateRendererInterface::class);
        $definition = $container->getDefinition(TemplateRendererInterface::class);
        $arguments = $definition->getArguments();
        unset($arguments['$filenameExtension']);
        $definition->setArguments($arguments);
        $container->compile();

        return $container;
    }
}
