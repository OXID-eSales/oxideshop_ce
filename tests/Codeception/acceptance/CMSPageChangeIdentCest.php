<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

final class CMSPageChangeIdentCest
{
    /**
     * @var string
     */
    private $testCmsPageIdent = '_test_oxstdfooter';

    /**
     * @var string
     */
    private $cmsPageDemoIdent = 'oxstdfooter';

    /** @param AcceptanceTester $I */
    public function _after(AcceptanceTester $I)
    {
        $I->updateInDatabase(
            'oxcontents',
            ['OXLOADID' => $this->cmsPageDemoIdent],
            ['OXLOADID' => $this->testCmsPageIdent]
        );
    }

    /**
     * @group todo_add_clean_cache_after_database_update
     * @param AcceptanceTester $I
     */
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
