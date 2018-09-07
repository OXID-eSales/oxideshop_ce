<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao;

use Doctrine\DBAL\Driver\Statement;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class ShopConfigurationSettingDao implements ShopConfigurationSettingDaoInterface
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
     * ShopConfigurationSettingDao constructor.
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     * @param ContextInterface             $context
     */
    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory, ContextInterface $context)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context = $context;
    }

    /**
     * @param ShopConfigurationSetting $shopConfigurationSettig
     */
    public function save(ShopConfigurationSetting $shopConfigurationSettig)
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
                'shopId'    => $shopConfigurationSettig->getShopId(),
                'name'      => $shopConfigurationSettig->getName(),
                'type'      => $this->getValueType($shopConfigurationSettig->getValue()),
                'value'     => $this->encodeValue($shopConfigurationSettig->getValue()),
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param string $name
     * @param int    $shopId
     * @return ShopConfigurationSetting
     */
    public function get(string $name, int $shopId): ShopConfigurationSetting
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('decode(oxvarvalue, :key) as value, oxvartype as type, oxvarname as name')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $value = $this->getShopCofigurationSettingValueFromDoctrineStatment(
            $queryBuilder->execute()
        );

        return new ShopConfigurationSetting(
            $shopId,
            $name,
            $value
        );
    }

    /**
     * @param Statement $statement
     * @return mixed
     */
    private function getShopCofigurationSettingValueFromDoctrineStatment(Statement $statement)
    {
        $result = $statement->fetch();

        if (false === $result) {
            throw new EntryDoesNotExistDaoException();
        }

        return $this->decodeValue(
            $result['type'],
            $result['value']
        );
    }

    /**
     * @param mixed $type
     * @param mixed $value
     *
     * @return mixed
     */
    private function decodeValue($type, $value)
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
