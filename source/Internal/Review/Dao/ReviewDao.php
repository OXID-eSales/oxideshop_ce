<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;

/**
 * Class ReviewDao
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Dao
 */
class ReviewDao implements ReviewDaoInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * ReviewDao constructor.
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
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
        $reviewsData = $this->getReviewsFromDatabaseByUserId($userId);

        return $this->mapReviews($reviewsData);
    }

    /**
     * @param Review $review
     */
    public function delete(Review $review)
    {
        $query = '
              DELETE 
              FROM 
                  oxreviews 
              WHERE 
                  oxid = ?
        ';

        $this->database->execute($query, [$review->getId()]);
    }

    /**
     * Returns User Reviews from database.
     *
     * @param string $userId
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface
     */
    private function getReviewsFromDatabaseByUserId($userId)
    {
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);

        $query = '
              SELECT 
                  *
              FROM 
                  oxreviews 
              WHERE 
                  oxuserid = ? 
              ORDER BY 
                  oxcreate DESC
        ';

        return $this->database->select($query, [$userId]);
    }

    /**
     * Maps rating data from database to Reviews Collection.
     *
     * @param ResultSetInterface $reviewsData
     *
     * @return ArrayCollection
     */
    private function mapReviews($reviewsData)
    {
        $reviews = new ArrayCollection();

        foreach ($reviewsData as $reviewData) {
            $reviews[] = $this->mapReview($reviewData);
        }

        return $reviews;
    }

    /**
     * Maps data from database to Review.
     *
     * @param array $reviewData
     *
     * @return Review
     */
    private function mapReview($reviewData)
    {
        $review = new Review();
        $review
            ->setId($reviewData['OXID'])
            ->setRating($reviewData['OXRATING'])
            ->setText($reviewData['OXTEXT'])
            ->setObjectId($reviewData['OXOBJECTID'])
            ->setUserId($reviewData['OXUSERID'])
            ->setType($reviewData['OXTYPE'])
            ->setCreatedAt($reviewData['OXTIMESTAMP']);

        return $review;
    }
}
