<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \Exception;
use \oxfield;
use \oxException;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing forgotpwd class
 */
class ForgotpwdTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuser');
        oxDb::getDb()->execute("delete from oxremark where oxparentid = '_testArt'");
        oxDb::getDb()->execute("delete from oxnewssubscribed where oxuserid = '_testArt'");
        parent::tearDown();
    }

    /**
     * Test forgot email.
     *
     * @return null
     */
    public function testGetForgotEmail()
    {
        $this->setRequestParameter('lgn_usr', 'testuser');
        $oForgotPwd = $this->getProxyClass('forgotpwd');
        $oForgotPwd->forgotPassword();
        $this->assertFalse($oForgotPwd->getForgotEmail());
    }

    /**
     * Test get update id.
     *
     * @return null
     */
    public function testGetUpdateId()
    {
        $oView = oxNew('forgotpwd');
        $this->assertNull($oView->getUpdateId());

        $this->setRequestParameter('uid', 'testuid');
        $this->assertEquals('testuid', $oView->getUpdateId());
    }

    /**
     * Test show update screen.
     *
     * @return null
     */
    public function testShowUpdateScreen()
    {
        $oView = oxNew('forgotpwd');
        $this->assertFalse($oView->showUpdateScreen());

        $this->setRequestParameter('uid', 'testuid');
        $this->assertTrue($oView->showUpdateScreen());
    }

    /**
     * Test update success.
     *
     * @return null
     */
    public function testUpdateSuccess()
    {
        $oView = oxNew('forgotpwd');
        $this->assertFalse($oView->updateSuccess());

        $this->setRequestParameter('success', 'testsuccess');
        $this->assertTrue($oView->updateSuccess());
    }

    /**
     * Test update password using too short or unmaching passwords.
     *
     * @return null
     */
    public function testUpdatePasswordProblemsWithPass()
    {
        // overriding utility function
        oxTestModules::addFunction("oxUtilsView", "addErrorToDisplay", "{ throw new Exception( \$aA[0] ); }");

        $oView = oxNew('forgotpwd');

        // no pass
        $this->setRequestParameter('password_new', null);
        $this->setRequestParameter('password_new_confirm', null);
        try {
            $blExcp = false;
            $oView->updatePassword();
        } catch (Exception $oExcp) {
            $blExcp = $oExcp->getMessage() == oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_EMPTYPASS');
        }
        $this->assertTrue($blExcp);

        // pass does not match
        $this->setRequestParameter('password_new', 'aaaaaa');
        $this->setRequestParameter('password_new_confirm', 'bbbbbb');
        try {
            $blExcp = false;
            $oView->updatePassword();
        } catch (Exception $oExcp) {
            $blExcp = $oExcp->getMessage() == oxRegistry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH');
        }
        $this->assertTrue($blExcp);

        // pass too short
        $this->setRequestParameter('password_new', 'aaa');
        $this->setRequestParameter('password_new_confirm', 'aaa');
        try {
            $blExcp = false;
            $oView->updatePassword();
        } catch (Exception $oExcp) {
            $blExcp = $oExcp->getMessage() == oxRegistry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_TOO_SHORT');
        }

        $this->assertTrue($blExcp);
    }

    /**
     * Test update password with wrong/expired uid.
     *
     * @return null
     */
    public function testUpdatePasswordUnableToLoadUserByUid()
    {
        // overriding utility function
        oxTestModules::addFunction("oxUtilsView", "addErrorToDisplay", "{ throw new Exception( \$aA[0] ); }");

        $this->setRequestParameter('uid', 'aaaaaa');
        $this->setRequestParameter('password_new', 'aaaaaa');
        $this->setRequestParameter('password_new_confirm', 'aaaaaa');

        $oView = oxNew('forgotpwd');

        try {
            $oView->updatePassword();
        } catch (Exception $oExcp) {
            $blExcp = $oExcp->getMessage() == 'ERROR_MESSAGE_PASSWORD_LINK_EXPIRED';
        }

        $this->assertTrue($blExcp);
    }

    /**
     * Test update password.
     *
     * @return null
     */
    public function testUpdatePassword()
    {
        // adding test user
        $oUser = oxNew('oxuser');
        $oUser->setId('_testArt');
        $oUser->oxuser__oxshopid = new oxfield($this->getConfig()->getShopId());
        $oUser->setPassword('xxxxxx');
        $oUser->setUpdateKey();

        // overriding utility function
        $this->setRequestParameter('uid', $oUser->getUpdateId());
        $this->setRequestParameter('password_new', 'aaaaaa');
        $this->setRequestParameter('password_new_confirm', 'aaaaaa');

        $oView = oxNew('forgotpwd');
        $this->assertEquals('forgotpwd?success=1', $oView->updatePassword());
    }

    /**
     * Testing Account_Newsletter::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oF = oxNew('ForgotPwd');

        $this->assertEquals(1, count($oF->getBreadCrumb()));
    }

    /**
     * Test ForgotPwd::updatePassword() - try to set password with spec. chars.
     * #0003680
     *
     * @return null
     */
    public function testUpdatePassword_passwordSpecChars()
    {
        $oRealInputValidator = \OxidEsales\Eshop\Core\Registry::getInputValidator();

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('password_new', $sPass);
        $this->setRequestParameter('password_new_confirm', $sPass);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('checkPassword'));
        oxTestModules::addModuleObject('oxuser', $oUser);

        $oInputValidator = $this->getMock('oxInputValidator');
        $oInputValidator->expects($this->once())->method('checkPassword')->with($this->equalTo($oUser), $this->equalTo($sPass), $this->equalTo($sPass), $this->equalTo(true))->will($this->returnValue(new oxException()));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $oInputValidator);

        $oView = oxNew('ForgotPwd');
        $oView->updatePassword();

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $oRealInputValidator);
    }

    /**
     * Test if link is expired
     *
     * @return null
     */
    public function testIsExpiredLink()
    {
        $this->setRequestParameter('uid', 'aaaaaa');

        $oView = oxNew('forgotpwd');
        $this->assertTrue($oView->isExpiredLink());
    }

    /**
     * Test get title.
     */
    public function testGetTitle()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ForgotPasswordController::class, array('showUpdateScreen', 'updateSuccess'));
        $oView->expects($this->any())->method('showUpdateScreen')->will($this->returnValue(false));
        $oView->expects($this->any())->method('updateSuccess')->will($this->returnValue(false));

        $this->assertEquals(oxRegistry::getLang()->translateString('FORGOT_PASSWORD', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }

    /**
     * Test get title, when password update screen is shown
     */
    public function testGetTitle_ShowUpdateScreen()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ForgotPasswordController::class, array('showUpdateScreen', 'updateSuccess'));
        $oView->expects($this->any())->method('showUpdateScreen')->will($this->returnValue(true));
        $oView->expects($this->any())->method('updateSuccess')->will($this->returnValue(true));

        $this->assertEquals(oxRegistry::getLang()->translateString('NEW_PASSWORD', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }

    /**
     * Test get title, after successful password update
     */
    public function testGetTitle_UpdateSuccess()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ForgotPasswordController::class, array('showUpdateScreen', 'updateSuccess'));
        $oView->expects($this->any())->method('showUpdateScreen')->will($this->returnValue(false));
        $oView->expects($this->any())->method('updateSuccess')->will($this->returnValue(true));

        $this->assertEquals(oxRegistry::getLang()->translateString('CHANGE_PASSWORD', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }
}
