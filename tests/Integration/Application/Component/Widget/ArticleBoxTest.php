<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component\Widget;

use OxidEsales\Eshop\Application\Component\Widget\ArticleBox;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ArticleBoxTest extends IntegrationTestCase
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

    public function testGetActiveCategoryReturnsNullWhenCalledWithoutParentView(): void
    {
        $widget = oxNew(ArticleBox::class);
        $this->pushActiveView($widget);

        $this->assertNull($widget->getActiveCategory());
    }

    public function testGetLinkDoesNotRecurseWhenCalledWithoutParentView(): void
    {
        $widget = oxNew(ArticleBox::class);
        $this->pushActiveView($widget);

        $this->assertIsString($widget->getLink());
    }

    private function pushActiveView(object $view): void
    {
        Registry::getConfig()->setActiveView($view);
        $this->pushedViewCount++;
    }
}
