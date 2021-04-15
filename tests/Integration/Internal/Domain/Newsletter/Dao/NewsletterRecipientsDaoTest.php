<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Newsletter\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao\NewsletterRecipientsDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorer;
use PHPUnit\Framework\TestCase;

class NewsletterRecipientsDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var object|QueryBuilderFactoryInterface|null
     */
    private $queryBuilderFactory;

    /**
     * @var DatabaseRestorer
     */
    private $databaseRestorer;

    protected function setup(): void
    {
        $this->databaseRestorer = new DatabaseRestorer();
        $this->databaseRestorer->dumpDB(__CLASS__);
        $this->queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);
        $this->prepareTestData();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->databaseRestorer->restoreDB(__CLASS__);

        parent::tearDown();
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
                    "John",
                    "Doe",
                    "test_user@test.com",
                    "subscribed",
                    "Deutschland",
                    "test_group1"
                ]
            ],
            $recipientsList
        );
    }

    private function prepareTestData(): void
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);

        $testUserId = 'test_user';

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxuser')
            ->values([
                'oxid'          => ':id',
                'OXUSERNAME'      => ':username',
                'OXRIGHTS'      => ':userRights',
                'OXCOUNTRYID'      => ':countryId',
            ])
            ->setParameters([
                'id'    => $testUserId,
                'username' => "test_user@test.com",
                'userRights' => "malladmin",
                'countryId' => "a7c40f631fc920687.20179984",
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxgroups')
            ->values([
                'oxid'          => ':id',
                'OXTITLE'      => ':title',
                'OXTITLE_1'      => ':title'
            ])
            ->setParameters([
                'id'    => 'test_group1',
                'title' => "test_group1"
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxobject2group')
            ->values([
                'oxid'          => ':id',
                'OXSHOPID'      => '1',
                'OXOBJECTID'      => ':userId',
                'OXGROUPSID'      => ':groupId',
            ])
            ->setParameters([
                'id'      => $shopAdapter->generateUniqueId(),
                'userId'  => $testUserId,
                'groupId' => 'test_group1'
            ]);

        $queryBuilder->execute();

        $queryBuilder
            ->delete('oxnewssubscribed');

        $queryBuilder->execute();

        $queryBuilder
            ->insert('oxnewssubscribed')
            ->values([
                'oxid'          => ':id',
                'OXUSERID'      => ':userId',
                'OXSAL'      => ':sal',
                'OXFNAME'      => ':fistname',
                'OXLNAME'      => ':lastname',
                'OXEMAIL'      => ':email',
                'OXDBOPTIN'      => ':otpInState',
                'OXSHOPID'      => ':shopId',
            ])
            ->setParameters([
                'id'    => $shopAdapter->generateUniqueId(),
                'userId' => $testUserId,
                'sal' => "MR",
                'fistname' => "John",
                'lastname' => "Doe",
                'email' => "test_user@test.com",
                'otpInState' => "1",
                'shopId' => "1"
            ]);

        $queryBuilder->execute();
    }
}
