<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ProductVatTest extends IntegrationTestCase
{
    private string $productId1 = '101';
    private string $productId2 = '102';
    private string $productId3 = '103';
    private array $countriesId = [
        'germany' => 'a7c40f631fc920687.20179984',
        'switzerland' => 'a7c40f6321c6f6109.43859248',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->createProduct($this->productId1, 20);
        $this->createProduct($this->productId2, 30);
        $this->createProduct($this->productId3, 40);

        $this->createActiveUser();
        $this->updateProductVat($this->productId1, 5);
        $this->updateProductVat($this->productId2, 10);
    }

    private function createActiveUser(): void
    {
        $sTestUserId = substr_replace(Registry::getUtilsObject()->generateUId(), '_', 0, 1);

        $user = oxNew(User::class);
        $user->setId($sTestUserId);

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
        $user->oxuser__oxfname = new Field('Erna');
        $user->oxuser__oxlname = new Field('Helvetia');
        $user->oxuser__oxstreet = new Field('Dorfstrasse');
        $user->oxuser__oxstreetnr = new Field('117');
        $user->oxuser__oxcity = new Field('Oberbuchsiten');
        $user->oxuser__oxcountryid = new Field($this->countriesId[strtolower('germany')]);
        $user->oxuser__oxzip = new Field('4625');
        $user->oxuser__oxsal = new Field('MRS');
        $user->oxuser__oxcreate = new Field('2015-05-20 22:10:51');
        $user->oxuser__oxregister = new Field('2015-05-20 22:10:51');
        $user->oxuser__oxboni = new Field('1000');

        $user->save();
    }

    private function createProduct(string $productId, int $price): void
    {
        $product = oxNew(Article::class);
        $product->setAdminMode(false);
        $product->setId($productId);
        $product->oxarticles__oxprice = new Field($price);
        $product->oxarticles__oxshopid = new Field(1);
        $product->oxarticles__oxtitle = new Field('test');
        $product->save();
    }

    private function updateProductVat(string $productId, int $vat): void
    {
        $product = oxNew(Article::class);
        $product->setId($productId);
        $product->oxarticles__oxvat = new Field($vat);
        $product->save();
    }

    public function testProductVat(): void
    {
        $basket = oxNew(Basket::class);
        $basket->addToBasket($this->productId1, 1);
        $basket->addToBasket($this->productId2, 1);
        $basket->addToBasket($this->productId3, 1);

        $basket->calculateBasket(true);
        $this->assertSame(79.93, $basket->getNettoSum());

        $this->assertSame([
            5 => '0,95',
            10 => '2,73',
            19 => '6,39',
        ], $basket->getProductVats(true));

        $this->loginUser();

        $this->changeUserAddress();

        $basket->calculateBasket(true);
        $this->assertSame(79.93, $basket->getNettoSum());

        $this->assertSame([
            0 => '0,00',
        ], $basket->getProductVats(true));
    }

    private function loginUser(): void
    {
        $_POST['lgn_usr'] = 'testuser@oxideshop.dev';
        $_POST['lgn_pwd'] = 'asdfasdf';
        oxNew(UserComponent::class)->login();
    }

    private function changeUserAddress(): void
    {
        $countryInfo = [
            'germany' => [
                'oxuser__oxfname' => 'Erna',
                'oxuser__oxlname' => 'Hahnentritt',
                'oxuser__oxstreetnr' => '117',
                'oxuser__oxstreet' => 'Landstrasse',
                'oxuser__oxzip' => '22769',
                'oxuser__oxcity' => 'Hamburg',
                'oxuser__oxcountryid' => $this->countriesId['germany'],
            ],
            'switzerland' => [
                'oxuser__oxfname' => 'Erna',
                'oxuser__oxlname' => 'Hahnentritt',
                'oxuser__oxstreetnr' => '117',
                'oxuser__oxstreet' => 'Landstrasse',
                'oxuser__oxzip' => '3741',
                'oxuser__oxcity' => 'PULKAU',
                'oxuser__oxcountryid' => $this->countriesId['switzerland'],
            ],
        ];

        $_POST['invadr'] = $countryInfo[strtolower('switzerland')];
        $_POST['stoken'] = Registry::getSession()->getSessionChallengeToken();

        $userComponent = oxNew(UserComponent::class);
        $this->assertSame('payment', $userComponent->changeUser());
    }
}
