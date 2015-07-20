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

/**
 * Guestbook record manager.
 * Returns template, that arranges guestbook record information.
 * Admin Menu: User information -> Guestbook -> Main.
 */
class Adminguestbook_Main extends oxAdminDetails
{

    /**
     * Executes parent method parent::render() and returns template file
     * name "adminguestbook_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != '-1') {
            // load object
            $oLinks = oxNew('oxgbentry');
            $oLinks->load($soxId);

            // #580A - setting GB entry as viewed in admin
            if (!isset($oLinks->oxgbentries__oxviewed) || !$oLinks->oxgbentries__oxviewed->value) {
                $oLinks->oxgbentries__oxviewed = new oxField(1);
                $oLinks->save();
            }
            $this->_aViewData["edit"] = $oLinks;
        }

        //show "active" checkbox if moderating is active
        $this->_aViewData['blShowActBox'] = $myConfig->getConfigParam('blGBModerate');

        return 'adminguestbook_main.tpl';
    }

    /**
     * Saves guestbook record changes.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        // checkbox handling
        if (!isset($aParams['oxgbentries__oxactive'])) {
            $aParams['oxgbentries__oxactive'] = 0;
        }

        $oLinks = $this->getGuestbookEntryObject();
        if ($soxId != "-1") {
            $oLinks->load($soxId);
        } else {
            $aParams['oxgbentries__oxid'] = null;

            // author
            $aParams['oxgbentries__oxuserid'] = oxRegistry::getSession()->getVariable('auth');
        }

        $aParams = $this->appendAdditionalParametersForSave($aParams);

        $oLinks->assign($aParams);
        $oLinks->save();
        $this->setEditObjectId($oLinks->getId());
    }

    /**
     * Getter of oxgbentry object
     *
     * @return \oxGbEntry
     */
    public function getGuestbookEntryObject()
    {
        return oxNew('oxGbEntry');
    }

    /**
     * Add additional parameters for saving on oxgbentry save
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function appendAdditionalParametersForSave($parameters)
    {
        return $parameters;
    }
}
