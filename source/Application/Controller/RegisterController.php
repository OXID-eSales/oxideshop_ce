<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxField;
use OxidEsales\Eshop\Core\Registry;
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
        return Registry::getConfig()->getRequestParameter('newslettererror');
    }

    /**
     * Return registration status (if it was set)
     *
     * @return int | null
     */
    public function getRegistrationStatus()
    {
        return Registry::getConfig()->getRequestParameter('success');
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
            Registry::getSession()->setVariable('usr', $oUser->getId());

            // redirecting to confirmation page
            return 'register?confirmstate=1';
        } else {
            // confirmation failed
            Registry::getUtilsView()->addErrorToDisplay('REGISTER_ERRLINKEXPIRED', false, true);

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
        return Registry::getConfig()->getRequestParameter('uid');
    }

    /**
     * Returns confirmation state: "1" - success, "-1" - error
     *
     * @return int
     */
    public function isConfirmed()
    {
        return (bool) Registry::getConfig()->getRequestParameter("confirmstate");
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
        $aPath['title'] = Registry::getLang()->translateString('REGISTER', $iBaseLanguage, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
