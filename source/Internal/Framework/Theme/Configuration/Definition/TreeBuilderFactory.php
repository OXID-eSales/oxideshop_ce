<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

readonly class TreeBuilderFactory implements TreeBuilderFactoryInterface
{
    public function create(): NodeInterface
    {
        $treeBuilder = new TreeBuilder('themeConfiguration');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('source')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('activated')
                ->end()
                ->scalarNode('title')
                ->end()
                ->arrayNode('themeSettings')
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')
                            ->end()
                            ->variableNode('value')
                            ->end()
                            ->scalarNode('group')
                            ->end()
                            ->integerNode('position')
                            ->end()
                            ->arrayNode('constraints')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder->buildTree();
    }
}
