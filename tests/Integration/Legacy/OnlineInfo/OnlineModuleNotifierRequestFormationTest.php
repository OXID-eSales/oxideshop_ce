<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\OnlineInfo;

use OxidEsales\Eshop\Core\OnlineModuleVersionNotifier;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\SimpleXml;
use OxidEsales\EshopCommunity\Application\Controller\FrontendController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Container\ContainerInterface;
use SimpleXMLElement;
use Throwable;

final class OnlineModuleNotifierRequestFormationTest extends IntegrationTestCase
{
    private ContainerInterface $container;
    private string $clusterId;
    private string $documentName = 'omvnRequest';
    private string $edition;
    private string $module1Id = 'extending_1_class';
    private string $module1Title = 'Test extending 1 shop class';
    private string $module1Description = 'Module testing extending 1 shop class';
    private string $module1ClassExtensionsShopClass = 'OxidEsales\Eshop\Application\Model\Order';
    private string $module1ClassExtensionsModuleClass = 'oeTest/extending_1_class/myorder';
    private string $module1ControllerId = FrontendController::class;
    private string $module1ControllerClassNameSpace = 'oeTest/controller_1_class/myFrontendController';
    private string $module2Id = 'extending_1_class_3_extensions';
    private string $module2Title = 'Test extending 1 shop class with 3 extensions';
    private string $module2Description = 'Module testing extending 1 shop class with 3 extensions';
    private string $module2ClassExtensionsShopClass = 'OxidEsales\Eshop\Application\Model\Order';
    private string $module2ClassExtensionsModuleClass = 'oeTest/extending_1_class_3_extensions/myorder1';
    private string $moduleVersion = '1.0';
    private string $pVersion = '1.1';
    private string $productId = 'eShop';
    private string $shopUrl;
    private string $shopVersion;
    private string $xmlLog;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = ContainerFactory::getInstance()->getContainer();
        $this->prepareTestData();
    }

    public function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    public function testRequestFormation(): void
    {
        $licenseCaller = new OnlineModuleVersionNotifierCaller(
            oxNew(CurlSpy::class, $this->xmlLog),
            oxNew(OnlineServerEmailBuilder::class),
            oxNew(SimpleXml::class)
        );
        (new OnlineModuleVersionNotifier($licenseCaller))->versionNotify();

        $xml = $this->loadRequestLogXml();
        $this->assertEquals(7, $xml->count());
        $this->assertEquals($this->documentName, $xml->getName());
        $this->assertEquals($this->pVersion, $xml->pVersion);
        $this->assertEquals($this->edition, $xml->edition);
        $this->assertEquals($this->shopVersion, $xml->version);
        $this->assertEquals($this->shopUrl, $xml->shopUrl);
        $this->assertEquals($this->productId, $xml->productId);
        $this->assertEquals(2, $xml->modules->children()->count());
        /** module 1 */
        $this->assertEquals(10, $xml->modules->module[0]->children()->count());
        $this->assertEquals($this->module1Id, $xml->modules->module[0]->id);
        $this->assertEquals($this->moduleVersion, $xml->modules->module[0]->version);
        $this->assertEquals($this->module1Title, $xml->modules->module[0]->title);
        $this->assertEquals($this->module1Description, $xml->modules->module[0]->description);
        $this->assertEquals(1, $xml->modules->module[0]->classExtensions->children()->count());
        $this->assertEquals(
            $this->module1ClassExtensionsShopClass,
            $xml->modules->module[0]->classExtensions->children()->children()->shopClass
        );
        $this->assertEquals(
            $this->module1ClassExtensionsModuleClass,
            $xml->modules->module[0]->classExtensions->children()->children()->moduleClass
        );
        $this->assertEquals(1, $xml->modules->module[0]->controllers->children()->count());
        $this->assertEquals(
            $this->module1ControllerId,
            $xml->modules->module[0]->controllers->children()->children()->id
        );
        $this->assertEquals(
            $this->module1ControllerClassNameSpace,
            $xml->modules->module[0]->controllers->children()->children()->controllerClassNameSpace
        );
        /** active in shops */
        $this->assertEquals(1, $xml->modules->module[0]->activeInShops->children()->count());
        $this->assertEquals($this->shopUrl, $xml->modules->module[0]->activeInShops->activeInShop);
        /** module 2 */
        $this->assertEquals(10, $xml->modules->module[1]->children()->count());
        $this->assertEquals($this->module2Id, $xml->modules->module[1]->id);
        $this->assertEquals($this->moduleVersion, $xml->modules->module[1]->version);
        $this->assertEquals($this->module2Title, $xml->modules->module[1]->title);
        $this->assertEquals($this->module2Description, $xml->modules->module[1]->description);
        $this->assertCount(1, $xml->modules->module[0]->classExtensions->children());
        $this->assertEquals(
            $this->module2ClassExtensionsShopClass,
            $xml->modules->module[1]->classExtensions->children()->children()->shopClass
        );
        $this->assertEquals(
            $this->module2ClassExtensionsModuleClass,
            $xml->modules->module[1]->classExtensions->children()->children()->moduleClass
        );
        $this->assertEmpty($xml->modules->module[1]->controllers->children());
        /** active in shops */
        $this->assertEquals(1, $xml->modules->module[1]->activeInShops->children()->count());
        $this->assertEquals($this->shopUrl, $xml->modules->module[1]->activeInShops->activeInShop);
    }

    private function prepareTestData(): void
    {
        $this->xmlLog = sprintf('%s/%s.xml', __DIR__, uniqid('request_log_', true));
        $this->edition = Registry::getConfig()->getEdition()->value;
        $this->shopVersion = ShopVersion::getVersion();
        $this->shopUrl = Registry::getConfig()->getShopUrl();
        $this->clusterId = uniqid('cluster-', true);

        Registry::getConfig()->setConfigParam('sClusterId', [$this->clusterId]);

        $this->container
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        $this->installModule($this->module1Id);
        $this->activateModule($this->module1Id);

        $this->installModule($this->module2Id);
        $this->activateModule($this->module2Id);
    }

    private function installModule(string $moduleId): void
    {
        $package = new OxidEshopPackage(__DIR__ . '/../Modules/TestData/modules/' . $moduleId);
        $this->container->get(ModuleInstallerInterface::class)->install($package);
    }

    private function uninstallModule(string $moduleId): void
    {
        $package = new OxidEshopPackage(__DIR__ . '/../Modules/TestData/modules/' . $moduleId);
        $this->container->get(ModuleInstallerInterface::class)->uninstall($package);
    }

    private function activateModule(string $moduleId): void
    {
        $this->container->get(ModuleActivationBridgeInterface::class)->activate($moduleId, 1);
    }

    private function loadRequestLogXml(): SimpleXMLElement
    {
        return simplexml_load_string(file_get_contents($this->xmlLog));
    }

    private function cleanUpTestData(): void
    {
        $fileSystem = $this->container->get('oxid_esales.symfony.file_system');
        if ($fileSystem->exists($this->xmlLog)) {
            $fileSystem->remove($this->xmlLog);
        }
        try {
            $this->uninstallModule($this->moduleId1);
            $this->uninstallModule($this->moduleId2);
        } catch (Throwable) {
        }
    }
}
