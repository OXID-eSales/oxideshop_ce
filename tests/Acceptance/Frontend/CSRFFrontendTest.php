<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/**
 * Test csrf token matching.
 */
class CSRFFrontendTest extends FrontendTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->clearCookies();
    }

    public function testAddToBasketWithoutCSRFToken(): void
    {
        $this->openShop();
        $this->loginInFrontend('example_test@oxid-esales.dev', 'useruser');

        $this->assertBasketIsEmpty();
        $this->addToBasketWithoutCSRFToken();

        $this->assertTextPresent('%ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN%');
    }

    public function testAddToBasketWithCSRFToken(): void
    {
        $this->openShop();
        $this->loginInFrontend('example_test@oxid-esales.dev', 'useruser');

        $this->assertBasketIsEmpty();
        $this->openArticle(1000);
        $this->clickAndWait('toBasket');
        $this->assertBasketIsNotEmpty();
    }

    public function testGuestAddToBasket(): void
    {
        $this->assertBasketIsEmpty();
        $this->addToBasket(1000);
        $this->assertBasketIsNotEmpty();
    }

    private function assertBasketIsEmpty(): void
    {
        $this->open($this->_getShopUrl(['cl' => 'basket']));
        $this->assertEquals('%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%', $this->getText('breadCrumb'));
        $this->assertTextPresent('%BASKET_EMPTY%');
    }

    private function assertBasketIsNotEmpty(): void
    {
        $this->open($this->_getShopUrl(['cl' => 'basket']));
        $this->assertEquals('%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%', $this->getText('breadCrumb'));
        $this->assertTextNotPresent('%BASKET_EMPTY%');
    }

    private function addToBasketWithoutCSRFToken(): void
    {
        $data = [
            'actcontrol' => 'start',
            'lang'       => '1',
            'cl'         => 'start',
            'fnc'        => 'tobasket',
            'aid'        => 'dc5ffdf380e15674b56dd562a7cb6aec',
            'anid'       => 'dc5ffdf380e15674b56dd562a7cb6aec',
            'am'         => 1
        ];
        $url = $this->_getShopUrl($data);

        $this->open($url);
    }
}
