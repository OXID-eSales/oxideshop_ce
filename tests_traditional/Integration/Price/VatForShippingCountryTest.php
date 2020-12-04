<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Price;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\TestingLibrary\UnitTestCase;

class VatForShippingCountryTest extends UnitTestCase
{
    private const USER_ID = '_testVatUserId';
    private const ADDRESS_ID = '_testVatAddressId';

    private const FIRST_ARTICLE_ID = '101';
    private const SECOND_ARTICLE_ID = '102';
    private const THIRD_ARTICLE_ID = '103';

    /** @var array */
    private $addressInfo = [];

    protected function setUp(): void
    {
        $this->initiateAddressInfo();

        $this->createArticle(self::FIRST_ARTICLE_ID, 20);
        $this->createArticle(self::SECOND_ARTICLE_ID, 30);
        $this->createArticle(self::THIRD_ARTICLE_ID, 40);

        $this->createActiveUser('Germany');
        $this->updateArticleVat(self::FIRST_ARTICLE_ID, 5);
        $this->updateArticleVat(self::SECOND_ARTICLE_ID, 10);

        parent::setUp();
    }

    private function initiateAddressInfo(): void
    {
        $germany = [
            'oxfname'     => 'Erna',
            'oxlname'     => 'Hahnentritt',
            'oxstreetnr'  => '117',
            'oxstreet'    => 'Landstrasse',
            'oxzip'       => '22769',
            'oxcity'      => 'Hamburg',
            'oxcountryid' => 'a7c40f631fc920687.20179984',
            'oxcompany' => 'myCompany',
            'oxfon' => '217-8918713',
            'oxfax' => '217-8918713',
            'oxsal' => 'MRS'
        ];

        $switzerland = [
            'oxfname'     => 'Erna',
            'oxlname'     => 'Hahnentritt',
            'oxstreetnr'  => '117',
            'oxstreet'    => 'Landstrasse',
            'oxzip'       => '3741',
            'oxcity'      => 'PULKAU',
            'oxcountryid' => 'a7c40f6321c6f6109.43859248',
            'oxcompany' => 'myCompany',
            'oxfon' => '217-8918713',
            'oxfax' => '217-8918713',
            'oxsal' => 'MRS'
        ];

        $this->addressInfo = ['Germany' => $germany, 'Switzerland' => $switzerland];
    }

    public function testProductVat(): void
    {
        $config = Registry::getConfig();
        $config->setConfigParam('blShippingCountryVat', true);

        $basket = oxNew(Basket::class);
        $basket->addToBasket(self::FIRST_ARTICLE_ID, 1);
        $basket->addToBasket(self::SECOND_ARTICLE_ID, 1);
        $basket->addToBasket(self::THIRD_ARTICLE_ID, 1);
        $basket->calculateBasket(true);

        $this->loginUser();

        // Billing Germany - shipping Germany
        $this->setBillingAddress('Germany');
        $this->setShippingAddress('Germany');
        $this->assertSame([5  => '0,95', 10 => '2,73', 19 => '6,39',], $basket->getProductVats(true));

        // Billing Germany - shipping Switzerland
        $this->setBillingAddress('Germany');
        $this->setShippingAddress('Switzerland');
        $basket->calculateBasket(true);
        $this->assertSame([0  => '0,00',], $basket->getProductVats(true));

        // Billing Switzerland - shipping Germany
        $this->setBillingAddress('Switzerland');
        $this->setShippingAddress('Germany');
        $basket->calculateBasket(true);
        $this->assertSame([5  => '0,95', 10 => '2,73', 19 => '6,39',], $basket->getProductVats(true));
    }

    /**
     * @param string $country
     */
    private function setShippingAddress(string $country): void
    {
        $addressId = self::ADDRESS_ID . $country;
        $addressInfo = $this->addressInfo[$country];

        $address = oxNew('oxbase');
        $address->init('oxaddress');
        $address->setId($addressId);

        $address->oxaddress__oxuserid = new Field(self::USER_ID);
        $address->oxaddress__oxaddressuserid = new Field(self::USER_ID);
        $address->oxaddress__oxfname = new Field($addressInfo['oxfname']);
        $address->oxaddress__oxlname = new Field($addressInfo['oxlname']);
        $address->oxaddress__oxstreetnr = new Field($addressInfo['oxstreetnr']);
        $address->oxaddress__oxstreet = new Field($addressInfo['oxstreet']);
        $address->oxaddress__oxzip = new Field($addressInfo['oxzip']);
        $address->oxaddress__oxcity = new Field($addressInfo['oxcity']);
        $address->oxaddress__oxcountryid = new Field($addressInfo['oxcountryid']);
        $address->oxaddress__oxcompany = new Field($addressInfo['oxcompany']);
        $address->oxaddress__oxaddinfo = new Field(null);
        $address->oxaddress__oxstateid = new Field(null);
        $address->oxaddress__oxfon = new Field($addressInfo['oxfon']);
        $address->oxaddress__oxfax = new Field($addressInfo['oxfax']);
        $address->oxaddress__oxsal = new Field($addressInfo['oxsal']);

        $address->save();
        $this->getSession()->setVariable('deladrid', $addressId);
    }

