<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Twig;

use OxidEsales\Twig\Loader\FilesystemLoader;
use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OxidTwigLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }
        if (!$container->has(TemplateDirectoryResolverInterface::class)) {
            return;
        }

        $container->register('oxid.twig.loader.theme_filesystem', FilesystemLoader::class)
            ->setArguments([new Reference(TemplateDirectoryResolverInterface::class)])
            ->setPublic(true)
            ->addTag('twig.loader', ['priority' => 100]);

        if ($container->hasDefinition(\OxidEsales\Twig\Event\AdminModeChangeEventSubscriber::class)) {
            $container->getDefinition(\OxidEsales\Twig\Event\AdminModeChangeEventSubscriber::class)
                ->setArgument(0, new Reference('oxid.twig.loader.theme_filesystem'));
        }
    }
}
