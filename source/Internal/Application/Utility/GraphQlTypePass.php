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
     * @var string $graphQlSchemaFactoryId
     */
    protected $graphQlSchemaFactoryId;
    /**
     * @var string $graphQlPermissionsServiceId
     */
    protected $graphQlPermissionsServiceId;
    /**
     * @var string $graphqlQueryTag The tag string for query types
     */
    protected $graphqlQueryTag;

    /**
     * @var string $graphqlQueryTag The tag string for query types
     */
    protected $graphqlMutationTag;

    /**
     * @var string $graphqlPermissionsTag The tag permission providers
     */
    protected $graphqlPermissionsTag;

    /**
     * GraphQlTypePass constructor.
     *
     * @param string $graphQlSchemaFactoryId
     * @param string $graphQlPermissionsServiceId
     * @param string $graphqlQueryTag
     * @param string $graphqlMutationTag
     * @param string $graphqlPermissionsTag
     */
    public function __construct(
        $graphQlSchemaFactoryId = 'OxidEsales\GraphQl\Framework\SchemaFactoryInterface',
        $graphQlPermissionsServiceId = 'OxidEsales\GraphQl\Service\PermissionsServiceInterface',
        $graphqlQueryTag = 'graphql_query_provider',
        $graphqlMutationTag = 'graphql_mutation_provider',
        $graphqlPermissionsTag = 'graphql_permissions_provider'
    ) {
        $this->graphQlSchemaFactoryId = $graphQlSchemaFactoryId;
        $this->graphQlPermissionsServiceId = $graphQlPermissionsServiceId;
        $this->graphqlQueryTag = $graphqlQueryTag;
        $this->graphqlMutationTag = $graphqlMutationTag;
        $this->graphqlPermissionsTag = $graphqlPermissionsTag;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return null
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->graphQlSchemaFactoryId) &&
            !$container->hasAlias($this->graphQlSchemaFactoryId)) {
            return;
        }

        $graphQlSchemaFactoryDefinition = $container->findDefinition($this->graphQlSchemaFactoryId);

        foreach ($container->findTaggedServiceIds($this->graphqlQueryTag) as $id => $type) {
            $graphQlSchemaFactoryDefinition->addMethodCall('addQueryProvider', [new Reference($id)]);
        }
        foreach ($container->findTaggedServiceIds($this->graphqlMutationTag) as $id => $type) {
            $graphQlSchemaFactoryDefinition->addMethodCall('addMutationProvider', [new Reference($id)]);
        }

        if (!$container->hasDefinition($this->graphQlPermissionsServiceId) &&
            !$container->hasAlias($this->graphQlPermissionsServiceId)) {
            return;
        }
        $permissionsService = $container->findDefinition($this->graphQlPermissionsServiceId);
        foreach ($container->findTaggedServiceIds($this->graphqlPermissionsTag) as $id => $type) {
            $permissionsService->addMethodCall('addPermissionsProvider', [new Reference($id)]);
        }
        return;
    }
}
