<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * Current user "My account" window.
 * When user is logged in arranges "My account" window, by creating
 * links to user details, order review, notice list, wish list. There
 * is a link for logging out. Template includes Topoffer , bargain
 * boxes. OXID eShop -> MY ACCOUNT.
 */
class AccountController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Number of user's orders.
     *
     * @var int
     */
    protected $_iOrderCnt = null;

    /**
     * Current article id.
     *
     * @var string
     */
    protected $_sArticleId = null;

    /**
     * Search parameter for Html.
     *
     * @var string
     */
    protected $_sSearchParamForHtml = null;

    /**
     * Search parameter.
     *
     * @var string
     */
    protected $_sSearchParam = null;

    /**
     * List type.
     *
     * @var string
     */
    protected $_sListType = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/dashboard.tpl';

    /**
     * Current class login template name.
     *
     * @var string
     */
    protected $_sThisLoginTemplate = 'page/account/login.tpl';

    /**
     * Alternative login template name.
     *
     * @var string
     */
    protected $_sThisAltLoginTemplate = 'page/privatesales/login.tpl';

    /**
     * Current view search engine indexing state.
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Start page meta description CMS ident.
     *
     * @var string
     */
    protected $_sMetaDescriptionIdent = 'oxstartmetadescription';

    /**
     * Start page meta keywords CMS ident.
     *
     * @var string
     */
    protected $_sMetaKeywordsIdent = 'oxstartmetakeywords';

    /**
     * Sign if to load and show bargain action.
     *
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Status of the account deletion.
     *
     * @var bool
     */
    private $accountDeletionStatus;

    /**
     * Loads action articles. If user is logged and returns name of
     * template to render account::_sThisTemplate.
     *
     * @return string current template file name
     */
    public function render()
    {
        parent::render();

        // performing redirect if needed
        $this->redirectAfterLogin();

        // is logged in ?
        $user = $this->getUser();
        $passwordField = 'oxuser__oxpassword';
        if (
            !$user || ($user && !$user->$passwordField->value) ||
            ($this->isEnabledPrivateSales() && $user && (!$user->isTermsAccepted() || $this->confirmTerms()))
        ) {
            $this->_sThisTemplate = $this->_getLoginTemplate();
        }

        return $this->_sThisTemplate;
    }

    /**
     * Returns login template name:
     *  - if "login" feature is on returns $this->_sThisAltLoginTemplate
     *  - else returns $this->_sThisLoginTemplate.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLoginTemplate" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getLoginTemplate()
    {
        return $this->isEnabledPrivateSales() ? $this->_sThisAltLoginTemplate : $this->_sThisLoginTemplate;
    }

    /**
     * Confirms term agreement. Returns value of confirmed term.
     *
     * @return string|bool
     */
    public function confirmTerms()
    {
        $termsConfirmation = Registry::getConfig()->getRequestParameter('term');
        if (!$termsConfirmation && $this->isEnabledPrivateSales()) {
            $user = $this->getUser();
            if ($user && !$user->isTermsAccepted()) {
                $termsConfirmation = true;
            }
        }

        return $termsConfirmation;
    }

    /**
     * Returns array from parent::getNavigationParams(). If current request
     * contains "sourcecl" and "anid" parameters - appends array with this
     * data. Array is used to fill forms and append shop urls with actual
     * state parameters.
     *
     * @return array
     */
    public function getNavigationParams()
    {
        $parameters = parent::getNavigationParams();

        if ($sourceClass = Registry::getConfig()->getRequestParameter('sourcecl')) {
            $parameters['sourcecl'] = $sourceClass;
        }

        if ($articleId = Registry::getConfig()->getRequestParameter('anid')) {
            $parameters['anid'] = $articleId;
        }

        return $parameters;
    }

    /**
     * For some user actions (like writing product
     * review) user must be logged in. So e.g. in product details page
     * there is a link leading to current view. Link contains parameter
     * "sourcecl", which tells where to redirect after successfull login.
     * If this parameter is defined and oxcmp_user::getLoginStatus() ==
     * USER_LOGIN_SUCCESS (means user has just logged in) then user is
     * redirected back to source view.
     */
    public function redirectAfterLogin()
    {
        // in case source class is provided - redirecting back to it with all default parameters
        if (
            ($sourceClass = Registry::getConfig()->getRequestParameter('sourcecl')) &&
            USER_LOGIN_SUCCESS === $this->_oaComponents['oxcmp_user']->getLoginStatus()
        ) {
            $redirectUrl = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopUrl() . 'index.php?cl=' . rawurlencode($sourceClass);

            // building redirect link
            foreach ($this->getNavigationParams() as $key => $value) {
                if ($value && 'sourcecl' !== $key) {
                    $redirectUrl .= '&' . rawurlencode($key) . '=' . rawurlencode($value);
                }
            }

            /** @var \OxidEsales\Eshop\Core\UtilsUrl $utilsUrl */
            $utilsUrl = Registry::getUtilsUrl();

            return Registry::getUtils()->redirect($utilsUrl->processUrl($redirectUrl), true, 302);
        }
    }

    /**
     * changes default template for compare in popup.
     */
    public function getOrderCnt()
    {
        if (null === $this->_iOrderCnt) {
            $this->_iOrderCnt = 0;
            if ($user = $this->getUser()) {
                $this->_iOrderCnt = $user->getOrderCount();
            }
        }

        return $this->_iOrderCnt;
    }

    /**
     * Return the active article id.
     *
     * @return string|bool
     */
    public function getArticleId()
    {
        if (null === $this->_sArticleId) {
            // passing wishlist information
            if ($articleId = Registry::getConfig()->getRequestParameter('aid')) {
                $this->_sArticleId = $articleId;
            }
        }

        return $this->_sArticleId;
    }

    /**
     * Template variable getter. Returns search parameter for Html.
     *
     * @return string
     */
    public function getSearchParamForHtml()
    {
        if (null === $this->_sSearchParamForHtml) {
            $this->_sSearchParamForHtml = false;
            if ($this->getArticleId()) {
                $this->_sSearchParamForHtml = Registry::getConfig()->getRequestParameter('searchparam');
            }
        }

        return $this->_sSearchParamForHtml;
    }

    /**
     * Template variable getter. Returns search parameter.
     *
     * @return string
     */
    public function getSearchParam()
    {
        if (null === $this->_sSearchParam) {
            $this->_sSearchParam = false;
            if ($this->getArticleId()) {
                $this->_sSearchParam = rawurlencode(Registry::getConfig()->getRequestParameter('searchparam', true));
            }
        }

        return $this->_sSearchParam;
    }

    /**
     * Template variable getter. Returns list type.
     *
     * @return string
     */
    public function getListType()
    {
        if (null === $this->_sListType) {
            $this->_sListType = false;
            if ($this->getArticleId()) {
                // searching in vendor #671
                $this->_sListType = Registry::getConfig()->getRequestParameter('listtype');
            }
        }

        return $this->_sListType;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $paths = [];
        $pathData = [];
        $language = Registry::getLang();
        $baseLanguageId = $language->getBaseLanguage();
        if ($user = $this->getUser()) {
            $username = $user->oxuser__oxusername->value;
            $pathData['title'] = $language->translateString('MY_ACCOUNT', $baseLanguageId, false) . ' - ' . $username;
        } else {
            $pathData['title'] = $language->translateString('LOGIN', $baseLanguageId, false);
        }
        $pathData['link'] = $this->getLink();
        $paths[] = $pathData;

        return $paths;
    }

    /**
     * Template variable getter. Returns article list count in comparison.
     *
     * @return int
     */
    public function getCompareItemsCnt()
    {
        $compare = oxNew(\OxidEsales\Eshop\Application\Controller\CompareController::class);

        return $compare->getCompareItemsCnt();
    }

    /**
     * Page Title.
     *
     * @return string
     */
    public function getTitle()
    {
        $title = parent::getTitle();

        if ('account' === \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getClassName()) {
            $baseLanguageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
            $title = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('PAGE_TITLE_ACCOUNT', $baseLanguageId, false);
            if ($user = $this->getUser()) {
                $username = $user->oxuser__oxusername->value;
                $title .= ' - "' . $username . '"';
            }
        }

        return $title;
    }

    /**
     * Deletes User account.
     */
    public function deleteAccount(): void
    {
        $this->accountDeletionStatus = false;
        $user = $this->getUser();

        /*
         * Setting derived to false allows mall users to delete their account being in a different shop as the shop
         * the account was originally created in.
         */
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blMallUsers')) {
            $user->setIsDerived(false);
        }

        if ($this->canUserAccountBeDeleted() && $user->delete()) {
            $this->accountDeletionStatus = true;
            $user->logout();
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $session->destroy();
        }
    }

    /**
     * Returns true if User is allowed to delete own account.
     *
     * @return bool
     */
    public function isUserAllowedToDeleteOwnAccount()
    {
        $allowUsersToDeleteTheirAccount = Registry::getConfig()->getConfigParam('blAllowUsersToDeleteTheirAccount');

        $user = $this->getUser();

        return $allowUsersToDeleteTheirAccount && $user && !$user->isMallAdmin();
    }

    /**
     * Template variable getter. Returns true, if a user account has been sucessfully deleted, else false.
     *
     * @return bool
     */
    public function getAccountDeletionStatus()
    {
        return $this->accountDeletionStatus;
    }

    /**
     * Checks if possible to delete user.
     *
     * @return bool
     */
    private function canUserAccountBeDeleted()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();

        return $session->checkSessionChallenge() && $this->isUserAllowedToDeleteOwnAccount();
    }
}
