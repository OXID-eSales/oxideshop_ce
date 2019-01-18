<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin article main usergroup manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: User Administration -> User Groups -> Main.
 */
class UserGroupMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxgroups object,
     * passes data to Smarty engine and returns name of template file
     * "usergroup_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oGroup = oxNew(\OxidEsales\Eshop\Application\Model\Groups::class);
            $oGroup->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oGroup->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oGroup->loadInLang(key($oOtherLang), $soxId);
            }

            $this->_aViewData["edit"] = $oGroup;

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
            $oUsergroupMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\UserGroupMainAjax::class);
            $this->_aViewData['oxajax'] = $oUsergroupMainAjax->getColumns();

            return "popups/usergroup_main.tpl";
        }

        return "usergroup_main.tpl";
    }

    /**
     * Saves changed usergroup parameters.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxgroups__oxactive'])) {
            $aParams['oxgroups__oxactive'] = 0;
        }

        $oGroup = oxNew(\OxidEsales\Eshop\Application\Model\Groups::class);
        if ($soxId != "-1") {
            $oGroup->load($soxId);
        } else {
            $aParams['oxgroups__oxid'] = null;
        }

        $oGroup->setLanguage(0);
        $oGroup->assign($aParams);
        $oGroup->setLanguage($this->_iEditLang);
        $oGroup->save();

        // set oxid if inserted
        $this->setEditObjectId($oGroup->getId());
    }

    /**
     * Saves changed selected group parameters in different language.
     */
    public function saveinnlang()
    {
        $this->save();
    }
}
