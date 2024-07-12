<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\Eshop\Application\Controller\AccountController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\TestingLibrary\UnitTestCase;
use \oxTestModules;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests for Account class
 */
class AccountControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test view render().
     */
    public function testRenderConfirmTerms()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['confirmTerms', 'getUser']);
        $oUserView->expects($this->any())->method('confirmTerms')->will($this->returnValue(true));
        $oUserView->expects($this->any())->method('getUser')->will($this->returnValue(true));
        $this->assertEquals('page/privatesales/login', $oUserView->render());
    }

    /**
     * Test view render().
     */
    public function testRenderNoTerms()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxpassword = new Field("psw");
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['confirmTerms', 'getUser', 'getOrderCnt', "isEnabledPrivateSales"]);
        $oUserView->expects($this->any())->method('confirmTerms')->will($this->returnValue(false));
        $oUserView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oUserView->expects($this->any())->method('getOrderCnt');
        $oUserView->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));
        $this->assertEquals('page/account/dashboard', $oUserView->render());
    }

    /**
     * Test confirmTerms().
     */
    public function testConfirmTerms()
    {
        $oView = oxNew('account');
        $this->setRequestParameter('term', '2');
        $this->assertEquals('2', $oView->confirmTerms());
    }

    /**
     * Test confirmTerms().
     */
    public function testConfirmTermsForPrivateSales()
    {
        $this->setRequestParameter('term', false);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["isEnabledPrivateSales", "getUser"]);
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["isTermsAccepted"]);

        $oView->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));
        $oUser->expects($this->once())->method("isTermsAccepted")->will($this->returnValue(false));
        $oView->expects($this->once())->method("getUser")->will($this->returnValue($oUser));

        $this->assertTrue($oView->confirmTerms());
    }

    /**
     * Test get list type.
     */
    public function testGetListType()
    {
        $this->setRequestParameter('listtype', 'testListType');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["getArticleId"]);
        $oView->expects($this->once())->method("getArticleId")->will($this->returnValue(false));
        $this->assertFalse($oView->getListType());

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["getArticleId"]);
        $oView->expects($this->once())->method("getArticleId")->will($this->returnValue(true));
        $this->assertEquals('testListType', $oView->getListType());
    }

    /**
     * Test get search parameter.
     */
    public function testGetSearchParam()
    {
        $this->setRequestParameter('searchparam', 'testparam');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["getArticleId"]);
        $oView->expects($this->exactly(2))->method("getArticleId")->will($this->returnValue(false));
        $this->assertFalse($oView->getSearchParam());
        $this->assertFalse($oView->getSearchParamForHtml());

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["getArticleId"]);
        $oView->expects($this->exactly(2))->method("getArticleId")->will($this->returnValue(true));
        $this->assertEquals('testparam', $oView->getSearchParam());
        $this->assertEquals('testparam', $oView->getSearchParamForHtml());
    }

    /**
     * Test get article id.
     */
    public function testGetArticleId()
    {
        $this->setRequestParameter('aid', null);

        $oView = oxNew('account');
        $this->assertNull($oView->getArticleId());

        $this->setRequestParameter('aid', 'testaid');

        $oView = oxNew('account');
        $this->assertEquals('testaid', $oView->getArticleId());
    }

    /**
     * Test get order count.
     */
    public function testGetOrderCnt()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["getUser"]);
        $oView->expects($this->once())->method("getUser")->will($this->returnValue(false));
        $this->assertEquals(0, $oView->getOrderCnt());

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getOrderCount"]);
        $oUser->expects($this->once())->method("getOrderCount")->will($this->returnValue(999));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ["getUser"]);
        $oView->expects($this->once())->method("getUser")->will($this->returnValue($oUser));
        $this->assertEquals(999, $oView->getOrderCnt());
    }

    /**
     * Test redirect after login.
     */
    public function testRedirectAfterLoginShouldNotRedirect()
    {
        $this->setRequestParameter('sourcecl', null);

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, ["getLoginStatus"]);
        $oCmp->expects($this->never())->method("getLoginStatus")->will($this->returnValue(1));

        $oView = $this->getProxyClass("Account");
        $oView->setNonPublicVar("_oaComponents", ["oxcmp_user" => $oCmp]);
        $this->assertNull($oView->redirectAfterLogin());
    }

    /**
     * Test redirect after login.
     */
    public function testRedirectAfterLogin()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ return$aA[0];}');

        $this->setRequestParameter('sourcecl', 'testsource');
        $this->setRequestParameter('anid', 'testanid');

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, ["getLoginStatus"]);
        $oCmp->expects($this->once())->method("getLoginStatus")->will($this->returnValue(1));

        $oView = $this->getProxyClass("Account");
        $oView->setNonPublicVar("_oaComponents", ["oxcmp_user" => $oCmp]);

        $sParams = '';
        // building redirect link
        foreach ($oView->getNavigationParams() as $sName => $sValue) {
            if ($sValue && $sName != "sourcecl") {
                $sParams .= '&' . rawurlencode((string) $sName) . "=" . rawurlencode((string) $sValue);
            }
        }

        $sUrl = $this->getConfig()->getShopUrl() . 'index.php?cl=testsource' . $sParams;
        $this->assertEquals($sUrl, $oView->redirectAfterLogin());
    }

    /**
     * Test get navigation parameters.
     */
    public function testGetNavigationParams()
    {
        $this->setRequestParameter('sourcecl', 'testsource');
        $this->setRequestParameter('anid', 'testanid');

        $oView = oxNew('Account');
        $aNavParams = $oView->getNavigationParams();

        $this->assertTrue(isset($aNavParams['sourcecl']));
        $this->assertTrue(isset($aNavParams['anid']));
    }

    /**
     * Test view render.
     */
    public function testRender()
    {
        $this->setRequestParameter('aid', 'testanid');

        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new Field(1);

        $oView = $this->getMock(
            "account",
            ["redirectAfterLogin", "getUser", "isEnabledPrivateSales"]
        );

        $oView->expects($this->once())->method("redirectAfterLogin")->will($this->returnValue(1));
        $oView->expects($this->once())->method("getUser")->will($this->returnValue($oUser));
        $oView->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $this->assertEquals('page/account/dashboard', $oView->render());
    }

    /**
     * Test view render.
     */
    public function testRenderNoUser()
    {
        $this->setRequestParameter('aid', 'testanid');
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new Field(1);

        $oView = $this->getMock(
            "account",
            ["redirectAfterLogin", "getUser", 'isActive']
        );

        $oView->expects($this->once())->method("redirectAfterLogin")->will($this->returnValue(1));
        $oView->expects($this->once())->method("getUser")->will($this->returnValue(false));
        $oView->expects($this->any())->method('isActive')->will($this->returnValue(true));

        $this->assertEquals('page/privatesales/login', $oView->render());
    }

    /**
     * Test Account::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $sUsername = 'Username';
        $sLink = 'Link url';
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxusername = new Field($sUsername);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser', 'getLink']);
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->once())->method('getLink')->will($this->returnValue($sLink));

        $aBreadCrumbs = $oView->getBreadCrumb();

        $this->assertTrue(is_array($aBreadCrumbs));
        $this->assertEquals(1, count($aBreadCrumbs));
        $this->assertTrue(isset($aBreadCrumbs[0]['title']));
        $this->assertTrue(isset($aBreadCrumbs[0]['link']));
        $this->assertEquals('Mein Konto - ' . $sUsername, $aBreadCrumbs[0]['title']);
        $this->assertEquals($sLink, $aBreadCrumbs[0]['link']);
    }

    /**
     * Test Account::getBreadCrumb()
     */
    public function testGetBreadCrumbNoUser()
    {
        $oAcc = oxNew('account');
        $aBreadCrumbs = $oAcc->getBreadCrumb();

        $this->assertTrue(is_array($aBreadCrumbs));
        $this->assertEquals(1, count($aBreadCrumbs));
        $this->assertTrue(isset($aBreadCrumbs[0]['title']));
        $this->assertTrue(isset($aBreadCrumbs[0]['link']));
        $this->assertEquals('Anmelden', $aBreadCrumbs[0]['title']);
    }

    /**
     * Testing account::getCompareItemsCnt()
     */
    public function testGetCompareItemsCnt()
    {
        $this->getSession()->setVariable('aFiltcompproducts', ['1', '2']);

        $oAcc = oxNew('account');
        $this->assertEquals(2, $oAcc->getCompareItemsCnt());
    }

    /**
     * Testing account::getCompareItemsCnt()
     */
    public function testGetTitle()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new Field('Jon');

        $oActiveView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getClassKey']);
        $oActiveView->expects($this->any())->method('getClassKey')->will($this->returnValue('account'));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveView'], [], '', false);
        $oConfig->expects($this->any())->method('getActiveView')->will($this->returnValue($oActiveView));


        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser', 'getConfig']);
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals(Registry::getLang()->translateString('PAGE_TITLE_ACCOUNT', Registry::getLang()->getBaseLanguage(), false) . ' - "Jon"', $oView->getTitle());
    }

    public function testDeleteUserAccountWhenSessionChallengeValidAndFeatureEnabled()
    {
        $this->isAccountDeletionEnabled(true);
        $this->isSessionTokenValid(true);

        $user = $this->getUserMockForDeletion();
        $user->expects($this->once())->method('delete');
        $this->executeAccountDeletion($user);
    }

    public function testDeleteUserAccountWhenSessionChallengeInvalid()
    {
        $this->isAccountDeletionEnabled(true);
        $this->isSessionTokenValid(false);

        $user = $this->getUserMockForDeletion();
        $user->expects($this->never())->method('delete');
        $this->executeAccountDeletion($user);
    }

    public function testDeleteUserAccountWhenFeatureDisabled()
    {
        $this->isAccountDeletionEnabled(false);
        $this->isSessionTokenValid(true);

        $user = $this->getUserMockForDeletion();
        $user->expects($this->never())->method('delete');
        $this->executeAccountDeletion($user);
    }

    public function testAccountDeletionStatusIsFalse()
    {
        $this->isAccountDeletionEnabled(false);

        $user = $this->getUserMockForDeletion();
        $accountController = oxNew(AccountController::class);
        $accountController->setUser($user);
        $accountController->deleteAccount();

        $actualStatus = $accountController->getAccountDeletionStatus();
        $this->assertFalse($actualStatus);
    }

    public function testAccountDeletionStatusIsTrue()
    {
        $this->isAccountDeletionEnabled(true);
        $this->isSessionTokenValid(true);

        $userMock = $this->getUserMockForDeletion();
        $userMock
            ->expects($this->any())
            ->method('delete')
            ->will($this->returnValue(true));
        $accountController = oxNew(AccountController::class);
        $accountController->setUser($userMock);
        $accountController->deleteAccount();

        $actualStatus = $accountController->getAccountDeletionStatus();
        $this->assertTrue($actualStatus);
    }

    public function testGetReviewAndRatingItemsCountWhenUserIsNotLoggedIn()
    {
        $controller = oxNew(AccountController::class);
        $this->getConfig()->setUser(null);
        $this->assertSame(0, $controller->getReviewAndRatingItemsCount());
    }

    /**
     * @param bool $isValid
     */
    private function isSessionTokenValid($isValid)
    {
        $sessionToken = $isValid ? 'valid_token' : 'invalid_token';
        $this->setSessionParam('sess_stoken', $sessionToken);
        $this->setRequestParameter('stoken', 'valid_token');
        $this->stubSessionDestroyMethod();
    }

    /**
     * @param User $user
     */
    private function executeAccountDeletion($user)
    {
        $accountController = oxNew(AccountController::class);
        $accountController->setUser($user);
        $accountController->deleteAccount();
    }

    private function stubSessionDestroyMethod()
    {
        /** @var PHPUnit\Framework\MockObject\MockObject|Session $session */
        $session = $this->getMockBuilder(Session::class)->setMethods(['destroy'])->getMock();
        $session->expects($this->any())->method('destroy');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);
    }

    /**
     * @param bool $isEnabled
     */
    private function isAccountDeletionEnabled($isEnabled)
    {
        return $this->getConfig()->setConfigParam('blAllowUsersToDeleteTheirAccount', $isEnabled);
    }

    /**
     * @return PHPUnit\Framework\MockObject\MockObject|User
     */
    private function getUserMockForDeletion()
    {
        return $this->getMockBuilder(User::class)->setMethods(['delete'])->getMock();
    }
}
