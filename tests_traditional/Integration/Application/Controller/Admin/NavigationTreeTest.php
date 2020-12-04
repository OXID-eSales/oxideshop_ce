<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class NavigationTreeTest extends TestCase
{
    private const FIXTURE_MODULE_NAMES = [
        'module1',
        'module2'
    ];
    private const EXISTING_XML_ELEMENT_ID = 'mxcoresett';
    private const EXISTING_XML_ELEMENTS_ATTRIBUTE_NAME = 'cl';
    private const EXISTING_XML_ELEMENTS_ATTRIBUTE_VALUE_ORIGINAL = 'shop';
    private const EXISTING_XML_ELEMENTS_ATTRIBUTE_VALUE_CHANGED = 'MODULE1_SOME_OVERWRITTEN_VALUE';
    private const NEW_XML_ELEMENT_ID_MODULE_1 = 'TEST-MODULE-1-SOME-SUBMENU-NODE';
    private const NEW_XML_ELEMENT_ID_MODULE_2 = 'TEST-MODULE-2-SOME-SUBMENU-NODE';
    /** @var BasicContext */
    private $context;
    /** @var ContainerInterface */
    private $container;
    /** @var array */
    private $testPackageNames = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new BasicContext();
        $this->generateUniquePackageNames();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    public function testGetDomXmlWithNoActiveModulesWillReturnOriginalXml(): void
    {
        $this->assertShopsXmlIsInInitialState();
    }

    public function testGetDomXmlWith1ActiveModuleWillAdd1NewNode(): void
    {
        $this->assertShopsXmlIsInInitialState();
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        $xml = $this->getNavigationTree()->getDomXml()->saveXML();

        $this->assertStringContainsString(self::NEW_XML_ELEMENT_ID_MODULE_1, $xml);
    }

    public function testGetDomXmlWith2ActiveModulesWillAdd2NewNodes(): void
    {
        $this->assertShopsXmlIsInInitialState();
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->activateModule('module1');
        $this->activateModule('module2');

        $xml = $this->getNavigationTree()->getDomXml()->saveXML();

        $this->assertStringContainsString(self::NEW_XML_ELEMENT_ID_MODULE_1, $xml);
        $this->assertStringContainsString(self::NEW_XML_ELEMENT_ID_MODULE_2, $xml);
    }

    public function testGetDomXmlWithDeactivationWillAddNewNodeForActiveModule(): void
    {
        $this->assertShopsXmlIsInInitialState();
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->deactivateModule('module1');

        $xml = $this->getNavigationTree()->getDomXml()->saveXML();

        $this->assertStringNotContainsString(self::NEW_XML_ELEMENT_ID_MODULE_1, $xml);
        $this->assertStringContainsString(self::NEW_XML_ELEMENT_ID_MODULE_2, $xml);
    }

    public function testGetDomXmlWillApplyCorrectLoadOrderAndOverwriteShopsValue(): void
    {
        $this->assertShopsXmlIsInInitialState();
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        $attributeValue = $this->getTestedAttributeValue(
            $this->getNavigationTree()->getDomXml()
        );

        $this->assertSame(self::EXISTING_XML_ELEMENTS_ATTRIBUTE_VALUE_CHANGED, $attributeValue);
    }

    private function getNavigationTree(): NavigationTree
    {
        $navigationTree = (new NavigationTree());
        /** Add user stub */
        $user = oxNew('oxuser');
        $user->oxuser__oxrights = new \oxfield('testRights');
        $navigationTree->setUser($user);
        return $navigationTree;
    }

    private function get(string $class)
    {
        if (!$this->container) {
            $this->container = ContainerFactory::getInstance()->getContainer();
        }
        return $this->container->get($class);
    }

    private function installModuleFixture(string $moduleName): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getTestPackage($moduleName));
    }

    private function uninstallModuleFixture(string $moduleName): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getTestPackage($moduleName));
    }

    private function activateModule(string $moduleId): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->activate($moduleId, $this->context->getDefaultShopId());
    }

    private function deactivateModule(string $moduleId): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->deactivate($moduleId, $this->context->getDefaultShopId());
    }

    private function getTestPackage(string $moduleName): OxidEshopPackage
    {
        $packageFixturePath = __DIR__ . "/Fixtures/$moduleName/";
        return new OxidEshopPackage($this->testPackageNames[$moduleName], $packageFixturePath);
    }

    private function generateUniquePackageNames(): void
    {
        foreach (self::FIXTURE_MODULE_NAMES as $moduleName) {
            $this->testPackageNames[$moduleName] = uniqid('package_', true);
        }
    }

    private function cleanUpTestData(): void
    {
        foreach (self::FIXTURE_MODULE_NAMES as $moduleName) {
            $this->uninstallModuleFixture($moduleName);
        }
    }

    private function assertShopsXmlIsInInitialState(): void
    {
        $navigationTree = $this->getNavigationTree();
        $dom = $navigationTree->getDomXml();
        $attributeValue = $this->getTestedAttributeValue($dom);
        $xml = $dom->saveXML();

        $this->assertSame(self::EXISTING_XML_ELEMENTS_ATTRIBUTE_VALUE_ORIGINAL, $attributeValue);
        $this->assertStringNotContainsString(self::NEW_XML_ELEMENT_ID_MODULE_1, $xml);
        $this->assertStringNotContainsString(self::NEW_XML_ELEMENT_ID_MODULE_2, $xml);
    }

    private function getTestedAttributeValue(\DOMDocument $dom): string
    {
        $xPath = new \DOMXPath($dom);
        $existingElementXPath = sprintf('//*[@id="%s"]', self::EXISTING_XML_ELEMENT_ID);
        $element = $xPath->query($existingElementXPath)->item(0);
        return $element->getAttribute(self::EXISTING_XML_ELEMENTS_ATTRIBUTE_NAME);
    }
}
