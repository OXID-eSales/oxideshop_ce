<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

/**
 * Class AccountReviewController
 *
 * @package OxidEsales\EshopCommunity\Application\Controller
 */
class AccountReviewController extends AccountController
{
    protected $itemsPerPage = 10;

    protected $_sThisTemplate = 'page/account/reviews.tpl';

    /**
     * Redirect to My Account, if validation does not pass.
     */
    public function init()
    {
        if (!$this->isUserAllowedToManageOwnReviews() || !$this->getUser()) {
            $this->redirectToAccountDashboard();
        }

        parent::init();
    }

    /**
     * Returns Review List
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel|null
     */
    public function getReviewList()
    {
        $currentPage    = $this->getActPage();
        $itemsPerPage   = $this->getItemsPerPage();
        $offset         = $currentPage * $itemsPerPage;

        $userId = $this->getUser()->getId();

        $reviewModel = oxNew(Review::class);
        $reviewAndRatingList = $reviewModel->getReviewAndRatingListByUserId($userId);

        return $this->getPaginatedReviewAndRatingList(
            $reviewAndRatingList,
            $itemsPerPage,
            $offset
        );
    }

    /**
     * Delete review and rating, which belongs to the active user.
     */
    public function deleteReviewAndRating()
    {
        if ($this->getSession()->checkSessionChallenge()) {
            $this->deleteReview();
            $this->deleteRating();
        }
    }

    /**
     * Deletes Review.
     */
    private function deleteReview()
    {
        $userReviewFacade = $this->getUserReviewFacade();
        $userId = $this->getUser()->getId();

        $reviewId = $this->getReviewIdFromRequest();
        if ($reviewId) {
            $userReviewFacade->deleteReview($reviewId, $userId);
        }
    }

    /**
     * Deletes Rating.
     */
    private function deleteRating()
    {
        $userReviewAndRatingFacade = $this->getUserRatingFacade();
        $userId = $this->getUser()->getId();

        $ratingId = $this->getRatingIdFromRequest();
        if ($ratingId) {
            $userRatingFacade->deleteRating($ratingId, $userId);
        }
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        return [
            [
                'title' => $this->getTranslatedString('MY_ACCOUNT'),
                'link'  => $this->getMyAccountPageUrl(),
            ],
            [
                'title' => $this->getTranslatedString('MY_REVIEWS'),
                'link'  => $this->getLink(),
            ],
        ];
    }

    /**
     * Generates the pagination.
     *
     * @return \stdClass
     */
    public function getPageNavigation()
    {
        $this->_iCntPages       = $this->getPagesCount();
        $this->_oPageNavigation = $this->generatePageNavigation();

        return $this->_oPageNavigation;
    }

    /**
     * Return how many items will be displayed per page.
     *
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Retrieve the Review id from the request
     *
     * @return Review
     */
    private function getReviewIdFromRequest()
    {
        $request = oxNew(Request::class);

        return $request->getRequestEscapedParameter('reviewId');
    }

    /**
     * Retrieve the Rating id from the request
     *
     * @return Review
     */
    private function getRatingIdFromRequest()
    {
        $request = oxNew(Request::class);

        return $request->getRequestEscapedParameter('ratingId');
    }

    /**
     * Get actual page number.
     *
     * @return int
     */
    public function getActPage()
    {
        $lastPage = $this->getPagesCount();
        $currentPage = parent::getActPage();

        if ($currentPage >= $lastPage) {
            $currentPage = $lastPage - 1;
        }

        return $currentPage;
    }

    /**
     * Redirect to My Account dashboard
     */
    private function redirectToAccountDashboard()
    {
        Registry::getUtils()->redirect(
            $this->getMyAccountPageUrl(),
            true,
            302
        );
        exit(0);
    }

    /**
     * Returns pages count.
     *
     * @return int
     */
    private function getPagesCount()
    {
        return ceil($this->getReviewAndRatingItemsCount() / $this->getItemsPerPage());
    }

    /**
     * Returns My Account page url.
     *
     * @return string
     */
    private function getMyAccountPageUrl()
    {
        $selfLink = $this->getViewConfig()->getSelfLink();

        return Registry::getSeoEncoder()->getStaticUrl($selfLink . 'cl=account');
    }

    /**
     * Returns translated string.
     *
     * @param string $string
     *
     * @return string
     */
    private function getTranslatedString($string)
    {
        $languageId = Registry::getLang()->getBaseLanguage();

        return Registry::getLang()->translateString(
            $string,
            $languageId,
            false
        );
    }

    /**
     * Paginate ReviewAndRating list.
     *
     * @param ArrayCollection $reviewAndRatingList
     * @param int             $itemsCount
     * @param int             $offset
     *
     * @return ArrayCollection
     */
    private function getPaginatedReviewAndRatingList(ArrayCollection $reviewAndRatingList, $itemsCount, $offset)
    {
        return new ArrayCollection($reviewAndRatingList->slice($offset, $itemsCount));
    }
}
