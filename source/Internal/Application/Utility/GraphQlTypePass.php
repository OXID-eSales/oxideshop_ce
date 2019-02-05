<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Utility;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class GraphQlTypePass
 *
 * This class configures the query/mutation type factories used
 * to build the GraphQL schema. It collects all appropriately
 * tagged queries / mutations and places them into the factories.
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Utility
 */
class GraphQlTypePass implements CompilerPassInterface
{

    /**
     * @var string $queryTypeFactoryId The container key for the query
     *                                 type factory
     */
    protected $queryTypeFactoryId;
    /**
     * @var string $queryTypeTag The tag string for query types
     */
    protected $queryTypeTag;
    /**
     * @var string $mutationTypeFactoryId The container key for the
     *                                    mutation type factory
     */
    protected $mutationTypeFactoryId;
    /**
     * @var string $mutationTypeTag The tag string for the mutation types
     */
    protected $mutationTypeTag;

    /**
     * GraphQlTypePass constructor.
     *
     * @param string $queryTypeFactoryId
     * @param string $queryTypeTag
     * @param string $mutationTypeFactoryId
     * @param string $mutationTypeTag
     */
    public function __construct(
        $queryTypeFactoryId = 'query_type_factory',
        $queryTypeTag = 'graphql_query_type',
        $mutationTypeFactoryId = 'mutation_type_factory',
        $mutationTypeTag = 'graphql_mutation_type'
    ) {
        $this->queryTypeFactoryId = $queryTypeFactoryId;
        $this->queryTypeTag = $queryTypeTag;
        $this->mutationTypeFactoryId = $mutationTypeFactoryId;
        $this->mutationTypeTag = $mutationTypeTag;
    }

    /**
     * @param ContainerBuilder $container
     * @return null
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->queryTypeFactoryId) && !$container->hasAlias($this->queryTypeFactoryId)) {
            return;
        }

        if (!$container->hasDefinition($this->mutationTypeFactoryId) && !$container->hasAlias($this->mutationTypeFactoryId)) {
            return;
        }

        $queryTypeFactoryDefinition = $container->findDefinition($this->queryTypeFactoryId);
        $mutationTypeFactoryDefinition = $container->findDefinition($this->mutationTypeFactoryId);

        foreach ($container->findTaggedServiceIds($this->queryTypeTag, true) as $id => $type) {
            $queryTypeFactoryDefinition->addMethodCall('addSubType', array(new Reference($id)));
        }
        foreach ($container->findTaggedServiceIds($this->mutationTypeTag, true) as $id => $type) {
            $mutationTypeFactoryDefinition->addMethodCall('addSubType', array(new Reference($id)));
        }
    }
}
