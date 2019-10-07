<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Definition;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassesWithoutNamespaceDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassExtensionsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ControllersDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\EventsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\SmartyPluginDirectoriesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksMappingKeys;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplatesDataMapper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

class TreeBuilderFactory implements TreeBuilderFactoryInterface
{
    /**
     * @return NodeInterface
     */
    public function create(): NodeInterface
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('shopConfiguration');

        $rootNode
            ->children()
                ->arrayNode('modules')->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('path')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('version')
                            ->end()
                            ->scalarNode('configured')
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
                            ->arrayNode(TemplatesDataMapper::MAPPING_KEY)
                                ->normalizeKeys(false)->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ControllersDataMapper::MAPPING_KEY)
                                ->normalizeKeys(false)->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(SmartyPluginDirectoriesDataMapper::MAPPING_KEY)
                                ->normalizeKeys(false)->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(EventsDataMapper::MAPPING_KEY)
                                ->normalizeKeys(false)->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ClassesWithoutNamespaceDataMapper::MAPPING_KEY)
                                ->normalizeKeys(false)->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(TemplateBlocksDataMapper::MAPPING_KEY)
                                ->normalizeKeys(false)->arrayPrototype()
                                    ->children()
                                        ->scalarNode(TemplateBlocksMappingKeys::BLOCK_NAME)
                                        ->end()
                                        ->scalarNode(TemplateBlocksMappingKeys::POSITION)
                                        ->end()
                                        ->scalarNode(TemplateBlocksMappingKeys::THEME)
                                        ->end()
                                        ->scalarNode(TemplateBlocksMappingKeys::SHOP_TEMPLATE_PATH)
                                        ->end()
                                        ->scalarNode(TemplateBlocksMappingKeys::MODULE_TEMPLATE_PATH)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode(ModuleSettingsDataMapper::MAPPING_KEY)
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
                    ->end()
                ->end()
                ->arrayNode('moduleChains')->normalizeKeys(false)
                    ->children()
                        ->arrayNode('classExtensions')
                            ->arrayPrototype()
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
