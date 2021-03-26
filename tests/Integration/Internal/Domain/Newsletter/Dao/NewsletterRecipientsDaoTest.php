<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Newsletter\Dao;

use Doctrine\DBAL\Exception;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao\NewsletterRecipientsDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorer;
use PHPUnit\Framework\TestCase;

class NewsletterRecipientsDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var DatabaseRestorer
     */
    private $databaseRestorer;

    /**
     * @var object|QueryBuilderFactoryInterface|null
     */
    private $queryBuilderFactory;

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
        $recipientsList = $this->mapRecipientListDataToArray(
            $this->get(NewsletterRecipientsDaoInterface::class)->get(1)
        );

        $this->assertContains(
            [
                NewsletterRecipient::SALUTATION           => "MR",
                NewsletterRecipient::FIRST_NAME           => "John",
                NewsletterRecipient::LAST_NAME            => "Doe",
                NewsletterRecipient::EMAIL                => "test_user@test.com",
                NewsletterRecipient::OPT_IN_STATE         => "subscribed",
                NewsletterRecipient::COUNTRY              => "Deutschland",
                NewsletterRecipient::ASSIGNED_USER_GROUPS => "test_group1,test_group2"
            ],
            $recipientsList
        );

    }

    /**
     * @param NewsletterRecipient[] $newsletterRecipient
     *
     * @return array
     */
    private function mapRecipientListDataToArray(array $newsletterRecipient): array
    {
        $result = [];

        foreach ($newsletterRecipient as $index=>$value) {
            $result[$index][$value::SALUTATION] = $value->getSalutation();
            $result[$index][$value::FIRST_NAME] = $value->getFistName();
            $result[$index][$value::LAST_NAME] = $value->getLastName();
            $result[$index][$value::EMAIL] = $value->getEmail();
            $result[$index][$value::OPT_IN_STATE] = $value->getOtpInState();
            $result[$index][$value::COUNTRY] = $value->getCountry();
            $result[$index][$value::ASSIGNED_USER_GROUPS] = $value->getUserGroups();
        }

        return $result;
    }

    private function prepareTestData(): void
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);

        // add test User

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

        // add Test groups

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
            ->insert('oxgroups')
            ->values([
                'oxid'          => ':id',
                'OXTITLE'      => ':title',
                'OXTITLE_1'      => ':title'
            ])
            ->setParameters([
                'id'    => 'test_group2',
                'title' => "test_group2"
            ]);

        $queryBuilder->execute();

        // attach test groups to test user


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
                'groupId' => 'test_group2'
            ]);

        $queryBuilder->execute();


        // add oxnewssubscribed data


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
