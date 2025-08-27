<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'system-info')]
final class SystemInfoCest
{
    public function defaultTimezone(AcceptanceTester $I): void
    {
        $I->wantToTest('the default timezone is set on application start.');

        $I->loginAdmin()
            ->openSystemInfo()
            ->seeRowInDateTable(
                'Default timezone',
                getenv('OXID_DEFAULT_TIMEZONE')
            );
    }
}
