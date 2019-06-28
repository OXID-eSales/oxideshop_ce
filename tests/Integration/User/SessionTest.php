<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\User;

use oxBasket;
use oxField;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxRegistry;
use oxUser;
use \OxidEsales\Eshop\Application\Model\Basket;
use \OxidEsales\Eshop\Application\Model\User;
use \OxidEsales\Eshop\Application\Model\BasketItem;
use \OxidEsales\Eshop\Core\Price;
use \OxidEsales\Eshop\Core\PriceList;

use \OxidEsales\Eshop\Core\Registry;

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

    public function testReturnsEmptyBasketIfNoBasketHasBeenSerializedInSession()
    {
        $this->assertSessionBasketIsEmpty();
    }

    public function testReturnsSerializedCalculatedBasketFromSessionOfAnonymousUser()
    {
        $this->storeSerializedBasketToSession(
            $this->getSerializedBasketWithArticle(self::FIRST_ARTICLE_ID)
        );

        $this->assertArticleInSessionBasket(self::FIRST_ARTICLE_ID);
    }

    public function testReturnsSerializedNonCalculatedBasketFromSessionOfAnonymousUser()
    {
        $this->storeSerializedBasketToSession(
            $this->getSerializedBasketWithArticle(self::FIRST_ARTICLE_ID, false)
        );

        $this->assertArticleInSessionBasket(self::FIRST_ARTICLE_ID);
    }

    /**
     * @dataProvider serializedBasketClassProvider
     */
    public function testReturnsEmptyBasketIfAnonymousUserHasSerializedBasketWithDeactivatedModuleClass($className)
    {
        $this->storeSerializedBasketToSession(
            $this->changeSerializedClass(
                $this->getSerializedBasketWithArticle(self::FIRST_ARTICLE_ID),
                get_class(oxNew($className)),
                "DummyDeactivatedModuleClassName"
            )
        );

        $this->assertSessionBasketIsEmpty();
    }

    /**
     * @dataProvider serializedBasketClassProvider
     */
    public function testReturnsEmptyBasketIfAnonymousUserHasSerializedBasketWithDeactivatedModuleAndNamespacedClass($className)
    {
        $deactivatedModuleClassFqn = "OxidEsales\\DummySpace\\DeactivatedModuleClassName";
        $this->registerFakeNamespacedDeactivatedModuleClass($deactivatedModuleClassFqn);

        $this->storeSerializedBasketToSession(
            $this->changeSerializedClass(
                $this->getSerializedBasketWithArticle(self::FIRST_ARTICLE_ID),
                get_class(oxNew($className)),
                $deactivatedModuleClassFqn
            )
        );

        $this->assertSessionBasketIsEmpty();
    }

    public function testReturnsEmptyBasketIfRegisteredUserHasSerializedBasketWithDeactivatedModuleUserClass()
    {
        $this->storeSerializedBasketToSession(
            $this->changeSerializedClass(
                $this->getSerializedBasketWithArticle(self::FIRST_ARTICLE_ID, true, true),
                get_class(oxNew(User::class)),
                "DummyDeactivatedModuleClassName"
            )
        );

        $this->assertSessionBasketIsEmpty();
    }

    public function testReturnsEmptyBasketIfRegisteredUserHasSerializedBasketWithDeactivatedModuleAndNamespacedUserClass()
    {
        $deactivatedModuleClassFqn = "OxidEsales\\DummySpace\\DeactivatedModuleClassName";
        $this->registerFakeNamespacedDeactivatedModuleClass($deactivatedModuleClassFqn);

        $this->storeSerializedBasketToSession(
            $this->changeSerializedClass(
                $this->getSerializedBasketWithArticle(self::FIRST_ARTICLE_ID, true, true),
                get_class(oxNew(User::class)),
                $deactivatedModuleClassFqn
            )
        );

        $this->assertSessionBasketIsEmpty();
    }

    /**
     * Describe all classes which could be serialized within Basket object.
     *
     * @return array
     */
    public function serializedBasketClassProvider()
    {
        return [
            [Basket::class],
            [BasketItem::class],
            [Price::class],
            [PriceList::class]
        ];
    }

    /**
     * @param string $serializedBasket
     */
    private function storeSerializedBasketToSession($serializedBasket)
    {
        Registry::getSession()->delBasket();
        $shopId = $this->getShopId();
        $this->setSessionParam("{$shopId}_basket", $serializedBasket);
    }

    /**
     * @param string $serializedBasket
     * @param string $oldClassName
     * @param string $newClassName
     *
     * @return string
     */
    private function changeSerializedClass($serializedBasket, $oldClassName, $newClassName)
    {
        $replaceFrom = sprintf('O:%d:"%s"', strlen($oldClassName), $oldClassName);
        $replaceTo = sprintf('O:%d:"%s"', strlen($newClassName), $newClassName);

        return str_replace($replaceFrom, $replaceTo, $serializedBasket);
    }

    /**
     * @param string $fqn Fully Qualified Name of namespaced class
     */
    private function registerFakeNamespacedDeactivatedModuleClass($fqn)
    {
        $fqnElements = explode("\\", $fqn);
        $className = array_pop($fqnElements);
        $namespace = implode("\\", $fqnElements);

        spl_autoload_register(function ($classToAutoload) use ($namespace, $className, $fqn) {
            if ($classToAutoload === $fqn) {
                eval("namespace $namespace;\n\nclass $className extends {$className}_parent {};");
            }
        });
    }

    private function assertSessionBasketIsEmpty()
    {
        $articles = Registry::getSession()->getBasket()->getBasketSummary()->aArticles;
        $count = count($articles);
        $this->assertSame(0, $count, "Failed asserting that basket is empty. Given count of items: $count.");
    }

    /**
     * @param string $articleId
     */
    private function assertArticleInSessionBasket($articleId)
    {
        $articles = Registry::getSession()->getBasket()->getBasketSummary()->aArticles;
        $this->assertGreaterThanOrEqual(
            1,
            (int)$articles[$articleId],
            "Failed asserting that there is at least one article '$articleId' in basket."
        );
    }

    /**
     * @param string $articleId
     * @param bool   $enableBasketCalculation
     * @param bool   $addUser
     *
     * @return string
     */
    private function getSerializedBasketWithArticle($articleId, $enableBasketCalculation = true, $addUser = false)
    {
        $basket = oxNew(Basket::class);
        $basket->addToBasket($articleId, 1);

        if ($addUser) {
            $user = oxNew(User::class);
            $user->load('firstuser@oxideshop.dev');
            $basket->setBasketUser($user);
        }

        $enableBasketCalculation && $basket->calculateBasket(true);
        return serialize($basket);
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
            'oxuser__oxpassword'  => new oxField(
                'c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                 'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
                oxField::T_RAW
            ), //password is asdfasdf
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
        $secondUser['oxuser__oxpassword'] = new oxField(
            'c1e113149bcc7737d1f0f91b0510f6cfb60697a5b654e9f49786d59e00e28' .
                                                        '1168c209de99baf94626fa0604794cc4b469a7b768c260cf5c0d1d1ea0c9933effe',
            oxField::T_RAW
        ); //password is qwerqwer
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

        $parent = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('isEnabledPrivateSales'));
        $parent->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $userComponent = oxNew('oxcmp_user');
        $userComponent->setParent($parent);

        $userComponent->login();

        $user = $userComponent->getUser();
        $basket = $this->_getFilledBasketForUser($user, $articleId);
        oxRegistry::getSession()->setBasket($basket);

        $this->setRequestParameter('stoken', Registry::getSession()->getSessionChallengeToken());
        $userComponent->logout();
    }
}
