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
namespace Integration\User;

use oxBasket;
use oxField;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxRegistry;
use oxUser;

class SessionTest extends UnitTestCase
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

    /**
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
        $this->fillBasketForLoggedInUser('firstuser@oxideshop.dev', 'asdfasdf', self::FIRST_ARTICLE_ID);

        //use logs in again, saved basket is restored
        $user = oxNew('oxUser');
        $user->login('firstuser@oxideshop.dev', 'asdfasdf');

        $basket = oxRegistry::getSession()->getBasket();
        $basket->load();
        $basket->onUpdate();
        $articles = $basket->getBasketSummary()->aArticles;
        $this->assertSame(1, count($articles));
        $this->assertSame(1, (int) $articles[self::FIRST_ARTICLE_ID]);
    }

    /**
     * Test case that user has items in basket, logs out and other
     * user logs in using same session. Mimics the case when first
     * user leaves browser while session still lives and next user
     * takes over.
     */
    public function testUserRestoreBasketOnNextLoginOtherUser()
    {
        $this->fillBasketForLoggedInUser('firstuser@oxideshop.dev', 'asdfasdf', self::FIRST_ARTICLE_ID);
        $this->fillBasketForLoggedInUser('seconduser@oxideshop.dev', 'qwerqwer', self::SECOND_ARTICLE_ID);

        //first user logs in again, let's check his restored basket
        $user = oxNew('oxuser');
        $user->login('firstuser@oxideshop.dev', 'asdfasdf');

        $basket = oxRegistry::getSession()->getBasket();
        $basket->load();
        $basket->onUpdate();
        $articles = $basket->getBasketSummary()->aArticles;
        $this->assertSame(1, count($articles));
        $this->assertSame(1, (int) $articles[self::FIRST_ARTICLE_ID]);
    }

    /**
     * Test case that one user has items in basket, logs out and other
     * user logs in using same session.
     */
    public function testUserBasketOnNextLoginOtherUser()
    {
        $this->fillBasketForLoggedInUser('firstuser@oxideshop.dev', 'asdfasdf', self::FIRST_ARTICLE_ID);

        //second user logs in, as he has no saved basket, basket should be empty now
        $user = oxNew('oxuser');
        $user->login('seconduser@oxideshop.dev', 'qwerqwer');

        $basket = oxRegistry::getSession()->getBasket();
        $basket->load();
        $basket->onUpdate();
        $articles = $basket->getBasketSummary()->aArticles;
        $this->assertSame(0, count($articles));
    }

    /**
     * Test helper inserts new user
     */
    private function _createUsers()
    {
        $firstUser = array(
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

        $secondUser = $firstUser;
        $secondUser['oxuser__oxusername'] = new oxField('seconduser@oxideshop.dev', oxField::T_RAW);
        $secondUser['oxuser__oxpassword'] = new oxField('c1e113149bcc7737d1f0f91b0510f6cfb60697a5b654e9f49786d59e00e28' .
                                                        '1168c209de99baf94626fa0604794cc4b469a7b768c260cf5c0d1d1ea0c9933effe',
            oxField::T_RAW); //password is qwerqwer
        $secondUser['oxuser__oxpasssalt'] = new oxField('e25b237fce506256b3a151256c410ab2', oxField::T_RAW);
        $secondUser['oxuser__oxcustnr'] = new oxField('667', oxField::T_RAW);
        $secondUser['oxuser__oxfname'] = new oxField('Foo', oxField::T_RAW);
        $secondUser['oxuser__oxlname'] = new oxField('Bla', oxField::T_RAW);

        $this->_insertUser($firstUser);
        $this->_insertUser($secondUser);
    }

    /**
     * Create a user
     *
     * @param $data
     *
     * @return string
     */
    private function _insertUser($data)
    {
        $userOxid = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);

        $user = oxNew('oxUser');
        $user->setId($userOxid);

        foreach ($data as $sKey => $sValue) {
            $user->$sKey = $sValue;
        }
        $user->save();

        return $userOxid;
    }

    /**
     * Test helper, fill basket for given user.
     *
     * @param oxUser $user
     * @param string $articleId
     *
     * @return oxBasket
     */
    private function _getFilledBasketForUser($user, $articleId)
    {
        $basket = oxnew('oxBasket');
        $basket->setBasketUser($user);
        $basket->addToBasket($articleId, 1);
        $basket->calculateBasket(true); //only saved on calculate, oxuserbaskets.oxtitle = 'savedbasket'

        $basket->onUpdate();
        $articles = $basket->getBasketSummary()->aArticles;
        $this->assertSame(1, (int) $articles[$articleId]);

        return $basket;
    }

    /**
     * Test helper, fill basket for logged in User
     *
     * @param string $username
     * @param string $password
     * @param string $articleId
     */
    private function fillBasketForLoggedInUser($username, $password, $articleId)
    {
        $this->setRequestParameter('lgn_usr', $username);
        $this->setRequestParameter('lgn_pwd', $password);

        $parent = $this->getMock('oxUser', array('isEnabledPrivateSales'));
        $parent->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $userComponent = oxNew('oxcmp_user');
        $userComponent->setParent($parent);

        $userComponent->login();

        $user = $userComponent->getUser();
        $basket = $this->_getFilledBasketForUser($user, $articleId);
        oxRegistry::getSession()->setBasket($basket);

        $userComponent->logout();

    }

}
