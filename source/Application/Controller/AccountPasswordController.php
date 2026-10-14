<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * Current user password change form.
 * When user is logged in he may change his Billing and Shipping
 * information (this is important for ordering purposes).
 * Information as email, password, greeting, name, company, address,
 * etc. Some fields must be entered. OXID eShop -> MY ACCOUNT
 * -> Update your billing and delivery settings.
 */
class AccountPasswordController extends \OxidEsales\Eshop\Application\Controller\AccountController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/password';

    /**
     * Whether the password had been changed.
     *
     * @var bool
     */
    protected $_blPasswordChanged = false;

    /**
     * If user is not logged in - returns name of template AccountUserController::_sThisLoginTemplate, or if user is
     * already logged in additionally loads user delivery address info and forms country list. Returns name of template
     * AccountUserController::_sThisTemplate
     *
     * @return string $_sThisTemplate current template file name
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

    public function changePassword()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        $user = $this->getUser();
        if (!$user) {
            return;
        }

        $oldPassword = Registry::getRequest()->getRequestParameter('password_old');
        $newPassword = Registry::getRequest()->getRequestParameter('password_new');
        $passwordConfirmation = Registry::getRequest()->getRequestParameter('password_new_confirm');

        /** @var \OxidEsales\Eshop\Core\InputValidator $inputValidator */
        $inputValidator = Registry::getInputValidator();
        if (($exception = $inputValidator->checkPassword($user, $newPassword, $passwordConfirmation, true))) {
            switch ($exception->getMessage()) {
                case Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_EMPTYPASS'):
                case Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_TOO_SHORT'):
                    return Registry::getUtilsView()->addErrorToDisplay(
                        'ERROR_MESSAGE_PASSWORD_TOO_SHORT',
                        false,
                        true
                    );
                default:
                    return Registry::getUtilsView()->addErrorToDisplay(
                        'ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH',
                        false,
                        true
                    );
            }
        }

        if (!$oldPassword || !$user->isSamePassword($oldPassword)) {
            /** @var \OxidEsales\Eshop\Core\UtilsView $utilsView */
            $utilsView = Registry::getUtilsView();

            return $utilsView->addErrorToDisplay('ERROR_MESSAGE_CURRENT_PASSWORD_INVALID', false, true);
        }

        $user->setPassword($newPassword);
        if ($user->save()) {
            $user->setUpdateKey(true);
            $this->_blPasswordChanged = true;

            Registry::getUtilsServer()->deleteUserCookie(Registry::getConfig()->getShopId());
        }
    }

    /**
     * Template variable getter. Returns true when password had been changed.
     *
     * @return bool
     */
    public function isPasswordChanged()
    {
        return $this->_blPasswordChanged;
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

        /** @var \OxidEsales\Eshop\Core\SeoEncoder $oSeoEncoder */
        $oSeoEncoder = Registry::getSeoEncoder();
        $oLang = Registry::getLang();
        $iBaseLanguage = $oLang->getBaseLanguage();
        $aPath['title'] = $oLang->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = $oSeoEncoder->getStaticUrl($this->getViewConfig()->getSelfLink() . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = $oLang->translateString('CHANGE_PASSWORD', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
