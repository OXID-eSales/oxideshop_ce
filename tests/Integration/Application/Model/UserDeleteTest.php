<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserDeleteTest extends IntegrationTestCase
{
    private const USER_ID = 'test_user_delete_id';
    private const ARTICLE_ID = 'test_article_id';

    public function setUp(): void
    {
        parent::setUp();
        $this->createUser(self::USER_ID);
    }

    public function testDeleteByIdWithoutLoadCascadesToAllRelatedTables(): void
    {
        $this->createAddress(self::USER_ID);
        $this->createGroupMembership(self::USER_ID);
        $this->createUserBasket(self::USER_ID);
        $this->createNewsletterSubscription(self::USER_ID);
        $this->createDeliveryAssignment(self::USER_ID);
        $this->createDiscountAssignment(self::USER_ID);
        $this->createRecommendationList(self::USER_ID);
        $this->createArticle(self::ARTICLE_ID);
        $this->createReview(self::USER_ID, self::ARTICLE_ID);
        $this->createRating(self::USER_ID, self::ARTICLE_ID);
        $this->createPriceAlarm(self::USER_ID);
        $this->createAcceptedTerms(self::USER_ID);
        $this->createRemark(self::USER_ID, 'r', 'test_remark_id');
        $this->createRemark(self::USER_ID, 'o', 'test_order_remark_id');

        $user = oxNew(User::class);
        $user->delete(self::USER_ID);

        $this->assertFalse(
            $this->fetchByUserIdColumn('oxaddress', 'oxuserid', self::USER_ID),
            'oxaddress rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxobject2group', 'oxobjectid', self::USER_ID),
            'oxobject2group rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxuserbaskets', 'oxuserid', self::USER_ID),
            'oxuserbaskets rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxnewssubscribed', 'oxuserid', self::USER_ID),
            'oxnewssubscribed rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxobject2delivery', 'oxobjectid', self::USER_ID),
            'oxobject2delivery rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxobject2discount', 'oxobjectid', self::USER_ID),
            'oxobject2discount rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxrecommlists', 'oxuserid', self::USER_ID),
            'oxrecommlists rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxreviews', 'oxuserid', self::USER_ID),
            'oxreviews rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxratings', 'oxuserid', self::USER_ID),
            'oxratings rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxpricealarm', 'oxuserid', self::USER_ID),
            'oxpricealarm rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchByUserIdColumn('oxacceptedterms', 'oxuserid', self::USER_ID),
            'oxacceptedterms rows must be removed when user is deleted by ID'
        );
        $this->assertFalse(
            $this->fetchRemarkByParentAndType(self::USER_ID, 'r'),
            'Non-order remarks must be removed when user is deleted by ID'
        );
        $this->assertNotFalse(
            $this->fetchRemarkById('test_order_remark_id'),
            'Order-related remarks (type=o) must be preserved when user is deleted'
        );
    }

    private function createUser(string $userId): void
    {
        $user = oxNew(User::class);
        $user->setId($userId);
        $user->oxuser__oxusername = new Field('test_delete@example.com');
        $user->oxuser__oxshopid = new Field(1);
        $user->save();
    }

    private function createAddress(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxaddress')
            ->values([
                'oxid' => ':oxid',
                'oxuserid' => ':oxuserid',
                'oxaddressuserid' => ':oxaddressuserid',
            ])
            ->setParameters([
                'oxid' => 'test_address_id',
                'oxuserid' => $userId,
                'oxaddressuserid' => $userId,
            ]);
        $qb->executeStatement();
    }

    private function createGroupMembership(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxobject2group')
            ->values([
                'oxid' => ':oxid',
                'oxshopid' => ':oxshopid',
                'oxobjectid' => ':oxobjectid',
                'oxgroupsid' => ':oxgroupsid',
            ])
            ->setParameters([
                'oxid' => 'test_o2g_id',
                'oxshopid' => 1,
                'oxobjectid' => $userId,
                'oxgroupsid' => 'oxidadmin',
            ]);
        $qb->executeStatement();
    }

    private function createUserBasket(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxuserbaskets')
            ->values([
                'oxid' => ':oxid',
                'oxuserid' => ':oxuserid',
                'oxtitle' => ':oxtitle',
            ])
            ->setParameters([
                'oxid' => 'test_basket_id',
                'oxuserid' => $userId,
                'oxtitle' => 'test basket',
            ]);
        $qb->executeStatement();
    }

    private function createNewsletterSubscription(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxnewssubscribed')
            ->values([
                'oxid' => ':oxid',
                'oxuserid' => ':oxuserid',
                'oxemail' => ':oxemail',
            ])
            ->setParameters([
                'oxid' => 'test_newsletter_id',
                'oxuserid' => $userId,
                'oxemail' => 'test@example.com',
            ]);
        $qb->executeStatement();
    }

    private function createDeliveryAssignment(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxobject2delivery')
            ->values([
                'oxid' => ':oxid',
                'oxdeliveryid' => ':oxdeliveryid',
                'oxobjectid' => ':oxobjectid',
                'oxtype' => ':oxtype',
            ])
            ->setParameters([
                'oxid' => 'test_delivery_id',
                'oxdeliveryid' => 'test_delivery_rule_id',
                'oxobjectid' => $userId,
                'oxtype' => 'oxuser',
            ]);
        $qb->executeStatement();
    }

    private function createDiscountAssignment(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxobject2discount')
            ->values([
                'oxid' => ':oxid',
                'oxdiscountid' => ':oxdiscountid',
                'oxobjectid' => ':oxobjectid',
                'oxtype' => ':oxtype',
            ])
            ->setParameters([
                'oxid' => 'test_discount_id',
                'oxdiscountid' => 'test_discount_rule_id',
                'oxobjectid' => $userId,
                'oxtype' => 'oxuser',
            ]);
        $qb->executeStatement();
    }

    private function createRecommendationList(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxrecommlists')
            ->values([
                'oxid' => ':oxid',
                'oxshopid' => ':oxshopid',
                'oxuserid' => ':oxuserid',
                'oxauthor' => ':oxauthor',
                'oxtitle' => ':oxtitle',
                'oxdesc' => ':oxdesc',
            ])
            ->setParameters([
                'oxid' => 'test_recomm_id',
                'oxshopid' => 1,
                'oxuserid' => $userId,
                'oxauthor' => 'Test Author',
                'oxtitle' => 'Test List',
                'oxdesc' => '',
            ]);
        $qb->executeStatement();
    }

    private function createArticle(string $articleId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxarticles')
            ->values(['oxid' => ':oxid'])
            ->setParameter('oxid', $articleId);
        $qb->executeStatement();
    }

    private function createReview(string $userId, string $articleId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxreviews')
            ->values([
                'oxid' => ':oxid',
                'oxobjectid' => ':oxobjectid',
                'oxtype' => ':oxtype',
                'oxtext' => ':oxtext',
                'oxuserid' => ':oxuserid',
            ])
            ->setParameters([
                'oxid' => 'test_review_id',
                'oxobjectid' => $articleId,
                'oxtype' => 'oxarticle',
                'oxtext' => '',
                'oxuserid' => $userId,
            ]);
        $qb->executeStatement();
    }

    private function createRating(string $userId, string $articleId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxratings')
            ->values([
                'oxid' => ':oxid',
                'oxshopid' => ':oxshopid',
                'oxuserid' => ':oxuserid',
                'oxtype' => ':oxtype',
                'oxobjectid' => ':oxobjectid',
                'oxrating' => ':oxrating',
            ])
            ->setParameters([
                'oxid' => 'test_rating_id',
                'oxshopid' => 1,
                'oxuserid' => $userId,
                'oxtype' => 'oxarticle',
                'oxobjectid' => $articleId,
                'oxrating' => 5,
            ]);
        $qb->executeStatement();
    }

    private function createPriceAlarm(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxpricealarm')
            ->values([
                'oxid' => ':oxid',
                'oxshopid' => ':oxshopid',
                'oxuserid' => ':oxuserid',
                'oxemail' => ':oxemail',
                'oxartid' => ':oxartid',
            ])
            ->setParameters([
                'oxid' => 'test_pricealarm_id',
                'oxshopid' => 1,
                'oxuserid' => $userId,
                'oxemail' => 'test@example.com',
                'oxartid' => 'test_article_id',
            ]);
        $qb->executeStatement();
    }

    private function createAcceptedTerms(string $userId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxacceptedterms')
            ->values([
                'oxuserid' => ':oxuserid',
                'oxshopid' => ':oxshopid',
                'oxtermversion' => ':oxtermversion',
            ])
            ->setParameters([
                'oxuserid' => $userId,
                'oxshopid' => 1,
                'oxtermversion' => '1',
            ]);
        $qb->executeStatement();
    }

    private function createRemark(string $userId, string $type, string $remarkId): void
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->insert('oxremark')
            ->values([
                'oxid' => ':oxid',
                'oxparentid' => ':oxparentid',
                'oxtype' => ':oxtype',
                'oxtext' => ':oxtext',
            ])
            ->setParameters([
                'oxid' => $remarkId,
                'oxparentid' => $userId,
                'oxtype' => $type,
                'oxtext' => '',
            ]);
        $qb->executeStatement();
    }

    private function fetchByUserIdColumn(string $table, string $column, string $userId): mixed
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->select($column)
            ->from($table)
            ->where("$column = :userid")
            ->setParameter('userid', $userId);

        return $qb->executeQuery()->fetchOne();
    }

    private function fetchRemarkByParentAndType(string $userId, string $type): mixed
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->select('oxid')
            ->from('oxremark')
            ->where('oxparentid = :oxparentid AND oxtype = :oxtype')
            ->setParameter('oxparentid', $userId)
            ->setParameter('oxtype', $type);

        return $qb->executeQuery()->fetchOne();
    }

    private function fetchRemarkById(string $remarkId): mixed
    {
        $qb = $this->get(QueryBuilderFactoryInterface::class)->create();
        $qb->select('oxid')
            ->from('oxremark')
            ->where('oxid = :oxid')
            ->setParameter('oxid', $remarkId);

        return $qb->executeQuery()->fetchOne();
    }
}
