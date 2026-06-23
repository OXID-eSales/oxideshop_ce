<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Definition;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfiguration\ThemeSettingsDataMapper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

readonly class TreeBuilderFactory implements TreeBuilderFactoryInterface
{
    public function create(): NodeInterface
    {
        $treeBuilder = new TreeBuilder('themeConfiguration');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('themeSource')
                ->end()
                ->scalarNode('version')
                ->end()
                ->scalarNode('activated')
                ->end()
                ->scalarNode('parentTheme')
                ->end()
                ->arrayNode('title')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('description')
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('thumbnail')
                ->end()
                ->scalarNode('author')
                ->end()
                ->arrayNode(ThemeSettingsDataMapper::MAPPING_KEY)
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('group')
                            ->end()
                            ->scalarNode('type')
                            ->end()
                            ->variableNode('value')
                            ->end()
                            ->scalarNode('position')
                            ->end()
                            ->arrayNode('constraints')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder->buildTree();
    }
}
