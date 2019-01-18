<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Console\ConsoleTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

class ModuleCommandsTestCase extends TestCase
{
    use ContainerTrait;
    use ConsoleTrait;

    /**
     * @return Application
     */
    protected function getApplication(): Application
    {
        $application = $this->get('oxid_esales.console.symfony.component.console.application');
        $application->setAutoExit(false);

        return $application;
    }

    protected function cleanupTestData()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove(Registry::getConfig()->getModulesDir() . '/testmodule');
        $moduleList = oxNew(ModuleList::class);
        $moduleList->cleanup();
    }

    protected function prepareTestData()
    {
        $fileSystem = new Filesystem();
        $fileSystem->mirror(__DIR__ . '/Fixtures', Registry::getConfig()->getConfigParam('sShopDir'));
    }
}
