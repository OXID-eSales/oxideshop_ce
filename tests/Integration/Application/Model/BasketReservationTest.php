<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\BasketReservation;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\UserBasket;
use OxidEsales\Eshop\Application\Model\UserBasketItem;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class BasketReservationTest extends IntegrationTestCase
{
    private int $basketReservationTimeout;
    private DatabaseInterface $database;
    private BasketReservation $basketReservation;
    private string $savedBasket = 'savedbasket';
    private string $reservations = 'reservations';
    private string $noticeList = 'noticelist';
    private int $oldTimestamp;

    public function setUp(): void
    {
        parent::setUp();

        $this->basketReservationTimeout = (int) Registry::getConfig()->getConfigParam('iPsBasketReservationTimeout');
        Registry::getConfig()->setConfigParam('iPsBasketReservationTimeout', 1);
        $this->database = DatabaseProvider::getDb();
        $this->basketReservation = new BasketReservation();
        $this->oldTimestamp = time() - 3600;
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('iPsBasketReservationTimeout', $this->basketReservationTimeout);
        parent::tearDown();
    }

    public function testReservationTimeoutRemovesExpiredBaskets(): void
    {
        $shopId = Registry::getConfig()->getShopId();
        $user1Id = 'testUser';
        $user2Id = 'testUser2';
        $productId = 'testProduct';
        $noticeListId = 'testNoticeList';
        $savedBasketUser2 = 'testSavedBasketUser2';
        $reservationsUser2 = 'testReservationsUser2';

        $this->setupTestUsers([$user1Id, $user2Id], $shopId);
        $this->createProduct($productId, 'Test Product 1', $shopId);
        $this->setupOneShopTestData(
            $shopId,
            $user1Id,
            $user2Id,
            $productId,
            $noticeListId,
            $savedBasketUser2,
            $reservationsUser2
        );

        $this->basketReservation->discardUnusedReservations(10);

        $remainingBaskets = $this->database->select('SELECT oxid FROM oxuserbaskets order by oxid');

        $this->assertSame(3, $remainingBaskets->count());
        $this->assertSame([[$noticeListId], [$reservationsUser2], [$savedBasketUser2]], $remainingBaskets->fetchAll());

        $remainingBasketItems = $this->database->select(
            'SELECT oxbasketid FROM oxuserbasketitems WHERE OXARTID = :productid order by oxbasketid',
            ['productid' => $productId]
        );

        $this->assertSame(3, $remainingBasketItems->count());
        $this->assertSame(
            [
                [$noticeListId],
                [$reservationsUser2],
                [$savedBasketUser2]
            ],
            $remainingBasketItems->fetchAll()
        );
    }

    public function testDiscardExpiredReservationsPreservesOtherShopsBaskets(): void
    {
        $shop1Id = Registry::getConfig()->getShopId();
        $shop2Id = 2;
        $user1IdShop1 = 'testUser1Shop1';
        $user2IdShop1 = 'testUser2Shop1';
        $user1IdShop2 = 'testUser1Shop2';
        $product1Id = 'testProductShop1';
        $product2Id = 'testProductShop2';

        $this->setupMultiShopTestData(
            $shop1Id,
            $shop2Id,
            $user1IdShop1,
            $user2IdShop1,
            $user1IdShop2,
            $product1Id,
            $product2Id
        );

        $this->basketReservation->discardUnusedReservations(10);

        $baskets = $this->database->select('SELECT oxuserid, oxtitle FROM oxuserbaskets order by oxuserid');
        $this->assertSame(1, $baskets->count());
        $this->assertSame([[$user1IdShop2, $this->savedBasket]], $baskets->fetchAll());

        $basketItemsCount = $this->database->select('SELECT * FROM oxuserbasketitems')->count();

        $this->assertSame(1, $basketItemsCount);
    }

    private function setupTestUsers(array $userIds, int $shopId): void
    {
        foreach ($userIds as $userId) {
            $this->createUser($userId, $shopId);
        }
    }

    private function setupOneShopTestData(
        int $shopId,
        string $user1Id,
        string $user2Id,
        string $productId,
        string $noticeListId,
        string $savedBasketUser2,
        string $reservationsUser2
    ): void {
        $this->createBasket($noticeListId, $shopId, $user1Id, $this->noticeList, $this->oldTimestamp, $productId);
        $this->createBasket(uniqid(), $shopId, $user1Id, $this->savedBasket, $this->oldTimestamp, $productId);
        $this->createBasket(uniqid(), $shopId, $user1Id, $this->reservations, $this->oldTimestamp, $productId);
        $this->createBasket($savedBasketUser2, $shopId, $user2Id, $this->savedBasket, time(), $productId);
        $this->createBasket($reservationsUser2, $shopId, $user2Id, $this->reservations, time(), $productId);
    }

    private function setupMultiShopTestData(
        int $shop1Id,
        int $shop2Id,
        string $user1IdShop1,
        string $user2IdShop1,
        string $user1IdShop2,
        string $product1Id,
        string $product2Id
    ): void {
        $this->createUser($user1IdShop1, $shop1Id);
        $this->createUser($user2IdShop1, $shop1Id);
        $this->createUser($user1IdShop2, $shop2Id);

        $this->createProduct($product1Id, 'Test Product 1', $shop1Id);
        $this->createProduct($product2Id, 'Test Product 2', $shop2Id);

        $this->createBasket(uniqid(), $shop1Id, $user1IdShop1, $this->savedBasket, $this->oldTimestamp, $product1Id);
        $this->createBasket(uniqid(), $shop1Id, $user2IdShop1, $this->reservations, $this->oldTimestamp, $product1Id);
        $this->createBasket(uniqid(), $shop1Id, $user2IdShop1, $this->savedBasket, $this->oldTimestamp, $product1Id);
        $this->createBasket(uniqid(), $shop1Id, $user2IdShop1, $this->reservations, $this->oldTimestamp, $product1Id);
        $this->createBasket(uniqid(), $shop2Id, $user1IdShop2, $this->savedBasket, $this->oldTimestamp, $product2Id);
        $this->createBasket(uniqid(), $shop2Id, $user1IdShop2, $this->reservations, $this->oldTimestamp, $product2Id);
    }

    private function createUser(string $userId, int $shopId): void
    {
        $user = new User();
        $user->setId($userId);
        $user->oxuser__oxshopid = new Field($shopId);
        $user->oxuser__oxusername = new Field(sprintf('%s@test.com', $userId));
        $user->oxuser__oxpassword = new Field(null);
        $user->oxuser__oxregister = new Field(0);

        $user->save();
    }

    private function createProduct(string $articleId, string $articleTitle, int $shopId): void
    {
        $article = new Article();
        $article->setId($articleId);
        $article->oxarticles__oxshopid = new Field($shopId);
        $article->oxarticles__oxtitle = new Field($articleTitle);
        $article->oxarticles__oxstock = new Field(10);
        $article->save();
    }

    private function createBasket(
        string $basketId,
        int $shopId,
        string $userId,
        string $title,
        int $updated,
        string $productId
    ): void {
        $this->createUserBasket($basketId, $shopId, $userId, $title, $updated);
        $this->createBasketItem(uniqid(), $basketId, $productId);
        $this->updateBasketModificationTime($basketId, $updated);
    }

    private function createUserBasket(
        string $basketId,
        int $shopId,
        string $userId,
        string $basketTitle,
        int $updated
    ): void {
        $basket = new UserBasket();
        $basket->setId($basketId);
        $basket->oxuserbaskets__oxshopid = new Field($shopId);
        $basket->oxuserbaskets__oxuserid = new Field($userId);
        $basket->oxuserbaskets__oxtitle = new Field($basketTitle);
        $basket->oxuserbaskets__oxtimestamp = new Field(date('Y-m-d H:i:s'));
        $basket->oxuserbaskets__oxpublic = new Field(0);
        $basket->oxuserbaskets__oxupdate = new Field($updated);

        $basket->save();
    }

    private function createBasketItem(
        string $basketItemId,
        string $basketId,
        string $productId,
    ): void {
        $basketItem = new UserBasketItem();
        $basketItem->setId($basketItemId);
        $basketItem->oxuserbasketitems__oxbasketid = new Field($basketId);
        $basketItem->oxuserbasketitems__oxamount = new Field(1);
        $basketItem->oxuserbasketitems__oxartid = new Field($productId);
        $basketItem->oxuserbasketitems__oxsellist = new Field('N;');
        $basketItem->oxuserbasketitems__oxpersparam = new Field('');

        $basketItem->save();
    }

    private function updateBasketModificationTime(string $basketId, int $updated): void
    {
        $basket = new UserBasket();
        $basket->load($basketId);
        $basket->oxuserbaskets__oxupdate = new Field($updated);

        $basket->save();
    }
}
