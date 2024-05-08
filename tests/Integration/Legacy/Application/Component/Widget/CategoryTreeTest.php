<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Component\Widget;

use OxidEsales\Eshop\Application\Component\Widget\CategoryTree;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * @todo move to templating engine component tests
 */
final class CategoryTreeTest extends IntegrationTestCase
{
    private CategoryTree $categoryTree;

    private string $fallbackWidgetTemplate = 'widget/sidebar/categorytree';

    public function setUp(): void
    {
        parent::setUp();
        $this->categoryTree = oxNew(CategoryTree::class);
    }

    public function testRenderWithDefaultTemplate(): void
    {
        $renderedTemplate = $this->categoryTree->render();

        $this->assertEquals($this->fallbackWidgetTemplate, $renderedTemplate);
    }

    public function testRenderWithNonExistingTemplate(): void
    {
        $nonExistingWidgetType = uniqid('widget-', true);
        $this->categoryTree->setViewParameters([
            'sWidgetType' => $nonExistingWidgetType,
        ]);

        $renderedTemplate = $this->categoryTree->render();

        $this->assertEquals($this->fallbackWidgetTemplate, $renderedTemplate);
    }
}
