<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;

/**
 * @internal
 */
class ReviewDao implements ReviewDaoInterface
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
     * Returns User Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviewsByUserId($userId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('r.*')
            ->from('oxreviews', 'r')
            ->where('r.oxuserid = :userId')
            ->orderBy('r.oxcreate', 'DESC')
            ->setParameter('userId', $userId);

        return $this->mapReviews($queryBuilder->execute()->fetchAll());
    }

    /**
     * @param Review $review
     */
    public function delete(Review $review)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxreviews')
            ->where('oxid = :id')
            ->setParameter('id', $review->getId())
            ->execute();
    }

    /**
     * Maps rating data from database to Reviews Collection.
     *
     * @param array $reviewsData
     *
     * @return ArrayCollection
     */
    private function mapReviews($reviewsData)
    {
        $reviews = new ArrayCollection();

        foreach ($reviewsData as $reviewData) {
            $review = new Review();
            $reviews[] = $this->mapper->map($review, $reviewData);
        }

        return $reviews;
    }
}
