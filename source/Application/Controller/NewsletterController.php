<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceBridgeInterface;

/**
 * Newsletter opt-in/out.
 * Arranges newsletter opt-in form, have some methods to confirm
 * user opt-in or remove user from newsletter list. OXID eShop ->
 * (Newsletter).
 */
class NewsletterController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Action articlelist
     *
     * @var object
     */
    protected $_oActionArticles = null;

    /**
     * Top start article
     *
     * @var object
     */
    protected $_oTopArticle = null;

    /**
     * Home country id
     *
     * @var string
     */
    protected $_sHomeCountryId = null;

    /**
     * Newletter status.
     *
     * @var integer
     */
    protected $_iNewsletterStatus = null;

    /**
     * User newsletter registration data.
     *
     * @var object
     */
    protected $_aRegParams = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/info/newsletter.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Only loads newsletter subscriber data.
     *
     * Template variables:
     * <b>aRegParams</b>
     */
    public function fill()
    {
        // loads submited values
        $this->_aRegParams = Registry::getConfig()->getRequestParameter("editval");
    }

    /**
     * Checks for newsletter subscriber data, if OK - creates new user as
     * subscriber or assigns existing user to newsletter group and sends
     * confirmation email.
     *
     * Template variables:
     * <b>success</b>, <b>error</b>, <b>aRegParams</b>
     *
     * @return bool
     */
    public function send()
    {
        $aParams = Registry::getConfig()->getRequestParameter("editval");
        $emailValidator = $this->getContainer()->get(EmailValidatorServiceBridgeInterface::class);

        // loads submited values
        $this->_aRegParams = $aParams;

        if (!$aParams['oxuser__oxusername']) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY');

            return;
        } elseif (!$emailValidator->isEmailValid($aParams['oxuser__oxusername'])) {
            // #1052C - eMail validation added
            Registry::getUtilsView()->addErrorToDisplay('MESSAGE_INVALID_EMAIL');

            return;
        }

        $blSubscribe = Registry::getConfig()->getRequestParameter("subscribeStatus");

        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->oxuser__oxusername = new Field($aParams['oxuser__oxusername'], Field::T_RAW);

        // if such user does not exist
        if (!$oUser->exists()) {
            // and subscribe is off - error, on - create
            if (!$blSubscribe) {
                Registry::getUtilsView()->addErrorToDisplay('NEWSLETTER_EMAIL_NOT_EXIST');

                return;
            } else {
                $oUser->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUser->oxuser__oxrights = new \OxidEsales\Eshop\Core\Field('user', \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUser->oxuser__oxshopid = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getConfig()->getShopId(), \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUser->oxuser__oxfname = new \OxidEsales\Eshop\Core\Field($aParams['oxuser__oxfname'], \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUser->oxuser__oxlname = new \OxidEsales\Eshop\Core\Field($aParams['oxuser__oxlname'], \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUser->oxuser__oxsal = new \OxidEsales\Eshop\Core\Field($aParams['oxuser__oxsal'], \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUser->oxuser__oxcountryid = new \OxidEsales\Eshop\Core\Field($aParams['oxuser__oxcountryid'], \OxidEsales\Eshop\Core\Field::T_RAW);
                $blUserLoaded = $oUser->save();
            }
        } else {
            $blUserLoaded = $oUser->load($oUser->getId());
        }


        // if user was added/loaded successfully and subscribe is on - subscribing to newsletter
        if ($blSubscribe && $blUserLoaded) {
            //removing user from subscribe list before adding
            $oUser->setNewsSubscription(false, false);

            $blOrderOptInEmail = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blOrderOptInEmail');
            if ($oUser->setNewsSubscription(true, $blOrderOptInEmail)) {
                // done, confirmation required?
                if ($blOrderOptInEmail) {
                    $this->_iNewsletterStatus = 1;
                } else {
                    $this->_iNewsletterStatus = 2;
                }
            } else {
                Registry::getUtilsView()->addErrorToDisplay('MESSAGE_NOT_ABLE_TO_SEND_EMAIL');
            }
        } elseif (!$blSubscribe && $blUserLoaded) {
            // unsubscribing user
            $oUser->setNewsSubscription(false, false);
            $this->_iNewsletterStatus = 3;
        }
    }

    /**
     * Loads user and Adds him to newsletter group.
     *
     * Template variables:
     * <b>success</b>
     */
    public function addme()
    {
        // user exists ?
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($oUser->load(Registry::getConfig()->getRequestParameter('uid'))) {
            $sConfirmCode = md5($oUser->oxuser__oxusername->value . $oUser->oxuser__oxpasssalt->value);
            // is confirm code ok?
            if (Registry::getConfig()->getRequestParameter('confirm') == $sConfirmCode) {
                $oUser->getNewsSubscription()->setOptInStatus(1);
                $oUser->addToGroup('oxidnewsletter');
                $this->_iNewsletterStatus = 2;
            }
        }
    }

    /**
     * Loads user and removes him from newsletter group.
     */
    public function removeme()
    {
        // existing user ?
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($oUser->load(Registry::getConfig()->getRequestParameter('uid'))) {
            $oUser->getNewsSubscription()->setOptInStatus(0);

            // removing from group ..
            $oUser->removeFromGroup('oxidnewsletter');

            $this->_iNewsletterStatus = 3;
        }
    }

    /**
     * simlink to function removeme bug fix #0002894
     */
    public function rmvm()
    {
        $this->removeme();
    }

    /**
     * Template variable getter. Returns action articlelist
     *
     * @return object
     */
    public function getTopStartActionArticles()
    {
        if ($this->_oActionArticles === null) {
            $this->_oActionArticles = false;
            if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadAktion')) {
                $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $oArtList->loadActionArticles('OXTOPSTART');
                if ($oArtList->count()) {
                    $this->_oTopArticle = $oArtList->current();
                    $this->_oActionArticles = $oArtList;
                }
            }
        }

        return $this->_oActionArticles;
    }

    /**
     * Template variable getter. Returns top start article
     *
     * @return object
     */
    public function getTopStartArticle()
    {
        if ($this->_oTopArticle === null) {
            $this->_oTopArticle = false;
            if ($this->getTopStartActionArticles()) {
                return $this->_oTopArticle;
            }
        }

        return $this->_oTopArticle;
    }

    /**
     * Template variable getter. Returns country id
     *
     * @return string
     */
    public function getHomeCountryId()
    {
        if ($this->_sHomeCountryId === null) {
            $this->_sHomeCountryId = false;
            $aHomeCountry = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aHomeCountry');
            if (is_array($aHomeCountry)) {
                $this->_sHomeCountryId = current($aHomeCountry);
            }
        }

        return $this->_sHomeCountryId;
    }

    /**
     * Template variable getter. Returns newsletter subscription status
     *
     * @return integer
     */
    public function getNewsletterStatus()
    {
        return $this->_iNewsletterStatus;
    }

    /**
     * Template variable getter. Returns user newsletter registration data
     *
     * @return array
     */
    public function getRegParams()
    {
        return $this->_aRegParams;
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
        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $aPath['title'] = Registry::getLang()->translateString('STAY_INFORMED', $iBaseLanguage, false);
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
        if ($this->getNewsletterStatus() == 4 || !$this->getNewsletterStatus()) {
            $sConstant = 'STAY_INFORMED';
        } elseif ($this->getNewsletterStatus() == 1) {
            $sConstant = 'MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS';
        } elseif ($this->getNewsletterStatus() == 2) {
            $sConstant = 'MESSAGE_NEWSLETTER_CONGRATULATIONS';
        } elseif ($this->getNewsletterStatus() == 3) {
            $sConstant = 'SUCCESS';
        }

        return Registry::getLang()->translateString($sConstant, Registry::getLang()->getBaseLanguage(), false);
    }
}
