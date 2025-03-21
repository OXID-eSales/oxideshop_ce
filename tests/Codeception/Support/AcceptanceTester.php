<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Support;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Admin\AdminLoginPage;
use OxidEsales\Codeception\Admin\AdminPanel;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Home;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\_generated\AcceptanceTesterActions;

class AcceptanceTester extends AcceptanceActor
{
    use AcceptanceTesterActions;

    public function openShop(): Home
    {
        Translator::switchTranslationDomain(
            Translator::TRANSLATION_DOMAIN_SHOP
        );
        $I = $this;
        $homePage = new Home($I);
        $I->amOnPage($homePage->URL);
        $I->waitForPageLoad();
        return $homePage;
    }

    public function loginShopWithExistingUser(): Home
    {
        $homePage = $this->openShop();
        $user = Fixtures::get('existingUser');
        return $homePage->loginUser($user['userLoginName'], $user['userPassword']);
    }

    public function openAdmin(): AdminLoginPage
    {
        Translator::switchTranslationDomain(
            Translator::TRANSLATION_DOMAIN_ADMIN
        );
        $I = $this;
        $adminLogin = new AdminLoginPage($I);
        $I->amOnPage($adminLogin->URL);
        return $adminLogin;
    }

    public function loginAdmin(): AdminPanel
    {
        $adminPage = $this->openAdmin();
        $admin = Fixtures::get('adminUser');
        return $adminPage->login($admin['userLoginName'], $admin['userPassword']);
    }
}
