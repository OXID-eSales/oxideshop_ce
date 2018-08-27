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
                'oxvartype'     => ':type',
                'oxvarvalue'    => 'encode(:value, :key)',
            ])
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
                'type'      => $this->getValueType($value),
                'value'     => $this->encodeValue($value),
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
            ->select('decode(oxvarvalue, :key) as value, oxvartype as type')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $result = $queryBuilder->execute()->fetch();

        return $this->decodeValue(
            $result['type'],
            $result['value']
        );
    }

    /**
     * @param string $type
     * @param string $value
     *
     * @return mixed
     */
    private function decodeValue(string $type, string $value)
    {
        switch ($type) {
            case 'arr':
            case 'aarr':
                $decodedValue = unserialize($value);
                break;
            case 'bool':
                $decodedValue = ($value === 'true' || $value === '1');
                break;
            case 'int':
                $decodedValue = (int) $value;
                break;
            default:
                $decodedValue = $value;
        }

        return $decodedValue;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function encodeValue($value): string
    {
        $encodedValue = $value;

        if (is_array($value)) {
            $encodedValue = serialize($value);
        }

        if (is_bool($value)) {
            $encodedValue = $value === true ? '1' : '';
        }

        if (is_int($value)) {
            $encodedValue = (string) $value;
        }

        return $encodedValue;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getValueType($value): string
    {
        $type = 'str';

        if (is_array($value)) {
            $type = 'arr';
        }

        if (is_bool($value)) {
            $type = 'bool';
        }

        if (is_int($value)) {
            $type = 'int';
        }

        return $type;
    }
}
