<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ComponentInstaller;
use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

class ComponentInstallerTest extends TestCase
{
    use ContainerTrait;

    private $servicesFilePath = __DIR__ . '/Fixtures/services.yaml';

    public function tearDown()
    {
        parent::tearDown();

        $this->removeGeneratedLineFromProjectFile();
    }

    public function testInstall()
    {
        $installer = $this->createInstaller();
        $installer->install(__DIR__ . '/Fixtures');

        $this->assertTrue($this->doesServiceLineExists());
    }

    public function testUpdate()
    {
        $installer = $this->createInstaller();
        $installer->update(__DIR__ . '/Fixtures');

        $this->assertTrue($this->doesServiceLineExists());
    }

    /**
     * @return ComponentInstaller
     */
    private function createInstaller(): ComponentInstaller
    {
        $packageStub = $this->getMockBuilder(Package::class)->disableOriginalConstructor()->getMock();
        $installer = new ComponentInstaller(
            new NullIO(),
            (new Facts)->getShopRootPath(),
            $packageStub
        );
        return $installer;
    }

    private function doesServiceLineExists()
    {
        $context = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);
        $contentsOfProjectFile = file_get_contents(
            $context->getGeneratedServicesFilePath()
        );

        return (bool)strpos($contentsOfProjectFile, $this->servicesFilePath);
    }

    private function removeGeneratedLineFromProjectFile()
    {
        /** @var ProjectYamlDao $projectYamlDao */
        $projectYamlDao = $this->get(ProjectYamlDaoInterface::class);
        $DIconfig = $projectYamlDao->loadProjectConfigFile();
        $DIconfig->removeImport($this->servicesFilePath);
        $projectYamlDao->saveProjectConfigFile($DIconfig);
    }
}
