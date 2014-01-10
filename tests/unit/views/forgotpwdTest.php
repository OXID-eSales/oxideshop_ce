<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Testing forgotpwd class
 */
class Unit_Views_forgotpwdTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable( 'oxuser' );
        oxDb::getDb()->execute( "delete from oxremark where oxparentid = '_testArt'" );
        oxDb::getDb()->execute( "delete from oxnewssubscribed where oxuserid = '_testArt'" );
        parent::tearDown();
    }

    /**
     * Test forgot email.
     *
     * @return null
     */
    public function testGetForgotEmail()
    {
        modConfig::setParameter( 'lgn_usr', 'testuser' );
        $oForgotPwd = $this->getProxyClass( 'forgotpwd' );
        $oForgotPwd->forgotPassword();
        $this->assertFalse( $oForgotPwd->getForgotEmail() );
    }

    /**
     * Test get update id.
     *
     * @return null
     */
    public function testGetUpdateId()
    {
        $oView = new forgotpwd();
        $this->assertNull( $oView->getUpdateId() );

        modConfig::setParameter( 'uid', 'testuid' );
        $this->assertEquals( 'testuid', $oView->getUpdateId() );
    }

    /**
     * Test show update screen.
     *
     * @return null
     */
    public function testShowUpdateScreen()
    {
        $oView = new forgotpwd();
        $this->assertFalse( $oView->showUpdateScreen() );

        modConfig::setParameter( 'uid', 'testuid' );
        $this->assertTrue( $oView->showUpdateScreen() );
    }

    /**
     * Test update success.
     *
     * @return null
     */
    public function testUpdateSuccess()
    {
        $oView = new forgotpwd();
        $this->assertFalse( $oView->updateSuccess() );

        modConfig::setParameter( 'success', 'testsuccess' );
        $this->assertTrue( $oView->updateSuccess() );
    }

    /**
     * Test update password using too short or unmaching passwords.
     *
     * @return null
     */
    public function testUpdatePasswordProblemsWithPass()
    {
        // overriding utility function
        oxTestModules::addFunction( "oxUtilsView", "addErrorToDisplay", "{ throw new Exception( \$aA[0] ); }");

        $oView = new forgotpwd();

        // no pass
        modConfig::setParameter( 'password_new', null );
        modConfig::setParameter( 'password_new_confirm', null );
        try {
            $blExcp = false;
            $oView->updatePassword();
        } catch ( Exception $oExcp ) {
            $blExcp = $oExcp->getMessage() == 'FORGOTPWD_ERRPASSWORDTOSHORT';
        }
        $this->assertTrue( $blExcp );

        // pass does not match
        modConfig::setParameter( 'password_new', 'aaaaaa' );
        modConfig::setParameter( 'password_new_confirm', 'bbbbbb' );
        try {
            $blExcp = false;
            $oView->updatePassword();
        } catch ( Exception $oExcp ) {
            $blExcp = $oExcp->getMessage() == 'FORGOTPWD_ERRPASSWDONOTMATCH';
        }
        $this->assertTrue( $blExcp );

        // pass too short
        modConfig::setParameter( 'password_new', 'aaa' );
        modConfig::setParameter( 'password_new_confirm', 'aaa' );
        try {
            $blExcp = false;
            $oView->updatePassword();
        } catch ( Exception $oExcp ) {
            $blExcp = $oExcp->getMessage() == 'FORGOTPWD_ERRPASSWORDTOSHORT';
        }

        $this->assertTrue( $blExcp );
    }

    /**
     * Test update password with wrong/expired uid.
     *
     * @return null
     */
    public function testUpdatePasswordUnableToLoadUserByUid()
    {
        // overriding utility function
        oxTestModules::addFunction( "oxUtilsView", "addErrorToDisplay", "{ throw new Exception( \$aA[0] ); }");

        modConfig::setParameter( 'uid', 'aaaaaa' );
        modConfig::setParameter( 'password_new', 'aaaaaa' );
        modConfig::setParameter( 'password_new_confirm', 'aaaaaa' );

        $oView = new forgotpwd();

        try {
            $oView->updatePassword();
        } catch ( Exception $oExcp ) {
            $blExcp = $oExcp->getMessage() == 'FORGOTPWD_ERRLINKEXPIRED';
        }

        $this->assertTrue( $blExcp );
    }

    /**
     * Test update password.
     *
     * @return null
     */
    public function testUpdatePassword()
    {
        // adding test user
        $oUser = new oxuser();
        $oUser->setId( '_testArt' );
        $oUser->oxuser__oxshopid = new oxfield( oxConfig::getInstance()->getShopId() );
        $oUser->setPassword( 'xxxxxx' );
        $oUser->setUpdateKey();

        // overriding utility function
        modConfig::setParameter( 'uid', $oUser->getUpdateId() );
        modConfig::setParameter( 'password_new', 'aaaaaa' );
        modConfig::setParameter( 'password_new_confirm', 'aaaaaa' );

        $oView = new forgotpwd();
        $this->assertEquals( 'forgotpwd?success=1', $oView->updatePassword() );
    }

    /**
     * Testing Account_Newsletter::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oF = new ForgotPwd();

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
        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        modConfig::setParameter( 'password_new', $sPass );
        modConfig::setParameter( 'password_new_confirm', $sPass );

        $oUser = $this->getMock( 'oxStdClass', array( 'checkPassword' ) );
        $oUser->expects( $this->once() )->method( 'checkPassword' )->with( $this->equalTo( $sPass ), $this->equalTo( $sPass ), $this->equalTo( true ) )->will( $this->returnValue( new oxException() ) );
        oxTestModules::addModuleObject( 'oxuser', $oUser );

        $oView = new ForgotPwd();
        $oView->updatePassword();
    }
}
