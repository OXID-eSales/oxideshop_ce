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
use OxidEsales\Eshop\Internal\Service\UserReviewAndRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Exception\ReviewAndRatingObjectTypeException;

/**
 * Class UserReviewAndRatingFacade
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
class UserReviewAndRatingFacade implements UserReviewAndRatingFacadeInterface
{
    /**
     * @var UserReviewAndRatingServiceInterface
     */
    private $userReviewAndRatingService;

    /**
     * UserReviewAndRatingFacade constructor.
     *
     * @param UserReviewAndRatingServiceInterface $userReviewAndRatingService
     */
    public function __construct(UserReviewAndRatingServiceInterface $userReviewAndRatingService)
    {
        $this->userReviewAndRatingService = $userReviewAndRatingService;
    }

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId)
    {
        $reviewAndRatingList = $this
            ->userReviewAndRatingService
            ->getReviewAndRatingList($userId);

        $this->prepareRatingAndReviewPropertiesData($reviewAndRatingList);

        return $reviewAndRatingList;
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
