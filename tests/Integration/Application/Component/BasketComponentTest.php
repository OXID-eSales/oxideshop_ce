<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class BasketComponentTest extends IntegrationTestCase
{
    public function testFrontendErrorMsgInToBasket(): void
    {
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())
            ->method('addErrorToDisplay')
            ->with('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');

        Registry::set(
            UtilsView::class,
            $utilsView
        );

        $this->actAsSearchEngine(false);
        $this->prepareSessionMock();

        oxNew(BasketComponent::class)->toBasket(1000, 2);
    }

    private function prepareSessionMock(): void
    {
        $basket = oxNew(Basket::class);
        $basket->setStockCheckMode(false);
        $session = $this->createConfiguredMock(
            Session::class,
            [
                'getId' => 'random-string',
                'isActualSidInCookie' => true,
                'checkSessionChallenge' => false,
                'getBasket' => $basket
            ]
        );

        Registry::set(
            Session::class,
            $session
        );
    }

    private function actAsSearchEngine(bool $isSearchEngine): void
    {
        Registry::set(
            Utils::class,
            $this->createConfiguredMock(
                Utils::class,
                ['isSearchEngine' => $isSearchEngine]
            )
        );
    }
}
