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
use oxDb;
use stdClass;

/**
 * Admin article main discount manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Discounts -> Main.
 */
class DiscountMain extends \oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates article category tree, passes
     * data to Smarty engine and returns name of template file "discount_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        parent::render();

        $sOxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($sOxId) && $sOxId != "-1") {
            // load object
            $oDiscount = oxNew("oxdiscount");
            $oDiscount->loadInLang($this->_iEditLang, $sOxId);

            $oOtherLang = $oDiscount->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oDiscount->loadInLang(key($oOtherLang), $sOxId);
            }

            $this->_aViewData["edit"] = $oDiscount;

            //disabling derived items
            if ($oDiscount->isDerived()) {
                $this->_aViewData["readonly"] = true;
            }

            // remove already created languages
            $aLang = array_diff(oxRegistry::getLang()->getLanguageNames(), $oOtherLang);

            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if (($iAoc = oxRegistry::getConfig()->getRequestParameter("aoc"))) {
            if ($iAoc == "1") {
                $oDiscountMainAjax = oxNew('discount_main_ajax');
                $this->_aViewData['oxajax'] = $oDiscountMainAjax->getColumns();

                return "popups/discount_main.tpl";
            } elseif ($iAoc == "2") {
                // generating category tree for artikel choose select list
                $this->_createCategoryTree("artcattree");

                $oDiscountItemAjax = oxNew('discount_item_ajax');
                $this->_aViewData['oxajax'] = $oDiscountItemAjax->getColumns();

                return "popups/discount_item.tpl";
            }
        }

        return "discount_main.tpl";
    }

    /**
     * Returns item discount product title
     *
     * @return string
     */
    public function getItemDiscountProductTitle()
    {
        $sTitle = false;
        $sOxId = $this->getEditObjectId();
        if (isset($sOxId) && $sOxId != "-1") {
            $sViewName = getViewName("oxarticles", $this->_iEditLang);
            // Reading from slave is ok here (see ESDEV-3804 and ESDEV-3822).
            $database = oxDb::getDb();
            $sQ = "select concat( $sViewName.oxartnum, ' ', $sViewName.oxtitle ) from oxdiscount
                   left join $sViewName on $sViewName.oxid=oxdiscount.oxitmartid
                   where oxdiscount.oxitmartid != '' and oxdiscount.oxid=" . $database->quote($sOxId);
            $sTitle = $database->getOne($sQ);
        }

        return $sTitle ? $sTitle : " -- ";
    }

    /**
     * Saves changed selected discount parameters.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oDiscount = oxNew("oxDiscount");
        if ($sOxId != "-1") {
            $oDiscount->load($sOxId);
        } else {
            $aParams['oxdiscount__oxid'] = null;
        }

        // checkbox handling
        if (!isset($aParams['oxdiscount__oxactive'])) {
            $aParams['oxdiscount__oxactive'] = 0;
        }

        //disabling derived items
        if ($oDiscount->isDerived()) {
            return;
        }

        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oDiscount->setLanguage(0);
        $oDiscount->assign($aParams);
        $oDiscount->setLanguage($this->_iEditLang);
        $oDiscount = oxRegistry::get("oxUtilsFile")->processFiles($oDiscount);
        try {
            $oDiscount->save();
        } catch (\oxInputException $exception) {

            $newException = oxNew("oxExceptionToDisplay");
            $newException->setMessage($exception->getMessage());
            $this->addTplParam('discount_title', $aParams['oxdiscount__oxtitle']);

            if (false !== strpos($exception->getMessage(), 'DISCOUNT_ERROR_OXSORT')) {
                $messageArgument = oxRegistry::getLang()->translateString('DISCOUNT_MAIN_SORT', oxRegistry::getLang()->getTplLanguage(), true);
                $newException->setMessageArgs($messageArgument);
            }

            oxRegistry::get("oxUtilsView")->addErrorToDisplay($newException);

            return;
        }

        // set oxid if inserted
        $this->setEditObjectId($oDiscount->getId());
    }

    /**
     * Saves changed selected discount parameters in different language.
     *
     * @return null
     */
    public function saveinnlang()
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oAttr = oxNew("oxdiscount");
        if ($sOxId != "-1") {
            $oAttr->load($sOxId);
        } else {
            $aParams['oxdiscount__oxid'] = null;
        }
        // checkbox handling
        if (!isset($aParams['oxdiscount__oxactive'])) {
            $aParams['oxdiscount__oxactive'] = 0;
        }

        //disabling derived items
        if ($oAttr->isDerived()) {
            return;
        }

        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oAttr->setLanguage(0);
        $oAttr->assign($aParams);
        $oAttr->setLanguage($this->_iEditLang);
        $oAttr = oxRegistry::get("oxUtilsFile")->processFiles($oAttr);
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId($oAttr->getId());
    }

    public function getNextOxsort() {
        $shopId = oxRegistry::getConfig()->getShopId();
        $nextSort = oxNew("oxdiscount")->getNextOxsort($shopId);

        return $nextSort;
    }
}
