<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Definition;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksMappingKeys;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfigurationMappingKeys;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @internal
 */
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
                ->arrayNode('modules')
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
                            ->scalarNode('autoActive')
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
                            ->arrayNode(ModuleConfigurationMappingKeys::CLASS_EXTENSIONS)
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ModuleConfigurationMappingKeys::TEMPLATES)
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ModuleConfigurationMappingKeys::CONTROLLERS)
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ModuleConfigurationMappingKeys::SMARTY_PLUGIN_DIRECTORIES)
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ModuleConfigurationMappingKeys::EVENTS)
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ModuleConfigurationMappingKeys::CLASSES_WITHOUT_NAMESPACE)
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode(ModuleConfigurationMappingKeys::TEMPLATE_BLOCKS)
                                ->arrayPrototype()
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
                            ->arrayNode('settings')
                                ->children()
                                    ->arrayNode(ModuleSetting::SHOP_MODULE_SETTING)
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
                    ->end()
                ->end()
                ->arrayNode('moduleChains')
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
