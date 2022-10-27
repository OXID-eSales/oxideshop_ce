<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Definition;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassExtensionsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ControllersDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\EventsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

class TreeBuilderFactory implements TreeBuilderFactoryInterface
{
    /**
     * @return NodeInterface
     */
    public function create(): NodeInterface
    {
        $treeBuilder = new TreeBuilder('moduleConfiguration');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('moduleSource')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('version')
                ->end()
                ->scalarNode('activated')
                ->end()
                ->arrayNode('title')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('description')
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('lang')
                ->end()
                ->scalarNode('thumbnail')
                ->end()
                ->scalarNode('author')
                ->end()
                ->scalarNode('url')
                ->end()
                ->scalarNode('email')
                ->end()
                ->arrayNode(ClassExtensionsDataMapper::MAPPING_KEY)
                    ->normalizeKeys(false)->scalarPrototype()->end()
                ->end()
                ->arrayNode(ControllersDataMapper::MAPPING_KEY)
                    ->normalizeKeys(false)->scalarPrototype()->end()
                ->end()
                ->arrayNode(EventsDataMapper::MAPPING_KEY)
                    ->normalizeKeys(false)->scalarPrototype()->end()
                ->end()
                ->arrayNode(ModuleSettingsDataMapper::MAPPING_KEY)
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('group')
                            ->end()
                            ->scalarNode('name')
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
