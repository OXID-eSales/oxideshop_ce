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

use oxField;
use oxRegistry;

/**
 * User registration window.
 * Collects and arranges user object data (information, like shipping address, etc.).
 */
class RegisterController extends \OxidEsales\Eshop\Application\Controller\UserController
{

    /**
     * Current class template.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/register.tpl';

    /**
     * Successful registration confirmation template
     *
     * @var string
     */
    protected $_sSuccessTemplate = 'page/account/register_success.tpl';

    /**
     * Successful Confirmation state template name
     *
     * @var string
     */
    protected $_sConfirmTemplate = 'page/account/register_confirm.tpl';

    /**
     * Order step marker
     *
     * @var bool
     */
    protected $_blIsOrderStep = false;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Executes parent::render(), passes error code to template engine,
     * returns name of template to render register::_sThisTemplate.
     *
     * @return string   current template file name
     */
    public function render()
    {
        parent::render();

        // checking registration status
        if ($this->isEnabledPrivateSales() && $this->isConfirmed()) {
            $sTemplate = $this->_sConfirmTemplate;
        } elseif ($this->getRegistrationStatus()) {
            $sTemplate = $this->_sSuccessTemplate;
        } else {
            $sTemplate = $this->_sThisTemplate;
        }

        return $sTemplate;
    }

    /**
     * Returns registration error code (if it was set)
     *
     * @return int | null
     */
    public function getRegistrationError()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('newslettererror');
    }

    /**
     * Return registration status (if it was set)
     *
     * @return int | null
     */
    public function getRegistrationStatus()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('success');
    }

    /**
     * Check if field is required.
     *
     * @param string $sField required field to check
     *
     * @return bool
     */
    public function isFieldRequired($sField)
    {
        return isset($this->getMustFillFields()[$sField]);
    }

    /**
     * Registration confirmation functionality. If registration
     * succeded - redirects to success page, if not - returns
     * exception informing about expired confirmation link
     *
     * @return mixed
     */
    public function confirmRegistration()
    {
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($oUser->loadUserByUpdateId($this->getUpdateId())) {
            // resetting update key parameter
            $oUser->setUpdateKey(true);

            // saving ..
            $oUser->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field(1);
            $oUser->save();

            // forcing user login
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('usr', $oUser->getId());

            // redirecting to confirmation page
            return 'register?confirmstate=1';
        } else {
            // confirmation failed
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('REGISTER_ERRLINKEXPIRED', false, true);

            // redirecting to confirmation page
            return 'account';
        }
    }

    /**
     * Returns special id used for password update functionality
     *
     * @return string
     */
    public function getUpdateId()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('uid');
    }

    /**
     * Returns confirmation state: "1" - success, "-1" - error
     *
     * @return int
     */
    public function isConfirmed()
    {
        return (bool) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("confirmstate");
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

        $iBaseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('REGISTER', $iBaseLanguage, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
