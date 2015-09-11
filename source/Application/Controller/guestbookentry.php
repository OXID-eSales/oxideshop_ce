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
 * Guest book entry manager class.
 * Manages guestbook entries, denies them, etc.
 */
class GuestbookEntry extends GuestBook
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/guestbook/guestbookentry.tpl';

    /**
     * Guestbook form id, prevents double entry submit
     *
     * @var string
     */
    protected $_sGbFormId = null;

    /**
     * Method applies validation to entry and saves it to DB.
     * On error/success returns name of action to perform
     * (on error: "guestbookentry?error=x"", on success: "guestbook").
     *
     * @return string
     */
    public function saveEntry()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        $sReviewText = trim(( string ) oxRegistry::getConfig()->getRequestParameter('rvw_txt', true));
        $sShopId = $this->getConfig()->getShopId();
        $sUserId = oxRegistry::getSession()->getVariable('usr');

        // guest book`s entry is validated
        if (!$sUserId) {
            $sErrorMessage = 'ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_LOGIN_TO_WRITE_ENTRY';
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($sErrorMessage);

            //return to same page
            return;
        }

        if (!$sShopId) {
            $sErrorMessage = 'ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_UNDEFINED_SHOP';
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($sErrorMessage);

            return 'guestbookentry';
        }

        // empty entries validation
        if ('' == $sReviewText) {
            $sErrorMessage = 'ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_REVIEW_CONTAINS_NO_TEXT';
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($sErrorMessage);

            return 'guestbookentry';
        }

        // flood protection
        $oEntrie = oxNew('oxgbentry');
        if ($oEntrie->floodProtection($sShopId, $sUserId)) {
            $sErrorMessage = 'ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_MAXIMUM_NUMBER_EXCEEDED';
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($sErrorMessage);

            return 'guestbookentry';
        }

        // double click protection
        if ($this->canAcceptFormData()) {
            // here the guest book entry is saved
            $oEntry = oxNew('oxgbentry');
            $oEntry->oxgbentries__oxshopid = new oxField($sShopId);
            $oEntry->oxgbentries__oxuserid = new oxField($sUserId);
            $oEntry->oxgbentries__oxcontent = new oxField($sReviewText);
            $oEntry->save();
        }

        return 'guestbook';
    }
}
