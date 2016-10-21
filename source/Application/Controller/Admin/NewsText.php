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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin Menu: Customer Info -> News -> Text.
 *
 * @deprecated since v.5.3.0 (2016-06-17); The Admin Menu: Customer Info -> News feature will be moved to a module in v6.0.0
 */
class NewsText extends \oxAdminDetails
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
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oNews = oxNew("oxnews");
            $iNewsLang = oxRegistry::getConfig()->getRequestParameter("newslang");

            if (!isset($iNewsLang)) {
                $iNewsLang = $this->_iEditLang;
            }

            $this->_aViewData["newslang"] = $iNewsLang;
            $oNews->loadInLang($iNewsLang, $soxId);

            foreach (oxRegistry::getLang()->getLanguageNames() as $id => $language) {
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
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oNews = oxNew("oxnews");

        $iNewsLang = oxRegistry::getConfig()->getRequestParameter("newslang");

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
