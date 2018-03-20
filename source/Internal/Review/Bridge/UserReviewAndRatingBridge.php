<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\RecommendationList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Review\ViewDataObject\ReviewAndRating;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewAndRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Review\Exception\ReviewAndRatingObjectTypeException;

/**
 * Class UserReviewAndRatingBridge
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Bridge
 */
class UserReviewAndRatingBridge implements UserReviewAndRatingBridgeInterface
{
    /**
     * @var UserReviewAndRatingServiceInterface
     */
    private $userReviewAndRatingService;

    /**
     * UserReviewAndRatingBridge constructor.
     *
     * @param UserReviewAndRatingServiceInterface $userReviewAndRatingService
     */
    public function __construct(UserReviewAndRatingServiceInterface $userReviewAndRatingService)
    {
        $this->userReviewAndRatingService = $userReviewAndRatingService;
    }

    /**
     * Get number of reviews by given user.
     *
     * @param string $userId
     *
     * @return int
     */
    public function getReviewAndRatingListCount($userId)
    {
        return $this
            ->userReviewAndRatingService
            ->getReviewAndRatingListCount($userId);
    }

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     *
     * @return array
     */
    public function getReviewAndRatingList($userId)
    {
        $reviewAndRatingList = $this
            ->userReviewAndRatingService
            ->getReviewAndRatingList($userId);

        $this->prepareRatingAndReviewPropertiesData($reviewAndRatingList);

        return $reviewAndRatingList->toArray();
    }

    /**
     * Prepare RatingAndReview properties data.
     *
     * @param ArrayCollection $reviewAndRatingList
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
     * @return string
     */
    private function getObjectTitle($type, $objectId)
    {
        $objectModel = $this->getObjectModel($type);
        $objectModel->load($objectId);

        $fieldName = $this->getObjectTitleFieldName($type);
        $field = $objectModel->$fieldName;

        return $field->value;
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
