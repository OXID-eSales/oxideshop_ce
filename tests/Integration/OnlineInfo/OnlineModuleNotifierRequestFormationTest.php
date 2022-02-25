<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

use OxidEsales\Eshop\Core\OnlineModuleVersionNotifier;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\SimpleXml;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Facts\Facts;
use Psr\Container\ContainerInterface;

final class OnlineModuleNotifierRequestFormationTest extends \OxidTestCase
{
    private ContainerInterface $container;
    private string $clusterId;
    private string $documentName = 'omvnRequest';
    private string $edition;
    private string $moduleId1 = 'extending_1_class';
    private string $moduleId2 = 'extending_1_class_3_extensions';
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
        $this->assertEquals($this->clusterId, $xml->clusterId);
        $this->assertEquals($this->edition, $xml->edition);
        $this->assertEquals($this->shopVersion, $xml->version);
        $this->assertEquals($this->shopUrl, $xml->shopUrl);
        $this->assertEquals($this->productId, $xml->productId);
        $this->assertEquals(2, $xml->modules->children()->count());
        /** module 1 */
        $this->assertEquals(3, $xml->modules->module[0]->children()->count());
        $this->assertEquals($this->moduleId1, $xml->modules->module[0]->id);
        $this->assertEquals($this->moduleVersion, $xml->modules->module[0]->version);
        /** active in shops */
        $this->assertEquals(1, $xml->modules->module[0]->activeInShops->children()->count());
        $this->assertEquals($this->shopUrl, $xml->modules->module[0]->activeInShops->activeInShop);
        /** module 2 */
        $this->assertEquals(3, $xml->modules->module[1]->children()->count());
        $this->assertEquals($this->moduleId2, $xml->modules->module[1]->id);
        $this->assertEquals($this->moduleVersion, $xml->modules->module[1]->version);
        /** active in shops */
        $this->assertEquals(1, $xml->modules->module[1]->activeInShops->children()->count());
        $this->assertEquals($this->shopUrl, $xml->modules->module[1]->activeInShops->activeInShop);
    }

    private function prepareTestData(): void
    {
        $this->xmlLog = sprintf("%s/%s.xml", __DIR__, uniqid('request_log_', true));
        $this->edition = (new Facts())->getEdition();
        $this->shopVersion = ShopVersion::getVersion();
        $this->shopUrl = Registry::getConfig()->getShopUrl();
        $this->clusterId = uniqid('cluster-', true);

        Registry::getConfig()->setConfigParam('sClusterId', [$this->clusterId]);

        $this->container
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        $this->installModule($this->moduleId1);
        $this->activateModule($this->moduleId1);

        $this->installModule($this->moduleId2);
        $this->activateModule($this->moduleId2);
    }

    private function installModule(string $moduleId): void
    {
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/../Modules/TestData/modules/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $this->container->get(ModuleInstallerInterface::class)->install($package);
    }

    private function activateModule(string $moduleId): void
    {
        $this->container->get(ModuleActivationBridgeInterface::class)->activate($moduleId, 1);
    }

    private function loadRequestLogXml(): \SimpleXMLElement
    {
        return simplexml_load_string(
            file_get_contents($this->xmlLog)
        );
    }

    private function cleanUpTestData(): void
    {
        $fileSystem = $this->container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->container->get(ContextInterface::class)->getModulesPath() . '/oeTest/');
        if ($fileSystem->exists($this->xmlLog)) {
            $fileSystem->remove($this->xmlLog);
        }
    }
}
