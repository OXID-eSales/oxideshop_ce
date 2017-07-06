<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
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
