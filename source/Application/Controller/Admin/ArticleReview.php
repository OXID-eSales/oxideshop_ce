<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Admin article review manager.
 * Collects customer review about article data. There ir possibility to update
 * review text or delete it.
 * Admin Menu: Manage Products -> Articles -> Review.
 */
class ArticleReview extends \oxAdminDetails
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

        $article = oxNew("oxArticle");
        $this->_aViewData["edit"] = $article;

        $articleId = $this->getEditObjectId();
        $reviewId = oxRegistry::getConfig()->getRequestParameter('rev_oxid');
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
                $reviewForEditing = oxNew("oxReview");
                $reviewForEditing->load($reviewId);
                $this->_aViewData["editreview"] = $reviewForEditing;

                $user = oxNew("oxuser");
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
     * @param oxArticle $article Article object
     *
     * @return oxList
     */
    protected function _getReviewList($article)
    {
        $database = oxDb::getDb();
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

        //$sSelect .= "and oxreviews.oxtext".oxRegistry::getLang()->getLanguageTag($this->_iEditLang)." != ''";
        $query .= "and oxreviews.oxlang = '" . $this->_iEditLang . "'";
        $query .= "and oxreviews.oxtext != '' ";

        // all reviews
        $reviewList = oxNew("oxlist");
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

        $parameters = oxRegistry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if ($this->getConfig()->getConfigParam('blGBModerate') && !isset($parameters['oxreviews__oxactive'])) {
            $parameters['oxreviews__oxactive'] = 0;
        }

        $review = oxNew("oxreview");
        $review->load(oxRegistry::getConfig()->getRequestParameter("rev_oxid"));
        $review->assign($parameters);
        $review->save();
    }

    /**
     * Deletes selected article review information.
     */
    public function delete()
    {
        $this->resetContentCache();

        $reviewId = oxRegistry::getConfig()->getRequestParameter("rev_oxid");
        $review = oxNew("oxreview");
        $review->load($reviewId);
        $review->delete();

        // recalculating article average rating
        $rating = oxNew("oxRating");
        $articleId = $this->getEditObjectId();

        $article = oxNew('oxArticle');
        $article->load($articleId);

        //switch database connection to master for the following read/write access.
        oxDB::getMaster();
        $article->setRatingAverage($rating->getRatingAverage($articleId, 'oxarticle'));
        $article->setRatingCount($rating->getRatingCount($articleId, 'oxarticle'));
        $article->save();
    }
}
