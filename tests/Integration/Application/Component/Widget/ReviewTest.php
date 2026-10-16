<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component\Widget;

use OxidEsales\Eshop\Application\Component\Widget\Review;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ReviewTest extends IntegrationTestCase
{
    private int $pushedViewCount = 0;

    public function setUp(): void
    {
        parent::setUp();
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    public function tearDown(): void
    {
        $config = Registry::getConfig();
        for ($i = 0; $i < $this->pushedViewCount; $i++) {
            $config->dropLastActiveView();
        }
        $this->pushedViewCount = 0;
        parent::tearDown();
    }

    public function testGetReviewsReturnsNullWhenCalledWithoutParentView(): void
    {
        $widget = oxNew(Review::class);
        $this->pushActiveView($widget);

        $this->assertNull($widget->getReviews());
    }

    private function pushActiveView(object $view): void
    {
        Registry::getConfig()->setActiveView($view);
        $this->pushedViewCount++;
    }
}
