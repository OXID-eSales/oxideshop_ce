<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller\FrontendController;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use OxidEsales\Eshop\Application\Component\CategoriesComponent;
use OxidEsales\Eshop\Application\Component\CurrencyComponent;
use OxidEsales\Eshop\Application\Component\LanguageComponent;
use OxidEsales\Eshop\Application\Component\ShopComponent;
use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Component\UtilsComponent;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class FrontendComponentTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testGetComponentNames(): void
    {
        $componentName = get_class($this->getComponentClass());

        $this->setParameter('oxid_esales.cacheable_user_components', [$componentName => 1]);

        $componentNames = [
            UserComponent::class => true,
            LanguageComponent::class => false,
            CurrencyComponent::class => true,
            ShopComponent::class => true,
            CategoriesComponent::class => false,
            UtilsComponent::class => true,
            BasketComponent::class => true,
            $componentName => true,
        ];

        $view = oxNew(FrontendController::class);
        $this->assertEquals($componentNames, $view->getComponentNames());
    }

    private function getComponentClass(): FrontendController
    {
        return new class extends FrontendController {
        };
    }
}
