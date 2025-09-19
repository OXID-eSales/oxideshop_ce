<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Enum\NewsletterSubscriptionStatus;
use OxidEsales\EshopCommunity\Application\Enum\SubscriptionOptedInStatus;
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
     * @deprecated will be removed in v8.0.
     */
    protected $_oActionArticles = null;

    /**
     * Top start article
     *
     * @var object
     * @deprecated will be removed in v8.0.
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
    protected $_sThisTemplate = 'page/info/newsletter';

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
        $this->_aRegParams = Registry::getRequest()->getRequestEscapedParameter("editval");
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
        $requestParameters = Registry::getRequest()->getRequestEscapedParameter("editval");
        $emailValidator = $this->getContainer()->get(EmailValidatorServiceBridgeInterface::class);

        // loads submited values
        $this->_aRegParams = $requestParameters;

        if (!$requestParameters['oxuser__oxusername']) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY');

            return;
        } elseif (!$emailValidator->isEmailValid($requestParameters['oxuser__oxusername'])) {
            // #1052C - eMail validation added
            Registry::getUtilsView()->addErrorToDisplay('MESSAGE_INVALID_EMAIL');

            return;
        }

        $subscribeRequest = Registry::getRequest()->getRequestEscapedParameter("subscribeStatus");

        $user = oxNew(User::class);
        $user->oxuser__oxusername = new Field($requestParameters['oxuser__oxusername'], Field::T_RAW);

        // if such user does not exist
        if (!$user->exists()) {
            // and subscribe is off - error, on - create
            if (!$subscribeRequest) {
                Registry::getUtilsView()->addErrorToDisplay('NEWSLETTER_EMAIL_NOT_EXIST');

                return;
            } else {
                $user->oxuser__oxactive = new Field(1, Field::T_RAW);
                $user->oxuser__oxrights = new Field('user', Field::T_RAW);
                $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId(), Field::T_RAW);
                $user->oxuser__oxfname = new Field($requestParameters['oxuser__oxfname'], Field::T_RAW);
                $user->oxuser__oxlname = new Field($requestParameters['oxuser__oxlname'], Field::T_RAW);
                $user->oxuser__oxsal = new Field($requestParameters['oxuser__oxsal'], Field::T_RAW);
                $user->oxuser__oxcountryid = new Field($requestParameters['oxuser__oxcountryid'], Field::T_RAW);
                $userExist = $user->save();
            }
        } else {
            $userExist = $user->load($user->getId());
        }


        // if user was added/loaded successfully and subscribe is on - subscribing to newsletter
        if ($subscribeRequest && $userExist) {
            $blOrderOptInEmail = Registry::getConfig()->getConfigParam('blOrderOptInEmail');
            if ($user->setNewsSubscription(true, $blOrderOptInEmail)) {
                // done, confirmation required?
                if ($blOrderOptInEmail) {
                    $this->_iNewsletterStatus = NewsletterSubscriptionStatus::Subscribed->value;
                } else {
                    $this->_iNewsletterStatus = NewsletterSubscriptionStatus::SubscriptionConfirmed->value;
                }
            } else {
                Registry::getUtilsView()->addErrorToDisplay('MESSAGE_NOT_ABLE_TO_SEND_EMAIL');
            }
        } elseif (!$subscribeRequest && $userExist) {
            // unsubscribing user
            $user->setNewsSubscription(false, false);
            $this->_iNewsletterStatus = NewsletterSubscriptionStatus::Canceled->value;
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
        $oUser = oxNew(User::class);
        if ($oUser->load(Registry::getRequest()->getRequestEscapedParameter('uid'))) {
            $sConfirmCode = md5($oUser->oxuser__oxusername->value . $oUser->oxuser__oxpasssalt->value);
            // is confirm code ok?
            if (Registry::getRequest()->getRequestEscapedParameter('confirm') == $sConfirmCode) {
                $oUser->getNewsSubscription()->setOptInStatus(SubscriptionOptedInStatus::Active->value);
                $oUser->addToGroup('oxidnewsletter');
                $this->_iNewsletterStatus = NewsletterSubscriptionStatus::SubscriptionConfirmed->value;
            }
        }
    }

    /**
     * Loads user and removes him from newsletter group.
     */
    public function removeme()
    {
        // existing user ?
        $user = oxNew(User::class);
        if ($user->load(Registry::getRequest()->getRequestEscapedParameter('uid'))) {
            $user->getNewsSubscription()->setOptInStatus(SubscriptionOptedInStatus::Disabled->value);

            // removing from group ..
            $user->removeFromGroup('oxidnewsletter');

            $this->_iNewsletterStatus = NewsletterSubscriptionStatus::Canceled->value;
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
     * @deprecated will be removed in v8.0.
     */
    public function getTopStartActionArticles()
    {
        if ($this->_oActionArticles === null) {
            $this->_oActionArticles = false;
            if (Registry::getConfig()->getConfigParam('bl_perfLoadAktion')) {
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
     * @deprecated will be removed in v8.0.
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
            $aHomeCountry = Registry::getConfig()->getConfigParam('aHomeCountry');
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
        $constant = match ($this->getNewsletterStatus()) {
            NewsletterSubscriptionStatus::Subscribed->value => 'MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS',
            NewsletterSubscriptionStatus::SubscriptionConfirmed->value => 'MESSAGE_NEWSLETTER_CONGRATULATIONS',
            NewsletterSubscriptionStatus::Canceled->value => 'SUCCESS',
            default => 'STAY_INFORMED',
        };

        return Registry::getLang()->translateString($constant, Registry::getLang()->getBaseLanguage(), false);
    }
}
