<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Core\Session;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class BasketComponentSessionTest extends IntegrationTestCase
{
    private string $articleId = '1000';

    private bool $configBlUseStock;

    public function setUp(): void
    {
        parent::setUp();
        $this->configBlUseStock = Registry::getConfig()->getConfigParam('blUseStock');
        Registry::getConfig()->setConfigParam('blUseStock', false);
        Registry::getSession()->setVariable('aLastcall', []);
        $this->createTestProduct();
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('blUseStock', $this->configBlUseStock);
        parent::tearDown();
    }

    public function testChangingBasketWhenSessionChallengeValidationNotPassed(): void
    {
        $this->actAsSearchEngine(false);
        $this->initSessionMock(false);

        oxNew(BasketComponent::class)->changeBasket($this->articleId, 2);

        $this->assertEquals(0, $this->getBasketItemCount());
        $this->assertFalse($this->isChangeBasketKeyExisted());
    }

    public function testChangingBasketWhenSessionChallengeValidationPassed(): void
    {
        $this->actAsSearchEngine(false);
        $this->initSessionMock(true);

        $_SESSION['sess_stoken'] = 'randomtoken';
        $_POST['stoken'] = 'randomtoken';

        oxNew(BasketComponent::class)->changeBasket($this->articleId, 2);

        $this->assertEquals(2, $this->getBasketItemCount());
        $this->assertTrue($this->isChangeBasketKeyExisted());
    }

    public function testChangingBasketWhenIsSearchEngine(): void
    {
        $this->actAsSearchEngine(true);
        $this->initSessionMock(true);

        oxNew(BasketComponent::class)->changeBasket($this->articleId, 2);

        $this->assertEquals(0, $this->getBasketItemCount());
        $this->assertFalse($this->isChangeBasketKeyExisted());
    }

    private function createTestProduct(): void
    {
        $product = oxNew(Article::class);
        $product->setId($this->articleId);
        $product->save();
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

    private function initSessionMock(bool $checkSessionChallenge): void
    {
        $session = $this->createConfiguredMock(
            Session::class,
            ['checkSessionChallenge' => $checkSessionChallenge]
        );

        Registry::set(
            Session::class,
            $session
        );
    }

    private function getBasketItemCount(): float
    {
        $articles = Registry::getSession()->getBasket()->getBasketSummary()->aArticles;
        return $articles[$this->articleId] ?? 0.0;
    }

    private function isChangeBasketKeyExisted(): bool
    {
        $lastCall = Registry::getSession()->getVariable('aLastcall');
        return isset($lastCall['changebasket'][1000]);
    }
}
