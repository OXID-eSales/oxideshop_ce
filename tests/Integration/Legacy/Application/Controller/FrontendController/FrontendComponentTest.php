<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller\FrontendController;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use oxubase;

final class FrontendComponentTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testGetComponentNames(): void
    {
        $componentName = get_class($this->getComponentClass());

        $this->setParameter('oxid_cacheable_user_components', [$componentName => 1]);
        $this->attachContainerToContainerFactory();

        $componentNames = [
            'oxcmp_user' => true,
            'oxcmp_lang' => false,
            'oxcmp_cur' => true,
            'oxcmp_shop' => true,
            'oxcmp_categories' => false,
            'oxcmp_utils' => true,
            'oxcmp_basket' => true,
            $componentName => true,
        ];

        $view = oxNew('oxUBase');
        $this->assertEquals($componentNames, $view->getComponentNames());
    }

    private function getComponentClass(): oxubase
    {
        return new class extends oxUbase {
        };
    }
}
