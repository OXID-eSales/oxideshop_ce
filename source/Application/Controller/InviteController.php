<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use oxRegistry;

/**
 * Article suggestion page.
 * Collects some article base information, sets default recommendation text,
 * sends suggestion mail to user.
 */
class InviteController extends \OxidEsales\Eshop\Application\Controller\AccountController
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
    protected $_aReqFields = ['rec_email', 'send_name', 'send_email', 'send_message', 'send_subject'];

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
            Registry::getUtils()->redirect($oConfig->getShopHomeUrl());

            return;
        }

        return parent::render();
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
            Registry::getUtils()->redirect($oConfig->getShopHomeUrl());
        }

        $aParams = Registry::getConfig()->getRequestParameter('editval', true);
        $oUser = $this->getUser();
        if (!is_array($aParams) || !$oUser) {
            return;
        }

        // storing used written values
        $oParams = (object) $aParams;
        $this->setInviteData((object) Registry::getConfig()->getRequestParameter('editval'));

        $oUtilsView = Registry::getUtilsView();

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

        //validating entered emails
        foreach ($aParams["rec_email"] as $sRecipientEmail) {
            if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($sRecipientEmail)) {
                $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_INVITE_INCORRECTEMAILADDRESS');

                return;
            }
        }

        if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($aParams["send_email"])) {
            $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_INVITE_INCORRECTEMAILADDRESS');

            return;
        }

        // sending invite email
        $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);

        if ($oEmail->sendInviteMail($oParams)) {
            $this->_iMailStatus = 1;

            //getting active user
            $oUser = $this->getUser();

            //saving statistics for sent emails
            $oUser->updateInvitationStatistics($aParams["rec_email"]);
        } else {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_CHECK_EMAIL');
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
        $aPaths = [];
        $aPath = [];

        $iLang = Registry::getLang()->getBaseLanguage();
        $aPath['title'] = Registry::getLang()->translateString('INVITE_YOUR_FRIENDS', $iLang, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
