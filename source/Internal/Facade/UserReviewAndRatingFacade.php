<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Facade;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\RecommendationList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Internal\Facade\UserReviewAndRatingFacadeInterface;
use OxidEsales\Eshop\Internal\ViewDataObject\ReviewAndRating;
use OxidEsales\EshopCommunity\Internal\Exception\ReviewAndRatingObjectTypeException;
use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserRatingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewServiceInterface;

/**
 * Class UserReviewAndRatingFacade
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
class UserReviewAndRatingFacade implements UserReviewAndRatingFacadeInterface
{
    /**
     * @var UserReviewServiceInterface
     */
    private $userReviewService;

    /**
     * @var UserRatingServiceInterface
     */
    private $userRatingService;

    /**
     * @var ReviewAndRatingMergingServiceInterface
     */
    private $reviewAndRatingMergingService;

    /**
     * UserReviewAndRatingFacade constructor.
     *
     * @param UserReviewServiceInterface             $userReviewService
     * @param UserRatingServiceInterface             $userRatingService
     * @param ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
     */
    public function __construct(
        UserReviewServiceInterface $userReviewService,
        UserRatingServiceInterface $userRatingService,
        ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
    ) {
        $this->userReviewService = $userReviewService;
        $this->userRatingService = $userRatingService;
        $this->reviewAndRatingMergingService = $reviewAndRatingMergingService;
    }

    /**
     * @param string $reviewId
     * @param string $userId
     */
    public function deleteReview($reviewId, $userId)
    {
    }

    /**
     * @param string $ratingId
     * @param string $userId
     */
    public function deleteRating($ratingId, $userId)
    {
    }

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     * @param int    $itemsPerPage
     * @param int    $offset
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId, $itemsPerPage, $offset)
    {
        $reviewAndRatingList = $this->getMergedReviewAndRatingList($userId);
        $reviewAndRatingList = $this->sortReviewAndRatingList($reviewAndRatingList);
        $reviewAndRatingList = $this->paginateReviewAndRatingList(
            $reviewAndRatingList,
            $itemsPerPage,
            $offset
        );

        $this->prepareRatingAndReviewPropertiesData($reviewAndRatingList);

        return $reviewAndRatingList;
    }

    /**
     * Returns merged Rating and Review.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    private function getMergedReviewAndRatingList($userId)
    {
        $reviews = $this->userReviewService->getReviews($userId);
        $ratings = $this->userRatingService->getRatings($userId);

        return $this
            ->reviewAndRatingMergingService
            ->mergeReviewAndRating($reviews, $ratings);
    }

    /**
     * Sorts ReviewAndRating list.
     *
     * @param ArrayCollection $reviewAndRatingList
     *
     * @return ArrayCollection
     */
    private function sortReviewAndRatingList(ArrayCollection $reviewAndRatingList)
    {
        $iterator = $reviewAndRatingList->getIterator();

        $iterator->uasort(function ($first, $second) {
            return $first->getCreatedAt() < $second->getCreatedAt() ? 1 : -1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * Paginate ReviewAndRating list.
     *
     * @param ArrayCollection $reviewAndRatingList
     * @param int             $itemsCount
     * @param int             $offset
     *
     * @return mixed
     */
    private function paginateReviewAndRatingList(ArrayCollection $reviewAndRatingList, $itemsCount, $offset)
    {
        return $reviewAndRatingList->slice($offset, $itemsCount);
    }

    /**
     * Prepare RatingAndReview properties data.
     *
     * @param array $reviewAndRatingList
     */
    private function prepareRatingAndReviewPropertiesData($reviewAndRatingList)
    {
        foreach ($reviewAndRatingList as $reviewAndRating) {
            $this->setObjectTitleToReviewAndRating($reviewAndRating);
            $this->formatReviewText($reviewAndRating);
            $this->formatReviewAndRatingDate($reviewAndRating);
        }
    }

    /**
     * Formats Review text.
     *
     * @param ReviewAndRating $reviewAndRating
     */
    private function formatReviewText(ReviewAndRating $reviewAndRating)
    {
        $preparedText = htmlspecialchars($reviewAndRating->getReviewText());

        $reviewAndRating->setReviewText($preparedText);
    }

    /**
     * Formats ReviewAndRating date.
     *
     * @param ReviewAndRating $reviewAndRating
     */
    private function formatReviewAndRatingDate(ReviewAndRating $reviewAndRating)
    {
        $formattedDate = Registry::getUtilsDate()->formatDBDate($reviewAndRating->getCreatedAt());

        $reviewAndRating->setCreatedAt($formattedDate);
    }

    /**
     * Sets object title to ReviewAndRating.
     *
     * @param ReviewAndRating $reviewAndRating
     */
    private function setObjectTitleToReviewAndRating(ReviewAndRating $reviewAndRating)
    {
        $title = $this->getObjectTitle(
            $reviewAndRating->getObjectType(),
            $reviewAndRating->getObjectId()
        );

        $reviewAndRating->setObjectTitle($title);
    }

    /**
     * Returns object title.
     *
     * @param string $type
     * @param string $objectId
     *
     * @return mixed
     */
    private function getObjectTitle($type, $objectId)
    {
        $objectModel = $this->getObjectModel($type);
        $objectModel->load($objectId);

        $fieldName = $this->getObjectTitleFieldName($type);

        return $objectModel->$fieldName->value;
    }

    /**
     * Returns object model.
     *
     * @param string $type
     *
     * @return Article|RecommendationList
     * @throws ReviewAndRatingObjectTypeException
     */
    private function getObjectModel($type)
    {
        if ($type === 'oxarticle') {
            $model = oxNew(Article::class);
        }

        if ($type === 'oxrecommlist') {
            $model = oxNew(RecommendationList::class);
        }

        if (!isset($model)) {
            throw new ReviewAndRatingObjectTypeException();
        }

        return $model;
    }

    /**
     * Returns field name of the object title.
     *
     * @param string $type
     *
     * @return string
     * @throws ReviewAndRatingObjectTypeException
     */
    private function getObjectTitleFieldName($type)
    {
        if ($type === 'oxarticle') {
            $fieldName = 'oxarticles__oxtitle';
        }

        if ($type === 'oxrecommlist') {
            $fieldName = 'oxrecommlists__oxtitle';
        }

        if (!isset($fieldName)) {
            throw new ReviewAndRatingObjectTypeException();
        }

        return $fieldName;
    }
}
