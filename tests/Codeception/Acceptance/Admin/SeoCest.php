<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class SeoCest
{
    public function updateStaticUrl(AcceptanceTester $I): void
    {
        $I->wantToTest('static SEO URL form');
        $I
            ->loginAdmin()
            ->openCoreSettings()
            ->openSEOTab()
            ->selectStaticSeoUrl('index.php?cl=account')
            ->seeInStaticSeoUrlFields(
                'index.php?cl=account',
                'mein-konto/',
                'en/my-account/'
            )
            ->fillStaticSeoUrlFields(
                'some-new-german-url/',
                'some-new-english-url/'
            )
            ->save()
            ->selectStaticSeoUrl('index.php?cl=account')
            ->seeInStaticSeoUrlFields(
                'index.php?cl=account',
                'some-new-german-url/',
                'en/some-new-english-url/'
            );
    }
}
