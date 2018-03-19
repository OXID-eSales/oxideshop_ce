<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Internal\DataObject\ProductRating;
use OxidEsales\EshopCommunity\Internal\Exception\InvalidObjectIdDaoException;

/**
 * Class ProductRatingDao
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Dao
 */
class ProductRatingDao implements ProductRatingDaoInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * RatingDao constructor.
     *
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @param ProductRating $productRating
     */
    public function update(ProductRating $productRating)
    {
        $query = '
            UPDATE
                oxarticles
            SET
                OXRATING = ?,
                OXRATINGCNT = ?
            WHERE 
                OXID = ?
        ';

        $this->database->execute($query, [
            $productRating->getRatingAverage(),
            $productRating->getRatingCount(),
            $productRating->getProductId(),
        ]);
    }

    /**
     * @param string $productId
     *
     * @return ProductRating
     */
    public function getProductRatingById($productId)
    {
        $this->validateProductId($productId);

        $productRatingData = $this->getProductRatingDataById($productId);

        return $this->mapProductRating($productRatingData);
    }

    /**
     * @param string $productId
     *
     * @return array
     */
    private function getProductRatingDataById($productId)
    {
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);

        $query = '
              SELECT
                  OXID,
                  OXRATING,
                  OXRATINGCNT
              FROM 
                  oxarticles 
              WHERE 
                  oxid = ? 
              LIMIT 1
        ';

        return $this->database->getRow($query, [$productId]);
    }

    /**
     * @param array $productRatingData
     *
     * @return ProductRating
     */
    private function mapProductRating($productRatingData)
    {
        $productRating = new ProductRating();
        $productRating
            ->setProductId($productRatingData['OXID'])
            ->setRatingAverage($productRatingData['OXRATING'])
            ->setRatingCount($productRatingData['OXRATINGCNT']);

        return $productRating;
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
