<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[group('cookieNotice')]
final class CookiesNoticeCest
{
    public function testCookieNoticeAccept(AcceptanceTester $I): void
    {
        $I->wantToTest('accept cookie functionality');

        $this->setCookieNoticeInactive($I);

        $I->openShop()
            ->dontSeeCookieNotice();

        $this->setCookieNoticeActive($I);
        $I->openShop()
            ->seeCookieNotice()
            ->closeCookieNotice();

        $I->openShop()
            ->dontSeeCookieNotice();
    }

    public function testCookieNoticeReject(AcceptanceTester $I): void
    {
        $I->wantToTest('reject cookie functionality');

        $this->setCookieNoticeActive($I);

        $I->openShop()
            ->seeCookieNotice()
            ->rejectCookies()
            ->seeRejectInfo();
    }

    private function setCookieNoticeActive(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blShowCookiesNotification', true);
        $I->clearShopCache();
    }

    private function setCookieNoticeInactive(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blShowCookiesNotification', false);
        $I->clearShopCache();
    }
}
