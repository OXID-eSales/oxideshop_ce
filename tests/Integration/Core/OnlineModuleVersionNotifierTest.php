<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\OnlineModulesNotifierRequest;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifier;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\FilesystemTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Filesystem\Path;

final class OnlineModuleVersionNotifierTest extends TestCase
{
    use FilesystemTrait;

    private string $activeModuleId = 'ExampleModule';
    private string $inactiveModuleId = 'NotActiveModuleWithMissingData';
    private string $moduleFixturesPath = '/Fixtures/Modules/';
    private int $shopId = 1;

    private array $modulesData = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->backupVarDirectory();
        ContainerFacade::get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
        $this->installModule($this->activeModuleId);
        $this->activateModule($this->activeModuleId);
        $this->installModule($this->inactiveModuleId);
    }

    public function tearDown(): void
    {
        $this->uninstallModule($this->activeModuleId);
        $this->uninstallModule($this->inactiveModuleId);
        $this->restoreVarDirectory();

        parent::tearDown();
    }

    public function testVersionNotifySendsCorrectModuleData(): void
    {
        $expectedRequest = $this->createNotifierRequest();

        $notifierCaller = $this->createMock(OnlineModuleVersionNotifierCaller::class);
        $notifierCaller->expects($this->once())
            ->method('doRequest')
            ->with($this->equalTo($expectedRequest));

        $notifier = new OnlineModuleVersionNotifier($notifierCaller);
        $notifier->versionNotify();
    }

    private function createNotifierRequest(): OnlineModulesNotifierRequest
    {
        $modulesData = [
            [
                'id' => 'ExampleModule',
                'version' => '1.0',
                'title' => 'Test OXID eShop module',
                'description' => 'Empty Module For Testing',
                'author' => 'OXID eSales AG',
                'url' => 'testurl.com',
                'email' => 'test@email.com',
                'classExtensions' => [],
                'controllers' => [],
                'activeInShop' => [Registry::getConfig()->getShopUrl()],
            ],
            [
                'id' => 'NotActiveModuleWithMissingData',
                'version' => '1.0',
                'title' => '',
                'description' => '',
                'author' => '',
                'url' => '',
                'email' => '',
                'classExtensions' => [],
                'controllers' => [],
                'activeInShop' => [],
            ],
        ];

        $modules = [];
        foreach ($modulesData as $moduleData) {
            $module = new stdClass();
            $module->id = $moduleData['id'];
            $module->version = $moduleData['version'];
            $module->title = $moduleData['title'];
            $module->description = $moduleData['description'];
            $module->author = $moduleData['author'];
            $module->url = $moduleData['url'];
            $module->email = $moduleData['email'];
            $module->classExtensions = $moduleData['classExtensions'];
            $module->controllers = $moduleData['controllers'];
            $module->activeInShops = new stdClass();
            $module->activeInShops->activeInShop = $moduleData['activeInShop'];

            $modules[] = $module;
        }

        $request = new OnlineModulesNotifierRequest();
        $request->modules = new stdClass();
        $request->modules->module = $modules;

        return $request;
    }

    private function installModule(string $moduleId): void
    {
        $installService = ContainerFacade::get(ModuleInstallerInterface::class);
        $installService->install($this->getModulePackage($moduleId));
    }

    private function activateModule(string $moduleId): void
    {
        $activationService = ContainerFacade::get(ModuleActivationBridgeInterface::class);
        $activationService->activate($moduleId, $this->shopId);
    }

    private function uninstallModule(string $moduleId): void
    {
        $installService = ContainerFacade::get(ModuleInstallerInterface::class);
        $installService->uninstall($this->getModulePackage($moduleId));
    }

    private function getModulePackage(string $moduleId): OxidEshopPackage
    {
        return new OxidEshopPackage(Path::join(__DIR__, $this->moduleFixturesPath, $moduleId));
    }
}
