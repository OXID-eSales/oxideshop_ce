<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\MailValidator;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormBridgeInterface;

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
        $contactFormBridge = $this->getContainer()->get(ContactFormBridgeInterface::class);

        $form = $contactFormBridge->getContactForm();
        $form->handleRequest($this->getMappedContactFormRequest());

        if ($form->isValid()) {
            $email = oxNew(Email::class);

            /**
            $message =  $lang->translateString('MESSAGE_FROM') . " " .
                $lang->translateString($requestParameters['oxuser__oxsal']) . " " .
                $requestParameters['oxuser__oxfname'] . " " .
                $requestParameters['oxuser__oxlname'] . "(" . $requestParameters['oxuser__oxusername'] . ")<br /><br />" .
                nl2br(Registry::getConfig()->getRequestParameter('c_message'));
             */

            if ($email->sendContactMail($form->email, $form->subject, $form->message)) {
                $this->_blContactSendStatus = 1;
            } else {
                Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_CHECK_EMAIL');
            }
        } else {
            foreach ($form->getErrors() as $error) {
                Registry::getUtilsView()->addErrorToDisplay($error);
            }

            return false;
        }

        $this->_aViewData['contactForm'] = $form;



        $requestParameters = Registry::getConfig()->getRequestParameter('editval');

        $emailValidator = oxNew(MailValidator::class);

        if (!$emailValidator->isValidEmail($requestParameters['oxuser__oxusername'])) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_INPUT_NOVALIDEMAIL');

            return false;
        }

        $sSubject = Registry::getConfig()->getRequestParameter('c_subject');
        if (!$requestParameters['oxuser__oxfname']
            || !$requestParameters['oxuser__oxlname']
            || !$requestParameters['oxuser__oxusername']
            || !$sSubject
        ) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_INPUT_NOTALLFIELDS');

            return false;
        }

        $lang = Registry::getLang();
        $message =  $lang->translateString('MESSAGE_FROM') . " " .
                    $lang->translateString($requestParameters['oxuser__oxsal']) . " " .
                    $requestParameters['oxuser__oxfname'] . " " .
                    $requestParameters['oxuser__oxlname'] . "(" . $requestParameters['oxuser__oxusername'] . ")<br /><br />" .
                    nl2br(Registry::getConfig()->getRequestParameter('c_message'));

        $email = oxNew(\OxidEsales\Eshop\Core\Email::class);
        if ($email->sendContactMail($requestParameters['oxuser__oxusername'], $sSubject, $message)) {
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
        $title = Registry::getLang()->translateString(
            'CONTACT',
            Registry::getLang()->getBaseLanguage(),
            false
        );

        return [
            [
                'title' => $title,
                'link'  => $this->getLink(),
            ]
        ];
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
     * @return array
     */
    private function getMappedContactFormRequest()
    {
        $request = Registry::getRequest();
        $personData = $request->getRequestEscapedParameter('editval');

        return [
            'email'         => $personData['oxuser__oxusername'],
            'firstName'     => $personData['oxuser__oxfname'],
            'lastName'      => $personData['oxuser__oxlname'],
            'salutation'    => $personData['oxuser__oxsal'],
            'subject'       => $request->getRequestEscapedParameter('c_subject'),
            'message'       => $request->getRequestEscapedParameter('c_message'),
        ];
    }
}
