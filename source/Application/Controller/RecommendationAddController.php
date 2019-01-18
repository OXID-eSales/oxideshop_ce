<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxUBase;

/**
 * Handles adding article to recommendation list process.
 * Due to possibility of external modules we recommned to extend the vews from oxUBase view.
 * However expreimentally we extend RecommAdd from Details view here.
 *
 * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
 */
class RecommendationAddController extends \OxidEsales\Eshop\Application\Controller\ArticleDetailsController
{
    /**
     * Template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/recommendationadd.tpl';

    /**
     * User recommendation lists
     *
     * @var array
     */
    protected $_aUserRecommList = null;

    /**
     * Renders the view
     *
     * @return unknown
     */
    public function render()
    {
        \OxidEsales\Eshop\Application\Controller\FrontendController::render();

        return $this->_sThisTemplate;
    }

    /**
     * Returns user recommlists
     *
     * @return array
     */
    public function getRecommLists()
    {
        if ($this->_aUserRecommList === null) {
            $oUser = $this->getUser();
            if ($oUser) {
                $this->_aUserRecommList = $oUser->getUserRecommLists();
            }
        }

        return $this->_aUserRecommList;
    }

    /**
     * Returns the title of the product added to the recommendation list.
     *
     * @return string
     */
    public function getTitle()
    {
        $oProduct = $this->getProduct();

        return $oProduct->oxarticles__oxtitle->value . ' ' . $oProduct->oxarticles__oxvarselect->value;
    }
}
