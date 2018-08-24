<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Configuration;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class ConfigurationSettingDao implements ConfigurationSettingDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * ConfigurationSettingDao constructor.
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     * @param ContextInterface             $context
     */
    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory, ContextInterface $context)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context = $context;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param int    $shopId
     */
    public function save(string $name, $value, int $shopId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxconfig')
            ->values([
                'oxid'          => 'uuid()',
                'oxshopid'      => ':shopId',
                'oxvarname'     => ':name',
                'oxvarvalue'    => 'encode(:value, :key)',
            ])
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
                'value'     => $this->serializeValue($value),
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param string $name
     * @param int    $shopId
     *
     * @return mixed
     */
    public function get(string $name, int $shopId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('decode(oxvarvalue, :key) as value')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $result = $queryBuilder->execute()->fetch();

        return $this->unserializeValue($result['value']);
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function serializeValue($value): string
    {
        return serialize($value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    private function unserializeValue(string $value)
    {
        return unserialize($value);
    }
}
