<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

#[Group('admin')]
final class SeoCest
{
    public function updateStaticUrl(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('static SEO URLs updating');

        $accountData = [
            'static_url' => 'index.php?cl=account',
            'localized' => [
                0 => 'mein-konto/',
                1 => 'en/my-account/'
            ]
        ];
        $updatedAccountData = $accountData;
        $updatedAccountData['localized'][0] = 'konto/';
        $updatedAccountData['localized'][1] = 'en/account/';
        $contactData = [
            'static_url' => 'index.php?cl=contact',
            'localized' => [
                0 => 'kontakt/',
                1 => 'en/contact/'
            ]
        ];

        $adminPanel = $I->loginAdmin();
        $seoTab = $adminPanel->openCoreSettings()->openSEOTab();

        // Check if form is empty when nothing is selected
        $seoTab->seeInStaticSeoUrlFields('', '', '');

        // Select account SEO
        $seoTab
            ->selectStaticSeoUrl($accountData['static_url'])
            ->seeInStaticSeoUrlFields(
                $accountData['static_url'],
                $accountData['localized'][0],
                $accountData['localized'][1]
            );

        // Update data
        $seoTab
            ->fillStaticSeoUrlFields($updatedAccountData['localized'][0], $updatedAccountData['localized'][1])
            ->save()
            ->seeInStaticSeoUrlFields(
                $updatedAccountData['static_url'],
                $updatedAccountData['localized'][0],
                $updatedAccountData['localized'][1]
            );

        // Check other SEO
        $seoTab
            ->selectStaticSeoUrl($contactData['static_url'])
            ->seeInStaticSeoUrlFields(
                $contactData['static_url'],
                $contactData['localized'][0],
                $contactData['localized'][1]
            );

        // Check again the edited SEO
        $seoTab
            ->selectStaticSeoUrl($updatedAccountData['static_url'])
            ->seeInStaticSeoUrlFields(
                $updatedAccountData['static_url'],
                $updatedAccountData['localized'][0],
                $updatedAccountData['localized'][1]
            );
    }
}
