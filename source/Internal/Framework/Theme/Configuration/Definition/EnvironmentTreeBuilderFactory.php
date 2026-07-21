<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

readonly class EnvironmentTreeBuilderFactory implements EnvironmentTreeBuilderFactoryInterface
{
    public function create(): NodeInterface
    {
        $treeBuilder = new TreeBuilder('themeEnvironmentConfiguration');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('themeSettings')
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->variableNode('value')
                                ->isRequired()
                                ->validate()
                                    ->ifNull()
                                    ->thenInvalid('The value must not be null.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder->buildTree();
    }
}
