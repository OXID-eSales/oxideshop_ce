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
final class NewCMSCreationCest
{
    /**
     * @group exclude_from_compilation
     */
    public function newCMSCreation(AcceptanceTester $I): void
    {
        $I->wantToTest('Create a new CMS and check if it is saved in database');

        $title = 'New CMS Content';
        $content = 'This is a new CMS content';
        $ident = 'newcmscontent';

        $adminPanel = $I->loginAdmin();
        $languages = $adminPanel->openCMSPages();
        $languages->createNewCMS($title, $ident, $content);
        $languages->find('where[oxcontents][oxtitle]', $title);

        $I->assertEquals($title, $I->grabFromDatabase('oxcontents', 'oxtitle', ['oxloadid' => $ident]));
    }
}
