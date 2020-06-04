<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\UserReviewBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ReviewDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewService;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\TestUtils\IntegrationTestCase;
use Webmozart\PathUtil\Path;

class ReviewDaoTest extends IntegrationTestCase
{
    public function testGetReviewsByUserId()
    {
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'review_dao_get_by_user_id_fixtures.yaml')]);

        $reviewDao = $this->get(ReviewDaoInterface::class);
        $reviews = $reviewDao->getReviewsByUserId('user1');

        $this->assertCount(2, $reviews->toArray());
    }

    public function testDeleteReview()
    {
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'review_dao_delete_fixtures.yaml')]);

        $reviewDao = $this->get(ReviewDaoInterface::class);

        $reviewsBeforeDeletion = $reviewDao->getReviewsByUserId('user1');
        $reviewToDelete = $reviewsBeforeDeletion->first();

        $reviewDao->delete($reviewToDelete);

        $reviewsAfterDeletion = $reviewDao->getReviewsByUserId('user1');

        $this->assertFalse(
            in_array(
                $reviewToDelete,
                $reviewsAfterDeletion->toArray()
            )
        );
    }
}
