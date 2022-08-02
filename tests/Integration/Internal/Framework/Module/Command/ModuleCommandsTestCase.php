<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Console\ConsoleTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Webmozart\PathUtil\Path;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class ModuleCommandsTestCase extends TestCase
{
    use ConsoleTrait;

    protected $modulesPath = __DIR__ . '/Fixtures/modules/';

    protected $moduleId = 'testmodule';

    private $container;

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        ContainerFactory::resetContainer();
        parent::tearDown();
    }

    protected function get(string $serviceId)
    {
        if ($this->container === null) {
            $this->container = ContainerFactory::getInstance()->getContainer();
        }

        return $this->container->get($serviceId);
    }

    /**
     * @return Application
     */
    protected function getApplication(): Application
    {
        $application = $this->get('oxid_esales.console.symfony.component.console.application');
        $application->setAutoExit(false);

        return $application;
    }

    protected function cleanupTestData(): void
    {
        $this
            ->get(ModuleInstallerInterface::class)
            ->uninstall(
                new OxidEshopPackage(
                    Path::join($this->modulesPath, $this->moduleId)
                )
            );
    }

    protected function installTestModule(): void
    {
        $this
            ->get(ModuleInstallerInterface::class)
            ->install(new OxidEshopPackage(Path::join($this->modulesPath, $this->moduleId)));
    }

    protected function executeCommand(string $command, array $input = []): string
    {
        $commandTester = new CommandTester(
            $this->get('console.command_loader')->get($command)
        );

        $commandTester->execute($input);
        return $commandTester->getDisplay();
    }
}
