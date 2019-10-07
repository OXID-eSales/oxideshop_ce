<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\RatingDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

class RatingDao implements RatingDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * @var RatingDataMapperInterface
     */
    private $ratingDataMapper;

    /**
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     * @param RatingDataMapperInterface    $ratingDataMapper
     */
    public function __construct(
        QueryBuilderFactoryInterface    $queryBuilderFactory,
        RatingDataMapperInterface           $ratingDataMapper
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->ratingDataMapper = $ratingDataMapper;
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
            $ratings->add($this->ratingDataMapper->map($rating, $ratingData));
        }

        return $ratings;
    }
}
