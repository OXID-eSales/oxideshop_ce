<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Utils\Database\FixtureLoader;
use OxidEsales\EshopCommunity\Tests\Utils\Database\TestDatabaseHandler;
use OxidEsales\EshopCommunity\Tests\Utils\Traits\CachingTrait;
use OxidEsales\EshopCommunity\Tests\Utils\Traits\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class IntegrationTestCase extends TestCase
{
    use ContainerTrait;
    use CachingTrait;

    const TESTVENDOR = 'oeTest';

    protected static $initialized = false;
    public function setUp(): void
    {
        if (!self::$initialized) {
            \OxidEsales\EshopCommunity\Tests\Utils\Database\TestDatabaseHandler::init();
            // Do something once here for _all_ test subclasses.
            self::$initialized = true;
        }
        parent::setUp();
        $this->cleanupCaching();
        FixtureLoader::getInstance()->reset();
        FixtureLoader::getInstance()->loadBasicFixtures();
        $this->activateModules();
    }

    public function tearDown(): void
    {
        $this->deactivateModules();
        $this->cleanupCaching();
        parent::tearDown();
    }

    public function loadFixtures(array $fixtureFiles)
    {
        FixtureLoader::getInstance()->loadFixtures($fixtureFiles);
    }

    public function cleanupTable($tablename)
    {
        FixtureLoader::getInstance()->cleanupTable($tablename);
    }

    public function loadFixture($fixtureFile)
    {
        $this->loadFixtures([$fixtureFile]);
    }

    public function cleanupFixtureTables()
    {
        FixtureLoader::getInstance()->cleanupFixtureTables();
    }

    private function activateModules()
    {
        if (! getenv('OXID_TEST_MODULES')) {
            return;
        }

        foreach (explode(':', getenv('OXID_TEST_MODULES')) as $modulePath) {
            $moduleId = $this->getModuleId($modulePath);
            $package = new OxidEshopPackage($moduleId, $modulePath);
            $package->setTargetDirectory($this->getTargetDirectory($modulePath, $moduleId));
            $this->get(ModuleInstallerInterface::class)->install($package);
            $this->get(ModuleActivationBridgeInterface::class)->activate($moduleId, 1);
        }
    }

    /**
     * Tries to get the vendor id for the module from composer.json
     *
     * @param string $modulePath
     * @param string $moduleId
     * @return string
     */
    private function getTargetDirectory(string $modulePath, string $moduleId): string
    {
        $composer = file_get_contents(Path::join($modulePath, 'composer.json'));
        if (preg_match('/modules\/(.*?)\/' . $moduleId . '/', $composer, $match)) {
            return Path::join($match[1], $moduleId);
        }
        return Path::join(self::TESTVENDOR, $moduleId);
    }

    private function deactivateModules()
    {
        $fileSystem = new Filesystem();
        if (getenv('OXID_TEST_MODULES')) {
            foreach (explode(':', getenv('OXID_TEST_MODULES')) as $modulePath) {
                $moduleId = $this->getModuleId($modulePath);
                try {
                    $this->get(ModuleActivationBridgeInterface::class)->deactivate($moduleId, 1);
                } catch (ModuleConfigurationNotFoundException $e) {
                    // OK, has been deactivated somehow by the test
                }
                $fileSystem->remove($this->getTargetDirectory($modulePath, $moduleId));
            }
        }
        $fileSystem->remove(Path::join(OX_BASE_PATH, 'modules', self::TESTVENDOR));
    }

    private function getModuleId($modulePath)
    {
        $metadata = $this->get(MetaDataProviderInterface::class)->getData(Path::join($modulePath, 'metadata.php'));
        return $metadata['moduleData'][MetaDataProvider::METADATA_ID];
    }
}
