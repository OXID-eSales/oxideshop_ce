<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Admin article review manager.
 * Collects customer review about article data. There ir possibility to update
 * review text or delete it.
 * Admin Menu: Manage Products -> Articles -> Review.
 */
class ArticleReview extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads selected article review information, returns name of template
     * file "article_review.tpl".
     *
     * @return string
     */
    public function render()
    {
        $config = $this->getConfig();

        parent::render();

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $this->_aViewData["edit"] = $article;

        $articleId = $this->getEditObjectId();
        $reviewId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('rev_oxid');
        if (isset($articleId) && $articleId != "-1") {
            // load object
            $article->load($articleId);

            if ($article->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            $reviewList = $this->_getReviewList($article);

            foreach ($reviewList as $review) {
                if ($review->oxreviews__oxid->value == $reviewId) {
                    $review->selected = 1;
                    break;
                }
            }
            $this->_aViewData["allreviews"] = $reviewList;
            $this->_aViewData["editlanguage"] = $this->_iEditLang;

            if (isset($reviewId)) {
                $reviewForEditing = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
                $reviewForEditing->load($reviewId);
                $this->_aViewData["editreview"] = $reviewForEditing;

                $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
                $user->load($reviewForEditing->oxreviews__oxuserid->value);
                $this->_aViewData["user"] = $user;
            }
            //show "active" checkbox if moderating is active
            $this->_aViewData["blShowActBox"] = $config->getConfigParam('blGBModerate');
        }

        return "article_review.tpl";
    }

    /**
     * returns reviews list for article
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article Article object
     *
     * @return oxList
     */
    protected function _getReviewList($article)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = "select oxreviews.* from oxreviews
                     where oxreviews.OXOBJECTID = " . $database->quote($article->oxarticles__oxid->value) . "
                     and oxreviews.oxtype = 'oxarticle'";

        $variantList = $article->getVariants();

        if ($this->getConfig()->getConfigParam('blShowVariantReviews') && count($variantList)) {
            // verifying rights
            foreach ($variantList as $variant) {
                $query .= "or oxreviews.oxobjectid = " . $database->quote($variant->oxarticles__oxid->value) . " ";
            }
        }

        //$sSelect .= "and oxreviews.oxtext".\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageTag($this->_iEditLang)." != ''";
        $query .= "and oxreviews.oxlang = '" . $this->_iEditLang . "'";
        $query .= "and oxreviews.oxtext != '' ";

        // all reviews
        $reviewList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $reviewList->init("oxreview");
        $reviewList->selectString($query);

        return $reviewList;
    }

    /**
     * Saves article review information changes.
     */
    public function save()
    {
        parent::save();

        $parameters = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if ($this->getConfig()->getConfigParam('blGBModerate') && !isset($parameters['oxreviews__oxactive'])) {
            $parameters['oxreviews__oxactive'] = 0;
        }

        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->load(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rev_oxid"));
        $review->assign($parameters);
        $review->save();
    }

    /**
     * Deletes selected article review information.
     */
    public function delete()
    {
        $this->resetContentCache();

        $reviewId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rev_oxid");
        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->load($reviewId);
        $review->delete();

        // recalculating article average rating
        $rating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);
        $articleId = $this->getEditObjectId();

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $article->load($articleId);

        //switch database connection to master for the following read/write access.
        \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $article->setRatingAverage($rating->getRatingAverage($articleId, 'oxarticle'));
        $article->setRatingCount($rating->getRatingCount($articleId, 'oxarticle'));
        $article->save();
    }
}
