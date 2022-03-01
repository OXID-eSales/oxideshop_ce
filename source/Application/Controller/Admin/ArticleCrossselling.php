<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article crosselling/accesories manager.
 * Creates list of available articles, there is ability to assign or remove
 * assigning of article to crosselling/accesories with other products.
 * Admin Menu: Manage Products -> Articles -> Crosssell.
 */
class ArticleCrossselling extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        // crossselling
        $this->createCategoryTree("artcattree");

        // accessoires
        $this->createCategoryTree("artcattree2");

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oArticle->load($soxId);

            if ($oArticle->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $iAoc = Registry::getRequest()->getRequestEscapedParameter("aoc");
        if ($iAoc == 1) {
            $oArticleCrossellingAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleCrosssellingAjax::class);
            $this->_aViewData['oxajax'] = $oArticleCrossellingAjax->getColumns();

            return "popups/article_crossselling";
        } elseif ($iAoc == 2) {
            $oArticleAccessoriesAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax::class);
            $this->_aViewData['oxajax'] = $oArticleAccessoriesAjax->getColumns();

            return "popups/article_accessories";
        }

        return "article_crossselling";
    }
}
