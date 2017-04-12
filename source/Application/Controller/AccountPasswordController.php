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
    protected $_sThisTemplate = 'page/account/password.tpl';

    /**
     * Whether the password had been changed.
     *
     * @var bool
     */
    protected $_blPasswordChanged = false;

    /**
     * If user is not logged in - returns name of template \OxidEsales\Eshop\Application\Controller\AccountUserController::_sThisLoginTemplate,
     * or if user is allready logged in additionally loads user delivery address
     * info and forms country list. Returns name of template \OxidEsales\Eshop\Application\Controller\AccountUserController::_sThisTemplate
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

    /**
     * changes current user password
     *
     * @return null
     */
    public function changePassword()
    {
        if (!\OxidEsales\Eshop\Core\Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        $oUser = $this->getUser();
        if (!$oUser) {
            return;
        }

        $sOldPass = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('password_old', true);
        $sNewPass = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('password_new', true);
        $sConfPass = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('password_new_confirm', true);

        /** @var \OxidEsales\Eshop\Core\InputValidator $oInputValidator */
        $oInputValidator = \OxidEsales\Eshop\Core\Registry::getInputValidator();
        if (($oExcp = $oInputValidator->checkPassword($oUser, $sNewPass, $sConfPass, true))) {
            switch ($oExcp->getMessage()) {
                case 'ERROR_MESSAGE_INPUT_EMPTYPASS':
                case 'ERROR_MESSAGE_PASSWORD_TOO_SHORT':
                    return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(
                        'ERROR_MESSAGE_PASSWORD_TOO_SHORT',
                        false,
                        true
                    );
                default:
                    return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(
                        'ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH',
                        false,
                        true
                    );
            }
        }

        if (!$sOldPass || !$oUser->isSamePassword($sOldPass)) {
            /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
            $oUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();

            return $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_CURRENT_PASSWORD_INVALID', false, true);
        }

        // testing passed - changing password
        $oUser->setPassword($sNewPass);
        if ($oUser->save()) {
            $this->_blPasswordChanged = true;
            // deleting user autologin cookies.
            \OxidEsales\Eshop\Core\Registry::getUtilsServer()->deleteUserCookie($this->getConfig()->getShopId());
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
        $aPaths = array();
        $aPath = array();

        /** @var \OxidEsales\Eshop\Core\SeoEncoder $oSeoEncoder */
        $oSeoEncoder = \OxidEsales\Eshop\Core\Registry::getSeoEncoder();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
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
