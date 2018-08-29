<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\TestingLibrary\UnitTestCase;

class BasketComponentTest extends UnitTestCase
{
    public function testChangingBasketWhenSessionChallengeValidationNotPassed()
    {
        $this->actAsSearchEngine(false);
        $this->sessionTokenIsCorrect(false);
        $this->initiateBasketChange();

        $this->assertFalse($this->isBasketChanged());
    }

    public function testChangingBasketWhenSessionChallengeValidationPassed()
    {
        $this->actAsSearchEngine(false);
        $this->sessionTokenIsCorrect(true);
        $this->initiateBasketChange();

        $this->assertTrue($this->isBasketChanged());
    }

    public function testChangingBasketWhenIsSearchEngine()
    {
        $this->actAsSearchEngine(true);
        $this->sessionTokenIsCorrect(true);
        $this->initiateBasketChange();

        $this->assertFalse($this->isBasketChanged());
    }

    public function testChangingBasketWhenIsNotSearchEngine()
    {
        $this->actAsSearchEngine(false);
        $this->sessionTokenIsCorrect(true);
        $this->initiateBasketChange();

        $this->assertTrue($this->isBasketChanged());
    }

    private function actAsSearchEngine($isSearchEngine)
    {
        $utilities = $this->getMockBuilder(Utils::class)
            ->setMethods(['isSearchEngine'])->getMock();
        $utilities->method('isSearchEngine')->willReturn($isSearchEngine);
        Registry::set(Utils::class, $utilities);
    }

    private function sessionTokenIsCorrect($isCorrect)
    {
        $session = $this->getMockBuilder(Session::class)
            ->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn($isCorrect);
        Registry::set(Session::class, $session);
    }

    private function initiateBasketChange()
    {
        /** @var \OxidEsales\Eshop\Application\Component\BasketComponent $basketComponent */
        $basketComponent = oxNew(BasketComponent::class);
        $basketComponent->changeBasket(1000, 2);
    }

    /**
     * @return bool
     */
    private function isBasketChanged()
    {
        return isset($_SESSION['aLastcall']['changebasket'][1000]);
    }
}
