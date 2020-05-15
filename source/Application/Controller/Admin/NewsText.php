<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin Menu: Customer Info -> News -> Text.
 * @deprecated 6.5.6 "News" feature will be removed completely
 */
class NewsText extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxnews object and
     * passes news text to smarty. Returns name of template file "news_text.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        $oNews = oxNew(\OxidEsales\Eshop\Application\Model\News::class);

        if (isset($soxId) && $soxId != "-1") {
            $iNewsLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("newslang");

            if (!isset($iNewsLang)) {
                $iNewsLang = $this->_iEditLang;
            }

            $this->_aViewData["newslang"] = $iNewsLang;
            $oNews->loadInLang($iNewsLang, $soxId);

            foreach (\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames() as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            // Disable editing for derived items.
            if ($oNews->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            $this->_aViewData["edit"] = $oNews;
        }
        $this->_aViewData["editor"] = $this->_generateTextEditor("100%", 255, $oNews, "oxnews__oxlongdesc", "news.tpl.css");

        return "news_text.tpl";
    }

    /**
     * Saves news text.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oNews = oxNew(\OxidEsales\Eshop\Application\Model\News::class);

        $iNewsLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("newslang");

        if (!isset($iNewsLang)) {
            $iNewsLang = $this->_iEditLang;
        }

        if ($soxId != "-1") {
            $oNews->loadInLang($iNewsLang, $soxId);
        } else {
            $aParams['oxnews__oxid'] = null;
        }

        // Disable editing for derived items.
        if ($oNews->isDerived()) {
            return;
        }

        $oNews->setLanguage(0);
        $oNews->assign($aParams);
        $oNews->setLanguage($iNewsLang);

        $oNews->save();
        // set oxid if inserted
        $this->setEditObjectId($oNews->getId());
    }
}
