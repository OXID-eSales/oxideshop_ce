<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

readonly class MetaDataTreeBuilderFactory implements MetaDataTreeBuilderFactoryInterface
{
    public function create(): NodeInterface
    {
        $treeBuilder = new TreeBuilder('themeMetaData');

        $treeBuilder->getRootNode()
            ->children()
                ->stringNode('id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->stringNode('version')
                    ->defaultValue('')
                ->end()
                ->stringNode('title')
                    ->defaultValue('')
                ->end()
                ->stringNode('description')
                    ->defaultValue('')
                ->end()
                ->stringNode('thumbnail')
                    ->defaultValue('')
                ->end()
                ->stringNode('author')
                    ->defaultValue('')
                ->end()
                ->stringNode('parentTheme')
                    ->defaultValue('')
                ->end()
                ->arrayNode('parentVersions')
                    ->stringPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder->buildTree();
    }
}
