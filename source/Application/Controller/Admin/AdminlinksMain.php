<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin links details manager.
 * Creates form for submitting new admin links or modifying old ones.
 * Admin Menu: Customer Info -> Links.
 */
class AdminlinksMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Sets link information data (or leaves empty), returns name of template
     * file "adminlinks_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        $oLinks = oxNew(\OxidEsales\Eshop\Application\Model\Links::class, getViewName('oxlinks'));

        if (isset($soxId) && $soxId != "-1") {
            $oLinks->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oLinks->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oLinks->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oLinks;

            //Disable editing for derived items
            if ($oLinks->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $this->_aViewData["posslang"] = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        // generate editor
        $this->_aViewData["editor"] = $this->_generateTextEditor(
            "100%",
            255,
            $oLinks,
            "oxlinks__oxurldesc",
            "links.tpl.css"
        );

        return "adminlinks_main.tpl";
    }

    /**
     * Saves information about link (active, date, URL, description, etc.) to DB.
     *
     * @return mixed
     */
    public function save()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxlinks__oxactive'])) {
            $aParams['oxlinks__oxactive'] = 0;
        }

        // adds space to the end of URL description to keep new added links visible
        // if URL description left empty
        if (isset($aParams['oxlinks__oxurldesc']) && strlen($aParams['oxlinks__oxurldesc']) == 0) {
            $aParams['oxlinks__oxurldesc'] .= " ";
        }

        if (!$aParams['oxlinks__oxinsert']) {
            // sets default (?) date format to output
            // else if possible - changes date format to system compatible
            $sDate = date(\OxidEsales\Eshop\Core\Registry::getLang()->translateString("simpleDateFormat"));
            if ($sDate == "simpleDateFormat") {
                $aParams['oxlinks__oxinsert'] = date("Y-m-d");
            } else {
                $aParams['oxlinks__oxinsert'] = $sDate;
            }
        }

        $iEditLanguage = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editlanguage");
        $oLinks = oxNew(\OxidEsales\Eshop\Application\Model\Links::class, getViewName('oxlinks'));

        if ($soxId != "-1") {
            //$oLinks->load( $soxId );
            $oLinks->loadInLang($iEditLanguage, $soxId);

            //Disable editing for derived items
            if ($oLinks->isDerived()) {
                return;
            }
        } else {
            $aParams['oxlinks__oxid'] = null;
        }

        //$aParams = $oLinks->ConvertNameArray2Idx( $aParams);

        $oLinks->setLanguage(0);
        $oLinks->assign($aParams);
        $oLinks->setLanguage($iEditLanguage);
        $oLinks->save();

        parent::save();

        // set oxid if inserted
        $this->setEditObjectId($oLinks->getId());
    }

    /**
     * Saves link description in different languages (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxlinks__oxactive'])) {
            $aParams['oxlinks__oxactive'] = 0;
        }

        $oLinks = oxNew(\OxidEsales\Eshop\Application\Model\Links::class, getViewName('oxlinks'));
        $iEditLanguage = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editlanguage");

        if ($soxId != "-1") {
            $oLinks->loadInLang($iEditLanguage, $soxId);
        } else {
            $aParams['oxlinks__oxid'] = null;
            //$aParams = $oLinks->ConvertNameArray2Idx( $aParams);
        }

        //Disable editing for derived items
        if ($oLinks->isDerived()) {
            return;
        }

        $oLinks->setLanguage(0);
        $oLinks->assign($aParams);

        // apply new language
        $oLinks->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang"));
        $oLinks->save();

        // set oxid if inserted
        $this->setEditObjectId($oLinks->getId());
    }
}
