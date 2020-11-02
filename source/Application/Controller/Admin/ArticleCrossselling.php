<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article crosselling/accesories manager.
 * Creates list of available articles, there is ability to assign or remove
 * assigning of article to crosselling/accesories with other products.
 * Admin Menu: Manage Products -> Articles -> Crosssell.
 */
class ArticleCrossselling extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Collects article crosselling and attributes information, passes
     * them to Smarty engine and returns name or template file
     * "article_crossselling.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        // crossselling
        $this->_createCategoryTree('artcattree');

        // accessoires
        $this->_createCategoryTree('artcattree2');

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && '-1' !== $soxId) {
            // load object
            $oArticle->load($soxId);

            if ($oArticle->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aoc');
        if (1 === $iAoc) {
            $oArticleCrossellingAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleCrosssellingAjax::class);
            $this->_aViewData['oxajax'] = $oArticleCrossellingAjax->getColumns();

            return 'popups/article_crossselling.tpl';
        } elseif (2 === $iAoc) {
            $oArticleAccessoriesAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax::class);
            $this->_aViewData['oxajax'] = $oArticleAccessoriesAjax->getColumns();

            return 'popups/article_accessories.tpl';
        }

        return 'article_crossselling.tpl';
    }
}
