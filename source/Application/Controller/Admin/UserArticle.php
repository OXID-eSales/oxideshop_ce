<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin user articles setting manager.
 * Collects user articles settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Articles.
 */
class UserArticle extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxlist object and returns name
     * of template file "user_article.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if ($soxId && $soxId != '-1') {
            // load object
            $oArticlelist = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticleList::class);
            $oArticlelist->loadOrderArticlesForUser($soxId);

            $this->_aViewData['oArticlelist'] = $oArticlelist;
        }

        return 'user_article.tpl';
    }
}
