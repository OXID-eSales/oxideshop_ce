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

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxRegistry;

/**
 * Article suggestion page.
 * Collects some article base information, sets default recommendation text,
 * sends suggestion mail to user.
 */
class InviteController extends \oxUBase
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/privatesales/invite.tpl';

    /**
     * Current class login template name.
     *
     * @var string
     */
    protected $_sThisLoginTemplate = 'page/account/login.tpl';

    /**
     * Required fields to fill before sending suggest email
     *
     * @var array
     */
    protected $_aReqFields = array('rec_email', 'send_name', 'send_email', 'send_message', 'send_subject');

    /**
     * CrossSelling article list
     *
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products article list
     *
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Recommlist
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Invition data
     *
     * @var object
     */
    protected $_aInviteData = null;

    /**
     * Email sent status status.
     *
     * @var integer
     */
    protected $_iMailStatus = null;

    /**
     * Executes parent::render(), if invitation is disabled - redirects to main page
     *
     * @return string
     */
    public function render()
    {
        $oConfig = $this->getConfig();

        if (!$oConfig->getConfigParam("blInvitationsEnabled")) {
            oxRegistry::getUtils()->redirect($oConfig->getShopHomeUrl());

            return;
        }

        parent::render();

        return $this->getUser() ? $this->_sThisTemplate : $this->_sThisLoginTemplate;
    }

    /**
     * Sends product suggestion mail and returns a URL according to
     * URL formatting rules.
     *
     * @return  null
     */
    public function send()
    {
        $oConfig = $this->getConfig();

        if (!$oConfig->getConfigParam("blInvitationsEnabled")) {
            oxRegistry::getUtils()->redirect($oConfig->getShopHomeUrl());
        }

        $aParams = oxRegistry::getConfig()->getRequestParameter('editval', true);
        $oUser = $this->getUser();
        if (!is_array($aParams) || !$oUser) {
            return;
        }

        // storing used written values
        $oParams = (object) $aParams;
        $this->setInviteData((object) oxRegistry::getConfig()->getRequestParameter('editval'));

        $oUtilsView = oxRegistry::get("oxUtilsView");

        // filled not all fields ?
        foreach ($this->_aReqFields as $sFieldName) {
            //checking if any email was entered
            if ($sFieldName == "rec_email") {
                foreach ($aParams[$sFieldName] as $sKey => $sEmail) {
                    //removing empty emails fields from eMails array
                    if (empty($sEmail)) {
                        unset($aParams[$sFieldName][$sKey]);
                    }
                }

                //counting entered eMails
                if (count($aParams[$sFieldName]) < 1) {
                    $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY');

                    return;
                }

                //updating values object
                $oParams->rec_email = $aParams[$sFieldName];
            }

            if (!isset($aParams[$sFieldName]) || !$aParams[$sFieldName]) {
                $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY');

                return;
            }
        }

        $oUtils = oxRegistry::getUtils();

        //validating entered emails
        foreach ($aParams["rec_email"] as $sRecipientEmail) {
            if (!$oUtils->isValidEmail($sRecipientEmail)) {
                $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_INVITE_INCORRECTEMAILADDRESS');

                return;
            }
        }

        if (!$oUtils->isValidEmail($aParams["send_email"])) {
            $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_INVITE_INCORRECTEMAILADDRESS');

            return;
        }

        // sending invite email
        $oEmail = oxNew('oxemail');

        if ($oEmail->sendInviteMail($oParams)) {
            $this->_iMailStatus = 1;

            //getting active user
            $oUser = $this->getUser();

            //saving statistics for sent emails
            $oUser->updateInvitationStatistics($aParams["rec_email"]);
        } else {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('ERROR_MESSAGE_CHECK_EMAIL');
        }
    }

    /**
     * Template variable getter. Return if mail was send successfully
     *
     * @return array
     */
    public function getInviteSendStatus()
    {
        return ($this->_iMailStatus == 1);
    }

    /**
     * Suggest data setter
     *
     * @param object $oData suggest data object
     */
    public function setInviteData($oData)
    {
        $this->_aInviteData = $oData;
    }

    /**
     * Template variable getter.
     *
     * @return array
     */
    public function getInviteData()
    {
        return $this->_aInviteData;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath = array();

        $iLang = oxRegistry::getLang()->getBaseLanguage();
        $aPath['title'] = oxRegistry::getLang()->translateString('INVITE_YOUR_FRIENDS', $iLang, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
