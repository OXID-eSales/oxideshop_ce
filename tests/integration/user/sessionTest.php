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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Integration_User_SessionTest extends OxidTestCase
{
    const FIRST_ARTICLE_ID = '0963c9792aea84adff1d2ef8aa4a7679';
    const SECOND_ARTICLE_ID = '09646538b54bac72b4ccb92fb5e3649f';

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        oxRegistry::getSession()->delBasket();

        $this->setConfigParam('blPerfNoBasketSaving', false);
        $this->_createUsers();
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuser');

        parent::tearDown();
    }

    /**
     * Test case that user has items in basket, logs out and later
     * logs in again.
     */
    public function testUserRestoreBasketOnNextLogin()
    {
        $this->_fillBasketForLoggedInUser('firstuser@oxideshop.dev', 'asdfasdf', self::FIRST_ARTICLE_ID);

        //user logs in again, saved basket is restored
        $oUser = oxNew('oxuser');
        $oUser->login('firstuser@oxideshop.dev', 'asdfasdf');

        $oBasket = oxRegistry::getSession()->getBasket();
        $oBasket->load();
        $oBasket->onUpdate();
        $aArticles = $oBasket->getBasketSummary()->aArticles;
        $this->assertSame(1, count($aArticles));
        $this->assertSame(1, (int) $aArticles[self::FIRST_ARTICLE_ID]);
    }

    /**
     * Test case that user has items in basket, logs out and other
     * user logs in using same session. Mimics the case when first
     * user leaves browser while session still lives and next user
     * takes over.
     */
    public function testUserRestoreBasketOnNextLoginOtherUser()
    {
        $this->_fillBasketForLoggedInUser('firstuser@oxideshop.dev', 'asdfasdf', self::FIRST_ARTICLE_ID);
        $this->_fillBasketForLoggedInUser('seconduser@oxideshop.dev', 'qwerqwer', self::SECOND_ARTICLE_ID);

        //first user logs in again, let's check his restored basket
        $oUser = oxNew('oxuser');
        $oUser->login('firstuser@oxideshop.dev', 'asdfasdf');

        $oBasket = oxRegistry::getSession()->getBasket();
        $oBasket->load();
        $oBasket->onUpdate();
        $oArticles = $oBasket->getBasketSummary()->aArticles;
        $this->assertSame(1, count($oArticles));
        $this->assertSame(1, (int) $oArticles[self::FIRST_ARTICLE_ID]);
    }

    /**
     * Test case that one user has items in basket, logs out and other
     * user logs in using same session.
     */
    public function testUserBasketOnNextLoginOtherUser()
    {
        $this->_fillBasketForLoggedInUser('firstuser@oxideshop.dev', 'asdfasdf', self::FIRST_ARTICLE_ID);

        //second user logs in, as he has no saved basket, basket should be empty now
        $oUser = oxNew('oxuser');
        $oUser->login('seconduser@oxideshop.dev', 'qwerqwer');

        $oBasket = oxRegistry::getSession()->getBasket();
        $oBasket->load();
        $oBasket->onUpdate();
        $oArticles = $oBasket->getBasketSummary()->aArticles;
        $this->assertSame(0, count($oArticles));
    }

    /**
     * Test helper inserts new user
     */
    private function _createUsers()
    {
        $aFirstUser = array(
            'oxuser__oxactive'    => new oxField('1', oxField::T_RAW),
            'oxuser__oxrights'    => new oxField('user', oxField::T_RAW),
            'oxuser__oxusername'  => new oxField('firstuser@oxideshop.dev', oxField::T_RAW),
            'oxuser__oxpassword'  => new oxField('c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                 'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
                oxField::T_RAW), //password is asdfasdf
            'oxuser__oxpasssalt'  => new oxField('3ddda7c412dbd57325210968cd31ba86', oxField::T_RAW),
            'oxuser__oxcustnr'    => new oxField('665', oxField::T_RAW),
            'oxuser__oxfname'     => new oxField('Bla', oxField::T_RAW),
            'oxuser__oxlname'     => new oxField('Foo', oxField::T_RAW),
            'oxuser__oxstreet'    => new oxField('blafoostreet', oxField::T_RAW),
            'oxuser__oxstreetnr'  => new oxField('123', oxField::T_RAW),
            'oxuser__oxcity'      => new oxField('Hamburg', oxField::T_RAW),
            'oxuser__oxcountryid' => new oxField('a7c40f631fc920687.20179984', oxField::T_RAW),
            'oxuser__oxzip'       => new oxField('22769', oxField::T_RAW),
            'oxuser__oxsal'       => new oxField('MR', oxField::T_RAW),
            'oxuser__oxactive'    => new oxField('1', oxField::T_RAW),
            'oxuser__oxboni'      => new oxField('1000', oxField::T_RAW),
            'oxuser__oxcreate'    => new oxField('2015-05-20 22:10:51', oxField::T_RAW),
            'oxuser__oxregister'  => new oxField('2015-05-20 22:10:51', oxField::T_RAW),
            'oxuser__oxboni'      => new oxField('1000', oxField::T_RAW)
        );

        $aSecondUser = $aFirstUser;
        $aSecondUser['oxuser__oxusername'] = new oxField('seconduser@oxideshop.dev', oxField::T_RAW);
        $aSecondUser['oxuser__oxpassword'] = new oxField('c1e113149bcc7737d1f0f91b0510f6cfb60697a5b654e9f49786d59e00e28' .
                                                        '1168c209de99baf94626fa0604794cc4b469a7b768c260cf5c0d1d1ea0c9933effe',
            oxField::T_RAW); //password is qwerqwer
        $aSecondUser['oxuser__oxpasssalt'] = new oxField('e25b237fce506256b3a151256c410ab2', oxField::T_RAW);
        $aSecondUser['oxuser__oxcustnr'] = new oxField('667', oxField::T_RAW);
        $aSecondUser['oxuser__oxfname'] = new oxField('Foo', oxField::T_RAW);
        $aSecondUser['oxuser__oxlname'] = new oxField('Bla', oxField::T_RAW);

        $this->_insertUser($aFirstUser);
        $this->_insertUser($aSecondUser);
    }

    /**
     * Create a user
     *
     * @param $data
     *
     * @return string
     */
    private function _insertUser($aData)
    {
        $sUserOxid = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);

        $oUser = oxNew('oxUser');
        $oUser->setId($sUserOxid);

        foreach ($aData as $sKey => $sValue) {
            $oUser->$sKey = $sValue;
        }
        $oUser->save();

        return $sUserOxid;
    }

    /**
     * Test helper, fill basket for given user.
     *
     * @param $oUser
     * @param $sArticleId
     *
     * @return oxBasket
     */
    private function _getFilledBasketForUser($oUser, $sArticleId)
    {
        $oBasket = oxnew('oxbasket');
        $oBasket->setBasketUser($oUser);
        $oBasket->addToBasket($sArticleId, 1);
        $oBasket->calculateBasket(true); //only saved on calculate, oxuserbaskets.oxtitle = 'savedbasket'

        $oBasket->onUpdate();
        $oArticles = $oBasket->getBasketSummary()->aArticles;
        $this->assertSame(1, (int) $oArticles[$sArticleId]);

        return $oBasket;
    }

    /**
     * Test helper, fill basket for logged in User
     *
     * @param $oUsername
     * @param $password
     * @param $sArticleId
     */
    private function _fillBasketForLoggedInUser($oUsername, $password, $sArticleId)
    {
        $this->setRequestParam('lgn_usr', $oUsername);
        $this->setRequestParam('lgn_pwd', $password);

        $parent = $this->getMock('oxuser', array('isEnabledPrivateSales'));
        $parent->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserComponent = oxNew('oxcmp_user');
        $oUserComponent->setParent($parent);

        $oUserComponent->login();

        $oUser = $oUserComponent->getUser();
        $oBasket = $this->_getFilledBasketForUser($oUser, $sArticleId);
        oxRegistry::getSession()->setBasket($oBasket);

        $oUserComponent->logout();

    }

}
