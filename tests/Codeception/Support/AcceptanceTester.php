<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Support;

use OxidEsales\EshopCommunity\Tests\Codeception\Support\_generated\AcceptanceTesterActions;
use Codeception\Lib\Friend;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Admin\AdminLoginPage;
use OxidEsales\Codeception\Admin\AdminPanel;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Home;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */

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
