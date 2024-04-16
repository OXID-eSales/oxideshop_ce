<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Newsletter\Dao;

use DateTime;
use DateInterval;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao\NewsletterRecipientsDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class NewsletterRecipientsDaoTest extends IntegrationTestCase
{
    private QueryBuilderFactoryInterface $queryBuilderFactory;

    public function setup(): void
    {
        parent::setUp();

        $this->beginTransactionForConnectionFromTestContainer();
        $this->queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);
        $this->prepareTestData();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->rollBackTransactionForConnectionFromTestContainer();
    }

    public function testGetNewsletterRecipients(): void
    {
        $recipientsList = $this->get(NewsletterRecipientsDataMapperInterface::class)->mapRecipientListDataToArray(
            $this->get(NewsletterRecipientsDaoInterface::class)->getNewsletterRecipients(1)
        );

        $this->assertEquals(
            [
                [
                    NewsletterRecipientsDataMapper::SALUTATION,
                    NewsletterRecipientsDataMapper::FIRST_NAME,
                    NewsletterRecipientsDataMapper::LAST_NAME,
                    NewsletterRecipientsDataMapper::EMAIL,
                    NewsletterRecipientsDataMapper::OPT_IN_STATE,
                    NewsletterRecipientsDataMapper::COUNTRY,
                    NewsletterRecipientsDataMapper::ASSIGNED_USER_GROUPS,
                ],
                [
                    "MR",
                    "christine's welt\"",
                    "Doe",
                    "test_user@test.com",
                    "subscribed",
                    "Deutschland",
                    "test_group1,test_group2"
                ],
                [
                    "MS",
                    "christine's welt\"",
                    "Doe",
                    "test_user2@test.com",
                    "subscribed",
                    "Deutschland",
                    ""
                ],
            ],
            $recipientsList
        );
    }

    private function prepareTestData(): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->delete('oxnewssubscribed');
        $queryBuilder->execute();

        $this->createSubscribedUserWithGroups();
        $this->createSubscribedUserWithoutGroups();
        $this->createSubscribedUserInOtherSubshop();
    }

    private function createSubscribedUserWithGroups(): void
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);

        $testUserId = 'test_user';

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxuser')
            ->values([
                'oxid' => ':id',
                'OXUSERNAME' => ':username',
                'OXRIGHTS' => ':userRights',
                'OXCOUNTRYID' => ':countryId',
                'OXCREATE' => ':create'
            ])
            ->setParameters([
                'id' => $testUserId,
                'username' => "test_user@test.com",
                'userRights' => "malladmin",
                'countryId' => "a7c40f631fc920687.20179984",
                'create' => (new DateTime())->format('Y-m-d H:i:s')
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxgroups')
            ->values([
                'oxid' => ':id',
                'OXTITLE' => ':title',
                'OXTITLE_1' => ':title'
            ])
            ->setParameters([
                'id' => 'test_group1',
                'title' => "test_group1"
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxgroups')
            ->values([
                'oxid' => ':id',
                'OXTITLE' => ':title',
                'OXTITLE_1' => ':title'
            ])
            ->setParameters([
                'id' => 'test_group2',
                'title' => "test_group2"
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxobject2group')
            ->values([
                'oxid' => ':id',
                'OXSHOPID' => '1',
                'OXOBJECTID' => ':userId',
                'OXGROUPSID' => ':groupId',
            ])
            ->setParameters([
                'id' => $shopAdapter->generateUniqueId(),
                'userId' => $testUserId,
                'groupId' => 'test_group1'
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxobject2group')
            ->values([
                'oxid' => ':id',
                'OXSHOPID' => '1',
                'OXOBJECTID' => ':userId',
                'OXGROUPSID' => ':groupId',
            ])
            ->setParameters([
                'id' => $shopAdapter->generateUniqueId(),
                'userId' => $testUserId,
                'groupId' => 'test_group2'
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxnewssubscribed')
            ->values([
                'oxid' => ':id',
                'OXUSERID' => ':userId',
                'OXSAL' => ':sal',
                'OXFNAME' => ':fistname',
                'OXLNAME' => ':lastname',
                'OXEMAIL' => ':email',
                'OXDBOPTIN' => ':otpInState',
                'OXSHOPID' => ':shopId',
            ])
            ->setParameters([
                'id' => $shopAdapter->generateUniqueId(),
                'userId' => $testUserId,
                'sal' => "MR",
                'fistname' => "christine&#039;s welt&quot;",
                'lastname' => "Doe",
                'email' => "test_user@test.com",
                'otpInState' => "1",
                'shopId' => "1"
            ]);

        $queryBuilder->execute();
    }

    private function createSubscribedUserWithoutGroups(): void
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);

        $testUserId = 'test_user2';

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxuser')
            ->values([
                'oxid' => ':id',
                'OXUSERNAME' => ':username',
                'OXRIGHTS' => ':userRights',
                'OXCOUNTRYID' => ':countryId',
                'OXCREATE' => ':create'
            ])
            ->setParameters([
                'id' => $testUserId,
                'username' => "test_user2@test.com",
                'userRights' => "malladmin",
                'countryId' => "a7c40f631fc920687.20179984",
                'create' => (new DateTime())->add(new DateInterval('P1D'))->format('Y-m-d H:i:s')
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxnewssubscribed')
            ->values([
                'oxid' => ':id',
                'OXUSERID' => ':userId',
                'OXSAL' => ':sal',
                'OXFNAME' => ':fistname',
                'OXLNAME' => ':lastname',
                'OXEMAIL' => ':email',
                'OXDBOPTIN' => ':otpInState',
                'OXSHOPID' => ':shopId',
            ])
            ->setParameters([
                'id' => $shopAdapter->generateUniqueId(),
                'userId' => $testUserId,
                'sal' => "MS",
                'fistname' => "christine&#039;s welt&quot;",
                'lastname' => "Doe",
                'email' => "test_user2@test.com",
                'otpInState' => "1",
                'shopId' => "1"
            ]);

        $queryBuilder->execute();
    }

    private function createSubscribedUserInOtherSubshop(): void
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);

        $testUserId = 'test_user3';

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxuser')
            ->values([
                'oxid' => ':id',
                'OXUSERNAME' => ':username',
                'OXRIGHTS' => ':userRights',
                'OXCOUNTRYID' => ':countryId',
            ])
            ->setParameters([
                'id' => $testUserId,
                'username' => "test_user3@test.com",
                'userRights' => "malladmin",
                'countryId' => "a7c40f631fc920687.20179984",
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxnewssubscribed')
            ->values([
                'oxid' => ':id',
                'OXUSERID' => ':userId',
                'OXSAL' => ':sal',
                'OXFNAME' => ':fistname',
                'OXLNAME' => ':lastname',
                'OXEMAIL' => ':email',
                'OXDBOPTIN' => ':otpInState',
                'OXSHOPID' => ':shopId',
            ])
            ->setParameters([
                'id' => $shopAdapter->generateUniqueId(),
                'userId' => $testUserId,
                'sal' => "MR",
                'fistname' => "christine&#039;s welt&quot;",
                'lastname' => "Doe",
                'email' => "test_user3@test.com",
                'otpInState' => "1",
                'shopId' => "99"
            ]);

        $queryBuilder->execute();
    }
}
