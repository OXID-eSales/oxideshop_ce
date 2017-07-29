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
 * Admin manufacturer main screen.
 * Performs collection and updating (on user submit) main item information.
 */
class ManufacturerMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Executes parent method parent::render(),
     * and returns name of template file
     * "manufacturer_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            $oManufacturer->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oManufacturer->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oManufacturer->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oManufacturer;

            // category tree
            $this->_createCategoryTree("artcattree");

            //Disable editing for derived articles
            if ($oManufacturer->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);
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

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oManufacturerMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerMainAjax::class);
            $this->_aViewData['oxajax'] = $oManufacturerMainAjax->getColumns();

            return "popups/manufacturer_main.tpl";
        }

        return "manufacturer_main.tpl";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        if (!isset($aParams['oxmanufacturers__oxactive'])) {
            $aParams['oxmanufacturers__oxactive'] = 0;
        }

        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);

        if ($soxId != "-1") {
            $oManufacturer->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxmanufacturers__oxid'] = null;
        }

        //Disable editing for derived articles
        if ($oManufacturer->isDerived()) {
            return;
        }

        //$aParams = $oManufacturer->ConvertNameArray2Idx( $aParams);
        $oManufacturer->setLanguage(0);
        $oManufacturer->assign($aParams);
        $oManufacturer->setLanguage($this->_iEditLang);
        $oManufacturer = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oManufacturer);
        $oManufacturer->save();

        // set oxid if inserted
        $this->setEditObjectId($oManufacturer->getId());
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return mixed
     */
    public function saveInnLang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        if (!isset($aParams['oxmanufacturers__oxactive'])) {
            $aParams['oxmanufacturers__oxactive'] = 0;
        }

        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);

        if ($soxId != "-1") {
            $oManufacturer->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxmanufacturers__oxid'] = null;
        }

        //Disable editing for derived articles
        if ($oManufacturer->isDerived()) {
            return;
        }

        //$aParams = $oManufacturer->ConvertNameArray2Idx( $aParams);
        $oManufacturer->setLanguage(0);
        $oManufacturer->assign($aParams);
        $oManufacturer->setLanguage($this->_iEditLang);
        $oManufacturer = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oManufacturer);
        $oManufacturer->save();

        // set oxid if inserted
        $this->setEditObjectId($oManufacturer->getId());
    }
}
