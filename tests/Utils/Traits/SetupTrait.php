<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils\Traits;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\DbMetaDataHandler;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

trait SetupTrait
{
    use ContainerTrait;

    private $configFile = null;
    private $testConfigFile = null;
    private $filesystem = null;

    public function createViews()
    {
        $dbHandler = new DbMetaDataHandler();
        $dbHandler->updateViews();
    }

    public function initializeDatabase()
    {
        $this->setupCodeceptionContainer();
        $this->forceDatabaseSetup();
        $this->loadFixture(Path::join(INSTALLATION_ROOT_PATH, 'tests', 'Codeception', '_data', 'db_fixture.yml'));
    }

    public function deleteTestConfigFile()
    {
        if ($this->getFilesystem()->exists($this->getTestConfigFile())) {
            unlink($this->getTestConfigFile());
        }
    }

}