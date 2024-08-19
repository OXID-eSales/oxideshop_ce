<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testLoginLogoutAdminDemoShop(): void
    {
        $this->createAdminUser();

        $this->setParameter('oxid_demo_shop_mode', true);
        Registry::getConfig()->setAdminMode(true);

        $user = oxNew(User::class);
        $user->login('admin', 'admin');

        $this->assertNotNull($this->getSessionParam('auth'));
        $this->assertNull($this->getSessionParam('usr'));

        $user = $user->getUser();
        $this->assertNotNull($user);
        $this->assertNotNull($user->getId());

        $user->logout();
        $this->assertNull($this->getSessionParam('usr'));
        $this->assertNull($this->getSessionParam('auth'));
        $this->assertFalse($user->getUser());
    }

    private function getSessionParam(string $parameterName)
    {
        return Registry::getSession()->getVariable($parameterName);
    }

    private function createAdminUser(): void
    {
        DatabaseProvider::getDb()
            ->execute("INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`) VALUES
        ('oxdefaultadmin', 1, 'malladmin', 1, 'admin', 'e3a8a383819630e42d9ef90be2347ea70364b5efbb11dfc59adbf98487e196fffe4ef4b76174a7be3f2338581e507baa61c852b7d52f4378e21bd2de8c1efa5e', '61646D696E61646D696E61646D696E', 1, '', 'Your Company Name', 'John', 'Doe', 'Maple Street', '2425', '', 'Any City', 'a7c40f631fc920687.20179984', '', '9041', '217-8918712', '217-8918713', 'MR', 1000, '2003-01-01 00:00:00', '2003-01-01 00:00:00', '', '', '0000-00-00', '', '', 0, 0)");
    }
}