    /**
     * @param string $country
     *
     * @return User
     */
    private function createActiveUser(string $country): User
    {
        $addressInfo = $this->addressInfo[$country];

        $user = oxNew(User::class);
        $user->setId(self::USER_ID);

        $user->oxuser__oxactive = new Field('1');
        $user->oxuser__oxrights = new Field('user');
        $user->oxuser__oxshopid = new Field(ShopIdCalculator::BASE_SHOP_ID);
        $user->oxuser__oxusername = new Field('testuser@oxideshop.dev');
        $user->oxuser__oxpassword = new Field(
            'c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
            'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d'
        ); //password is asdfasdf
        $user->oxuser__oxpasssalt = new Field('3ddda7c412dbd57325210968cd31ba86');
        $user->oxuser__oxcustnr = new Field('667');
        $user->oxuser__oxcreate = new Field('2015-05-20 22:10:51');
        $user->oxuser__oxregister = new Field('2015-05-20 22:10:51');
        $user->oxuser__oxboni = new Field('1000');

        $user->oxuser__oxfname = new Field($addressInfo['oxfname']);
        $user->oxuser__oxlname = new Field($addressInfo['oxlname']);
        $user->oxuser__oxstreet = new Field($addressInfo['oxstreet']);
        $user->oxuser__oxstreetnr = new Field($addressInfo['oxstreetnr']);
        $user->oxuser__oxcity = new Field($addressInfo['oxcity']);
        $user->oxuser__oxcountryid = new Field($addressInfo['oxcountryid']);
        $user->oxuser__oxzip = new Field($addressInfo['oxzip']);
        $user->oxuser__oxsal = new Field($addressInfo['oxsal']);

        $user->save();

        return $user;
    }

    /**
     * @param string $country
     */
    private function setBillingAddress(string $country): void
    {
        $addressInfo = $this->addressInfo[$country];

        $_POST['invadr'] = [
            'oxuser__oxfname'     => $addressInfo['oxfname'],
            'oxuser__oxlname'     => $addressInfo['oxlname'],
            'oxuser__oxstreetnr'  => $addressInfo['oxstreetnr'],
            'oxuser__oxstreet'    => $addressInfo['oxstreet'],
            'oxuser__oxzip'       => $addressInfo['oxzip'],
            'oxuser__oxcity'      => $addressInfo['oxcity'],
            'oxuser__oxcountryid' => $addressInfo['oxcountryid']
        ];

        $_POST['stoken'] = Registry::getSession()->getSessionChallengeToken();

        $userComponent = oxNew('oxcmp_user');
        $this->assertSame('payment', $userComponent->changeUser());
    }

    /**
     *
     * @return string
     */
    private function loginUser(): string
    {
        $_POST['lgn_usr'] = 'testuser@oxideshop.dev';
        $_POST['lgn_pwd'] = 'asdfasdf';
        $oCmpUser = oxNew('oxcmp_user');
        return $oCmpUser->login();
    }

    /**
     * @param string $articleId
     * @param int    $vat
     */
    private function updateArticleVat(string $articleId, int $vat): void
    {
        $article = oxNew(Article::class);
        $article->setId($articleId);
        $article->oxarticles__oxvat = new Field($vat);
        $article->save();
    }

    /**
     * @param string $articleId
     * @param int    $price
     */
    private function createArticle(string $articleId, int $price): void
    {
        $oArticle = oxNew(Article::class);
        $oArticle->setAdminMode(null);
        $oArticle->setId($articleId);
        $oArticle->oxarticles__oxprice = new Field($price);
        $oArticle->oxarticles__oxshopid = new Field(1);
        $oArticle->oxarticles__oxtitle = new Field("test");
        $oArticle->save();
    }
}
