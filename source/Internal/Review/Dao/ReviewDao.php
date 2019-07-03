<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Common\Dao\DynamicDataObjectDao;
use OxidEsales\EshopCommunity\Internal\Common\Dao\DynamicDataObjectDaoInterface;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;
use OxidEsales\EshopCommunity\Internal\Utility\LegacyServiceInterface;

/**
 * @internal
 */
class ReviewDao extends DynamicDataObjectDao implements ReviewDaoInterface, DynamicDataObjectDaoInterface
{
    const TABLE = 'oxreviews';

    /** @var LegacyServiceInterface $legacyService */
    private $legacyService;

    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * @var EntityMapperInterface
     */
    protected $mapper;

    /**
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     * @param EntityMapperInterface        $mapper
     */
    public function __construct(
        QueryBuilderFactoryInterface    $queryBuilderFactory,
        EntityMapperInterface           $mapper,
        LegacyServiceInterface          $legacyService
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->mapper = $mapper;
        $this->legacyService = $legacyService;
        $this->addObjectClass(Review::class);
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
            ->from($this::TABLE, 'r')
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
            ->delete($this::TABLE)
            ->where('oxid = :id')
            ->setParameter('id', $review->getId())
            ->execute();
    }

    public function save(Review $review) {

        if ($review->getId() == null) {
            return $this->insert($review);
        }
        else {
            return $this->update($review);
        }

    }

    private function insert(Review $review) {

        $queryBuilder = $this->queryBuilderFactory->create();
        $data = $this->mapper->getData($review);
        $data['OXID'] = $this->legacyService->getUniqueId();

        $queryBuilder->insert($this::TABLE);

        foreach ($data as $column => $value) {
            $queryBuilder
                ->setValue($column, ":$column")
                ->setParameter($column, $value);
        };
        $queryBuilder->execute();

        return $data['OXID'];

    }

    private function update(Review $review) {

        $queryBuilder = $this->queryBuilderFactory->create();
        $data = $this->mapper->getData($review);
        $queryBuilder
            ->update($this::TABLE);
        foreach ($data as $column => $value) {
            $queryBuilder
                ->set($column, ":$column")
                ->setParameter($column, $value);
        };
        $queryBuilder
            ->where($queryBuilder->expr()->eq('OXID', ":id"))
            ->setParameter('id', $data['OXID']);
        $queryBuilder->execute();

        return $data['OXID'];

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
            $review = $this->create();
            $reviews[] = $this->mapper->map($review, $reviewData);
        }

        return $reviews;
    }
}
