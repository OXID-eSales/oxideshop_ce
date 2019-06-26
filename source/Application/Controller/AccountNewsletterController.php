<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use oxRegistry;

/**
 * Current user newsletter manager.
 * When user is logged in in this manager window he can modify
 * his newletter subscription status - simply register or
 * unregister from newsletter. OXID eShop -> MY ACCOUNT -> Newsletter.
 */
class AccountNewsletterController extends \OxidEsales\Eshop\Application\Controller\AccountController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/newsletter.tpl';

    /**
     * Whether the newsletter option had been changed.
     *
     * @var bool
     */
    protected $_blNewsletter = null;

    /**
     * Whether the newsletter option had been changed give some affirmation.
     *
     * @var integer
     */
    protected $_iSubscriptionStatus = 0;

    /**
     * If user is not logged in - returns name of template AccountNewsletterController::_sThisLoginTemplate, or if user
     * is already logged in - returns name of template AccountNewsletterController::_sThisTemplate
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        // is logged in ?
        $oUser = $this->getUser();
        if (!$oUser) {
            return $this->_sThisTemplate = $this->_sThisLoginTemplate;
        }

        return $this->_sThisTemplate;
    }


    /**
     * Template variable getter. Returns 0 when newsletter had been changed.
     *
     * @return int
     */
    public function isNewsletter()
    {
        $oUser = $this->getUser();
        if (!$oUser) {
            return false;
        }

        return $oUser->getNewsSubscription()->getOptInStatus();
    }

    /**
     * Removes or adds user to newsletter group according to
     * current subscription status. Returns true on success.
     *
     * @return bool
     */
    public function subscribe()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return false;
        }

        // is logged in ?
        $oUser = $this->getUser();
        if (!$oUser) {
            return false;
        }

        $iStatus = $this->getConfig()->getRequestParameter('status');
        if ($oUser->setNewsSubscription($iStatus, $this->getConfig()->getConfigParam('blOrderOptInEmail'))) {
            $this->_iSubscriptionStatus = ($iStatus == 0 && $iStatus !== null) ? -1 : 1;
        }
    }

    /**
     * Template variable getter. Returns 1 when newsletter had been changed to "yes"
     * else return -1 if had been changed to "no".
     *
     * @return integer
     */
    public function getSubscriptionStatus()
    {
        return $this->_iSubscriptionStatus;
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
        $oUtils = Registry::getUtilsUrl();
        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();

        $aPath['title'] = Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = Registry::getSeoEncoder()->getStaticUrl($sSelfLink . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = Registry::getLang()->translateString('NEWSLETTER_SETTINGS', $iBaseLanguage, false);
        $aPath['link'] = $oUtils->cleanUrl($this->getLink(), ['fnc']);
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
