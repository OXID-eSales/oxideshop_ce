<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Dao\InvalidObjectIdDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\ProductRatingDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

class ProductRatingDao implements ProductRatingDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * @var ProductRatingDataMapperInterface
     */
    private $productRatingMapper;

    /**
     * @param QueryBuilderFactoryInterface     $queryBuilderFactory
     * @param ProductRatingDataMapperInterface $productRatingMapper
     */
    public function __construct(
        QueryBuilderFactoryInterface    $queryBuilderFactory,
        ProductRatingDataMapperInterface           $productRatingMapper
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->productRatingMapper = $productRatingMapper;
    }

    /**
     * @param ProductRating $productRating
     */
    public function update(ProductRating $productRating)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->update('oxarticles')
            ->set('OXRATING', ':OXRATING')
            ->set('OXRATINGCNT', ':OXRATINGCNT')
            ->where('OXID = :OXID')
            ->setParameters($this->productRatingMapper->getData($productRating));

        $queryBuilder->execute();
    }

    /**
     * @param string $productId
     *
     * @return ProductRating
     * @throws InvalidObjectIdDaoException
     */
    public function getProductRatingById($productId)
    {
        $this->validateProductId($productId);

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select([
                'OXID',
                'OXRATING',
                'OXRATINGCNT'
            ])
            ->from('oxarticles')
            ->where('oxid = :productId')
            ->setMaxResults(1)
            ->setParameter('productId', $productId);

        return $this->productRatingMapper->map(
            new ProductRating(),
            $queryBuilder->execute()->fetch()
        );
    }

    /**
     * @param string $productId
     *
     * @throws InvalidObjectIdDaoException
     */
    private function validateProductId($productId)
    {
        if (empty($productId) || !is_string($productId)) {
            throw new InvalidObjectIdDaoException();
        }
    }
}
