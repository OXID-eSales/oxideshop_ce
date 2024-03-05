<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\OnlineModulesNotifierRequest;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifier;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\FilesystemTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Facts\Facts;
use oxOnlineModulesNotifierRequest;
use oxOnlineModuleVersionNotifierCaller;
use PHPUnit\Framework\MockObject\MockObject;
use StdClass;
use Throwable;

final class OnlineModuleNotifierTest extends IntegrationTestCase
{
    use FilesystemTrait;

    private array $installedModules = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->installedModules = [];
        $this->backupVarDirectory();
        $this->truncateDatabase();
    }

    public function tearDown(): void
    {
        $this->uninstallTestedModules();
        $this->restoreVarDirectory();
        $this->get(ShopCacheCleanerInterface::class)->clear(1);

        parent::tearDown();
    }

    public function testVersionNotify(): void
    {
        $this->installModule('extending_1_class');
        $this->activateModule('extending_1_class');

        $this->installModule('extending_1_class_3_extensions');
        $this->activateModule('extending_1_class_3_extensions');

        $this->installModule('with_everything');

        /** @var oxOnlineModuleVersionNotifierCaller|MockObject $oCaller */
        $oCaller = $this->getMockBuilder(OnlineModuleVersionNotifierCaller::class)->disableOriginalConstructor()
            ->getMock();
        $oCaller->expects($this->once())
            ->method('doRequest')
            ->with($this->equalTo($this->getExpectedRequest()));

        $oNotifier = new OnlineModuleVersionNotifier($oCaller);
        $oNotifier->versionNotify();
    }

    /**
     * Returns formed request which should be returned during testing.
     *
     * @return oxOnlineModulesNotifierRequest
     */
    private function getExpectedRequest()
    {
        $request = oxNew(OnlineModulesNotifierRequest::class);

        $shopUrl = Registry::getConfig()->getShopUrl();
        $request->edition = (new Facts())->getEdition();
        $request->version = ShopVersion::getVersion();
        $request->shopUrl = $shopUrl;
        $request->pVersion = '1.1';
        $request->productId = 'eShop';

        $modules = new StdClass();
        $modules->module = [];

        $modulesInfo = [];
        $modulesInfo[] = [
            'id' => 'extending_1_class',
            'version' => '1.0',
            'activeInShop' => [$shopUrl],
        ];
        $modulesInfo[] = [
            'id' => 'extending_1_class_3_extensions',
            'version' => '1.0',
            'activeInShop' => [$shopUrl],
        ];
        $modulesInfo[] = [
            'id' => 'with_everything',
            'version' => '1.0',
            'activeInShop' => [],
        ];

        foreach ($modulesInfo as $moduleInfo) {
            $module = new StdClass();
            $module->id = $moduleInfo['id'];
            $module->version = $moduleInfo['version'];
            $module->activeInShops = new StdClass();
            $module->activeInShops->activeInShop = $moduleInfo['activeInShop'];
            $modules->module[] = $module;
        }

        $request->modules = $modules;

        return $request;
    }

    private function installModule(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(__DIR__ . '/TestData/modules/' . $moduleId)
            );
        $this->installedModules[] = $moduleId;
    }

    private function activateModule(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, 1);
    }

    private function uninstallModule(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall(
                new OxidEshopPackage(__DIR__ . '/TestData/modules/' . $moduleId)
            );
    }

    private function truncateDatabase(): void
    {
        DatabaseProvider::getDb()->execute('DELETE FROM `oxconfigdisplay`');
    }

    public function get(string $serviceId)
    {
        return ContainerFactory::getInstance()->getContainer()->get($serviceId);
    }

    private function uninstallTestedModules(): void
    {
        foreach ($this->installedModules as $moduleId) {
            try {
                $this->uninstallModule($moduleId);
            } catch (Throwable) {
            }
        }
    }
}
