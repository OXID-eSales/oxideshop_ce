<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class CMSPageChangeIdentCest
{
    private string $testCmsPageIdent = '_test_oxstdfooter';
    private string $cmsPageDemoIdent = 'oxstdfooter';

    #[Group('todo_add_clean_cache_after_database_update')]
    public function CMSPageChangeIdent(AcceptanceTester $I): void
    {
        $I->clearShopCache();
        $I->openShop();

        $cmsPageContent = $I->grabFromDatabase(
            'oxcontents',
            'OXCONTENT_1',
            ['OXLOADID' => $this->cmsPageDemoIdent]
        );

        $I->see(strip_tags($cmsPageContent));

        $I->updateInDatabase(
            'oxcontents',
            ['OXLOADID' => $this->testCmsPageIdent],
            ['OXLOADID' => $this->cmsPageDemoIdent]
        );

        $I->clearShopCache();
        $I->openShop();

        $I->dontSee(strip_tags($cmsPageContent));
    }
}
