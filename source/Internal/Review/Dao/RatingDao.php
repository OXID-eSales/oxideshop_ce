<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Rating;

/**
 * @internal
 */
class RatingDao implements RatingDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * @var EntityMapperInterface
     */
    private $mapper;

    /**
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     * @param EntityMapperInterface        $mapper
     */
    public function __construct(
        QueryBuilderFactoryInterface    $queryBuilderFactory,
        EntityMapperInterface           $mapper
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->mapper = $mapper;
    }

    /**
     * Returns User Ratings.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatingsByUserId($userId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('r.*')
            ->from('oxratings', 'r')
            ->where('r.oxuserid = :userId')
            ->orderBy('r.oxtimestamp', 'DESC')
            ->setParameter('userId', $userId);

        return $this->mapRatings($queryBuilder->execute()->fetchAll());
    }

    /**
     * @param Rating $rating
     */
    public function delete(Rating $rating)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxratings')
            ->where('oxid = :id')
            ->setParameter('id', $rating->getId())
            ->execute();
    }

    /**
     * Returns Ratings for a product.
     *
     * @param string $productId
     *
     * @return ArrayCollection
     */
    public function getRatingsByProductId($productId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('r.*')
            ->from('oxratings', 'r')
            ->where('r.oxobjectid = :productId')
            ->andWhere('r.oxtype = :productType')
            ->orderBy('r.oxtimestamp', 'DESC')
            ->setParameters(
                [
                    'productId'     => $productId,
                    'productType'   => 'oxarticle',
                ]
            );

        return $this->mapRatings($queryBuilder->execute()->fetchAll());
    }

    /**
     * Maps rating data from database to Ratings Collection.
     *
     * @param array $ratingsData
     *
     * @return ArrayCollection
     */
    private function mapRatings($ratingsData)
    {
        $ratings = new ArrayCollection();

        foreach ($ratingsData as $ratingData) {
            $rating = new Rating();
            $ratings->add($this->mapper->map($rating, $ratingData));
        }

        return $ratings;
    }
}
