<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\SystemInfoController;
use OxidEsales\Eshop\Application\Controller\Admin\ToolsController;
use OxidEsales\Eshop\Application\Controller\Admin\ToolsMain;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\AccessDeniedException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class DemoShopAdminToolsTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        Registry::getSession()->deleteVariable('auth');

        parent::tearDown();
    }

    public static function adminToolDataProvider(): array
    {
        return [
            'system info' => [SystemInfoController::class],
            'tools' => [ToolsController::class],
            'tools main' => [ToolsMain::class],
        ];
    }

    #[DataProvider('adminToolDataProvider')]
    public function testRenderDeniesAccessInDemoShop(string $controllerClass): void
    {
        $this->signInMallAdmin();
        $this->setParameter('oxid_esales.demo_shop_mode', true);

        $this->expectException(AccessDeniedException::class);

        oxNew($controllerClass)->render();
    }

    private function signInMallAdmin(): void
    {
        $user = oxNew(User::class);
        $user->setId('demo-shop-admin');
        $user->oxuser__oxusername = new Field('demo-shop-admin@example.com');
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->save();
        ContainerFacade::get(QueryBuilderFactoryInterface::class)->create()
            ->update('oxuser')
            ->set('oxrights', ':rights')
            ->where('oxid = :oxid')
            ->setParameters(['rights' => 'malladmin', 'oxid' => 'demo-shop-admin'])
            ->executeStatement();

        Registry::getSession()->setVariable('auth', 'demo-shop-admin');
    }
}
