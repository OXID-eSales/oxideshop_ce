<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin article attributes/selections lists manager.
 * Collects available attributes/selections lists for chosen article, may add
 * or remove any of them to article, etc.
 * Admin Menu: Manage Products -> Articles -> Selection.
 */
class ArticleAttribute extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Collects article attributes and selection lists, passes them to Smarty engine,
     * returns name of template file "article_attribute.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oArticle->load($soxId);

            if ($oArticle->isDerived()) {
                $this->_aViewData["readonly"] = true;
            }
        }

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oArticleAttributeAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleAttributeAjax::class);
            $this->_aViewData['oxajax'] = $oArticleAttributeAjax->getColumns();

            return "popups/article_attribute.tpl";
        } elseif ($iAoc == 2) {
            $oArticleSelectionAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSelectionAjax::class);
            $this->_aViewData['oxajax'] = $oArticleSelectionAjax->getColumns();

            return "popups/article_selection.tpl";
        }

        return "article_attribute.tpl";
    }
}
