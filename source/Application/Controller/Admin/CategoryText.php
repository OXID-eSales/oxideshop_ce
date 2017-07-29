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
 * Admin article categories text manager.
 * Category text/description manager, enables editing of text.
 * Admin Menu: Manage Products -> Categories -> Text.
 */
class CategoryText extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Loads category object data, pases it to Smarty engine and returns
     * name of template file "category_text.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $iCatLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("catlang");

            if (!isset($iCatLang)) {
                $iCatLang = $this->_iEditLang;
            }

            $this->_aViewData["catlang"] = $iCatLang;

            $oCategory->loadInLang($iCatLang, $soxId);

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            foreach (\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames() as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        $this->_aViewData["editor"] = $this->_generateTextEditor("100%", 300, $oCategory, "oxcategories__oxlongdesc", "list.tpl.css");

        return "category_text.tpl";
    }

    /**
     * Saves category description text to DB.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $myConfig = $this->getConfig();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $iCatLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("catlang");
        $iCatLang = $iCatLang ? $iCatLang : 0;

        if ($soxId != "-1") {
            $oCategory->loadInLang($iCatLang, $soxId);
        } else {
            $aParams['oxcategories__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oCategory->isDerived()) {
            return;
        }

        $oCategory->setLanguage(0);
        $oCategory->assign($aParams);
        $oCategory->setLanguage($iCatLang);
        $oCategory->save();

        // set oxid if inserted
        $this->setEditObjectId($oCategory->getId());
    }
}
