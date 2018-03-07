<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Internal\Dao\RatingDao;
use OxidEsales\Eshop\Internal\Dao\ReviewDao;
use OxidEsales\Eshop\Internal\Facade\UserReviewAndRatingFacade;
use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingService;
use OxidEsales\Eshop\Internal\Service\UserRatingService;
use OxidEsales\Eshop\Internal\Service\UserReviewAndRatingService;
use OxidEsales\Eshop\Internal\Service\UserReviewService;
use OxidEsales\Eshop\Internal\Service\UserService;

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
        $userReviewAndRatingFacade = $this->getUserReviewAndRatingFacade();

        return $userReviewAndRatingFacade->getReviewAndRatingList(
            $userId,
            $itemsPerPage,
            $offset
        );
    }

    /**
     * Delete an review and rating, which belongs to the active user.
     *
     * @return string
     */
    public function deleteReviewAndRating()
    {
        if ($this->getSession()->checkSessionChallenge()) {
            $this->deleteReview();
            $this->deleteRating();
        }

        return $this->getReviewListUrlPath();
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
     * @return string
     */
    private function getReviewListUrlPath()
    {
        $lastPage = $this->getPagesCount();
        $currentPage = $this->getActPage();

        if ($currentPage >= $lastPage) {
            $currentPage = $lastPage - 1;
        }

        return $currentPage > 0 ? 'account_reviewlist?pgNr=' . $currentPage : 'account_reviewlist';
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
        return ceil($this->getReviewItemsCnt() / $this->getItemsPerPage());
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
     * Returns UserReviewAndRatingFacade.
     *
     * @return UserReviewAndRatingFacade
     */
    private function getUserReviewAndRatingFacade()
    {
        return new UserReviewAndRatingFacade(
            $this->getUserReviewAndRatingService()
        );
    }

    /**
     * Returns UserReviewAndRatingService.
     *
     * @return UserReviewAndRatingService
     */
    private function getUserReviewAndRatingService()
    {
        return new UserReviewAndRatingService(
            $this->getUserReviewService(),
            $this->getUserRatingService(),
            $this->getReviewAndRatingMergingService()
        );
    }

    /**
     * Returns UserReviewService.
     *
     * @return UserReviewService
     */
    private function getUserReviewService()
    {
        return new UserReviewService(
            new ReviewDao(DatabaseProvider::getDb())
        );
    }

    /**
     * Returns UserRatingService.
     *
     * @return UserRatingService
     */
    private function getUserRatingService()
    {
        return new UserRatingService(
            new RatingDao(DatabaseProvider::getDb())
        );
    }

    /**
     * Returns ReviewAndRatingMergingService.
     *
     * @return ReviewAndRatingMergingService
     */
    private function getReviewAndRatingMergingService()
    {
        return new ReviewAndRatingMergingService();
    }
}
