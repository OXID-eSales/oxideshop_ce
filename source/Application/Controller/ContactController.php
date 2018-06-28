<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * Contact window.
 * Arranges "CONTACT" window, by creating form for user opinion (etc.)
 * submission. After user correctly
 * fulfils all required fields all information is sent to shop owner by
 * email. OXID eShop -> CONTACT.
 */
class ContactController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Entered user data.
     *
     * @var array
     */
    protected $_aUserData = null;

    /**
     * Entered contact subject.
     *
     * @var string
     */
    protected $_sContactSubject = null;

    /**
     * Entered conatct message.
     *
     * @var string
     */
    protected $_sContactMessage = null;

    /**
     * Contact email send status.
     *
     * @var object
     */
    protected $_blContactSendStatus = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/info/contact.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Composes and sends user written message, returns false if some parameters
     * are missing.
     *
     * @return bool
     */
    public function send()
    {
        $aParams = Registry::getConfig()->getRequestParameter('editval');

        // checking email address
        if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($aParams['oxuser__oxusername'])) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_INPUT_NOVALIDEMAIL');

            return false;
        }

        $sSubject = Registry::getConfig()->getRequestParameter('c_subject');
        $sMessage = $this->getContactFormMessage($aParams);

        $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
        if ($oEmail->sendContactMail($aParams['oxuser__oxusername'], $sSubject, $sMessage)) {
            $this->_blContactSendStatus = 1;
        } else {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_CHECK_EMAIL');
        }
    }

    /**
     * Template variable getter. Returns entered user data
     *
     * @return object
     */
    public function getUserData()
    {
        if ($this->_oUserData === null) {
            $this->_oUserData = Registry::getConfig()->getRequestParameter('editval');
        }

        return $this->_oUserData;
    }

    /**
     * Template variable getter. Returns entered contact subject
     *
     * @return object
     */
    public function getContactSubject()
    {
        if ($this->_sContactSubject === null) {
            $this->_sContactSubject = Registry::getConfig()->getRequestParameter('c_subject');
        }

        return $this->_sContactSubject;
    }

    /**
     * Template variable getter. Returns entered message
     *
     * @return object
     */
    public function getContactMessage()
    {
        if ($this->_sContactMessage === null) {
            $this->_sContactMessage = Registry::getConfig()->getRequestParameter('c_message');
        }

        return $this->_sContactMessage;
    }

    /**
     * Template variable getter. Returns status if email was send succesfull
     *
     * @return object
     */
    public function getContactSendStatus()
    {
        return $this->_blContactSendStatus;
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

        $aPath['title'] = Registry::getLang()->translateString('CONTACT', Registry::getLang()->getBaseLanguage(), false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getConfig()->getActiveShop()->oxshops__oxcompany->value;
    }

    /**
     * Returns contact form message.
     *
     * @param array $parameters
     *
     * @return string
     */
    private function getContactFormMessage($parameters)
    {
        $lang = Registry::getLang();

        $message = $lang->translateString('MESSAGE_FROM') . ' ';

        if ($parameters['oxuser__oxsal']) {
            $message .= $lang->translateString($parameters['oxuser__oxsal']) . ' ';
        }

        if ($parameters['oxuser__oxfname']) {
            $message .= $parameters['oxuser__oxfname'] . ' ';
        }

        if ($parameters['oxuser__oxlname']) {
            $message .= $parameters['oxuser__oxlname'] . ' ';
        }

        $message .= '(' . $parameters['oxuser__oxusername'] . ")<br /><br />";

        $messageBody = Registry::getConfig()->getRequestParameter('c_message');
        if ($messageBody) {
            $message .= nl2br($messageBody);
        }

        return $message;
    }
}
