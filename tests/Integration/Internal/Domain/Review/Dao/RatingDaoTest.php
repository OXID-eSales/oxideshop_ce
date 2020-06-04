<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;
use OxidEsales\EshopCommunity\Tests\TestUtils\IntegrationTestCase;
use Webmozart\PathUtil\Path;

class RatingDaoTest extends IntegrationTestCase
{
    public function testGetRatingsByUserId()
    {
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'rating_dao_get_by_user_id_fixtures.yaml')]);

        $ratingDao = $this->get(RatingDaoInterface::class);
        $ratings = $ratingDao->getRatingsByUserId('user1');

        $this->assertCount(2, $ratings->toArray());
        $this->assertInstanceOf(Rating::class, $ratings->first());
    }

    public function testGetRatingsByProductId()
    {
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'rating_dao_get_by_product_id_fixtures.yaml')]);

        $ratingDao = $this->get(RatingDaoInterface::class);
        $ratings = $ratingDao->getRatingsByProductId('product1');

        $this->assertCount(2, $ratings->toArray());
        $this->assertInstanceOf(Rating::class, $ratings->first());
    }

    public function testDeleteRating()
    {
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'rating_dao_delete_fixtures.yaml')]);

        $ratingDao = $this->get(RatingDaoInterface::class);

        $ratingsBeforeDeletion = $ratingDao->getRatingsByUserId('user1');
        $ratingToDelete = $ratingsBeforeDeletion->first();

        $ratingDao->delete($ratingToDelete);

        $ratingsAfterDeletion = $ratingDao->getRatingsByUserId('user1');

        $this->assertNotContains(
            $ratingToDelete,
            $ratingsAfterDeletion->toArray()
        );
    }

    private function getRatingDao()
    {
        return $this->get(RatingDaoInterface::class);
    }
}
