<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Class reserved for extending (for customization - you can add you own fields, etc.).
 */
class ArticleUserdef extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article data from DB, passes it to Smarty engine, returns name
     * of template file "article_userdef.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $this->_aViewData["edit"] = $oArticle;

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            if ($oArticle->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // load object
            $oArticle->load($soxId);
        }

        return "article_userdef.tpl";
    }
}
