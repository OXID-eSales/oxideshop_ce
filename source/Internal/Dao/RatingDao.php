<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Internal\DataObject\Rating;
use OxidEsales\Eshop\Internal\Dao\RatingDaoInterface;

/**
 * Class RatingDao
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Dao
 */
class RatingDao implements RatingDaoInterface
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
     * Returns User Ratings.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatingsByUserId($userId)
    {
        $ratingsData = $this->getRatingsFromDatabaseByUserId($userId);

        return $this->mapRatings($ratingsData);
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
        $ratingsData = $this->getRatingsFromDatabaseByProductId($productId);

        return $this->mapRatings($ratingsData);
    }

    /**
     * Returns User rating data from database.
     *
     * @param string $userId
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface
     */
    private function getRatingsFromDatabaseByUserId($userId)
    {
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);

        $query = '
              SELECT 
                  *
              FROM 
                  oxratings 
              WHERE 
                  oxuserid = ? 
              ORDER BY 
                  oxtimestamp DESC
        ';

        return $this->database->select($query, [$userId]);
    }

    /**
     * Returns Ratings data for a product from database.
     *
     * @param string $productId
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface
     */
    private function getRatingsFromDatabaseByProductId($productId)
    {
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);

        $query = '
              SELECT 
                  *
              FROM 
                  oxratings 
              WHERE 
                  oxobjectid = ?
                  AND oxtype = "oxarticle" 
              ORDER BY 
                  oxtimestamp DESC
        ';

        return $this->database->select($query, [$productId]);
    }

    /**
     * Maps rating data from database to Ratings Collection.
     *
     * @param ResultSetInterface $ratingsData
     *
     * @return ArrayCollection
     */
    private function mapRatings($ratingsData)
    {
        $ratings = new ArrayCollection();

        foreach ($ratingsData as $ratingData) {
            $ratings->add($this->mapRating($ratingData));
        }

        return $ratings;
    }

    /**
     * Maps data from database to Rating.
     *
     * @param array $ratingData
     *
     * @return Rating
     */
    private function mapRating($ratingData)
    {
        $rating = new Rating();
        $rating
            ->setId($ratingData['OXID'])
            ->setRating($ratingData['OXRATING'])
            ->setObjectId($ratingData['OXOBJECTID'])
            ->setUserId($ratingData['OXUSERID'])
            ->setType($ratingData['OXTYPE'])
            ->setCreatedAt($ratingData['OXTIMESTAMP']);

        return $rating;
    }
}
