<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Application\Controller\AccountController;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Class AccountReviewController
 *
 * @package OxidEsales\EshopCommunity\Application\Controller
 */
class AccountReviewController extends AccountController
{
    protected $itemsPerPage = 10;

    protected $_sThisTemplate = 'page/account/productreviews.tpl';

    /**
     * Redirect to My Account, if validation does not pass.
     */
    public function init()
    {
        if (!$this->isUserAllowedToManageHisProductReviews() || !$this->getUser()) {
            $this->redirectToAccountDashboard();
        }

        parent::init();
    }

    /**
     * Returns Product Review List
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel|null
     */
    public function getProductReviewList()
    {
        $currentPage    = $this->getActPage();
        $itemsPerPage   = $this->getItemsPerPage();
        $offset         = $currentPage * $itemsPerPage;

        $userId = $this->getUser()->getId();

        $review = oxNew(Review::class);
        $productReviewList = $review->getProductReviewsByUserId($userId, $offset, $itemsPerPage);

        return $productReviewList;
    }

    /**
     * Delete a product review and rating, which belongs to the active user.
     *
     * @return string
     */
    public function deleteProductReviewAndRating()
    {
        $articleId  = $this->getArticleIdFromRequest();
        $reviewId   = $this->getReviewIdFromRequest();

        if ($this->getSession()->checkSessionChallenge()) {
            $db = DatabaseProvider::getDb();
            $db->startTransaction();

            try {
                $this->deleteProductRating($articleId);
                $this->deleteProductReview($reviewId);

                $db->commitTransaction();
            } catch (\Exception $exception) {
                $db->rollbackTransaction();

                Registry::getUtilsView()->addErrorToDisplay('ERROR_PRODUCT_REVIEW_AND_RATING_NOT_DELETED');
            }
        }

        return $this->getRedirectUrlAfterReviewDeleting();
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
                'title' => $this->getTranslatedString('MY_PRODUCT_REVIEWS'),
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
     * @return string
     */
    private function getRedirectUrlAfterReviewDeleting()
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
        return ceil($this->getProductReviewItemsCnt() / $this->getItemsPerPage());
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
     * Delete a given review for a given user.
     *
     * @param int $articleId
     */
    private function deleteProductRating($articleId)
    {
        $shopId = Registry::getConfig()->getShopId();
        $userId = $this->getUser()->getId();
        $rating = oxNew(Rating::class);

        $ratingId = $rating->getProductRatingByUserId($articleId, $userId, $shopId);

        if ($ratingId) {
            $rating->delete($ratingId);
        }
    }

    /**
     * Delete a given review for a given user.
     *
     * @param   int $reviewId
     *
     * @throws \Exception
     */
    private function deleteProductReview($reviewId)
    {
        $review = oxNew(Review::class);

        if (!$review->load($reviewId)) {
            throw new \Exception('Review doesn\'t exist.');
        }

        if (!$this->isReviewProduct($review)) {
            throw new \Exception('It\'s not a product review.');
        }

        if (!$this->doesReviewBelongToCurrentUser($review)) {
            throw new \Exception('Review doesn\' belong to logged user.');
        }

        $review->delete($reviewId);
    }

    /**
     * @param Review $review
     *
     * @return bool
     */
    private function isReviewProduct($review)
    {
        return 'oxarticle' === $review->getObjectType();
    }

    /**
     * @param Review $review
     * @return bool
     */
    private function doesReviewBelongToCurrentUser($review)
    {
        $currentUser  = $this->getUser();
        $reviewUser   = $review->getUser();

        return $currentUser->getId() === $reviewUser->getId();
    }

    /**
     * Retrieve the article ID from the request
     *
     * @return string
     */
    private function getArticleIdFromRequest()
    {
        $request = oxNew(Request::class);

        return $request->getRequestEscapedParameter('aId');
    }

    /**
     * Retrieve the review ID from the request
     *
     * @return string
     */
    private function getReviewIdFromRequest()
    {
        $request = oxNew(Request::class);

        return $request->getRequestEscapedParameter('reviewId');
    }
}
