<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Command;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Command\ThemeActivateCommand;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ThemeActivateCommandTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $fixtureDirectory = __DIR__ . '/Fixtures';

    private string $themeId = 'testTheme';
    private array $originalConfig;

    public function setUp(): void
    {
        parent::setUp();
        $this->saveOriginalConfig();
        $this->setShopFixtures();
    }

    public function tearDown(): void
    {
        $this->restoreOriginalConfig();
        parent::tearDown();
    }

    public function testThemeActivationOnSuccess(): void
    {
        $arguments = ['theme-id' => $this->themeId];

        $themeActivateCommand = $this->getCommandObject();
        $commandTester = new CommandTester($themeActivateCommand);

        $commandTester->execute($arguments);
        $this->assertSame($this->themeId, $this->getActiveTheme());
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testThemeAlreadyActivated(): void
    {
        $arguments = ['theme-id' => $this->themeId];

        $themeActivateCommand = $this->getCommandObject();
        $commandTester = new CommandTester($themeActivateCommand);
        $commandTester->execute($arguments);
        $commandTester->execute($arguments); //running twice is important

        $this->assertStringContainsString(
            sprintf('Theme - "%s" is already active.', $this->themeId),
            $commandTester->getDisplay()
        );
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testNonExistingThemeActivation(): void
    {
        $nonExistingThemeId = 'some-theme-id';
        $arguments = ['theme-id' => $nonExistingThemeId];

        $themeActivateCommand = $this->getCommandObject();
        $commandTester = new CommandTester($themeActivateCommand);
        $commandTester->execute($arguments);

        $this->assertStringContainsString(
            sprintf('Theme - "%s" not found.', $nonExistingThemeId),
            $commandTester->getDisplay()
        );
        $this->assertSame('absolute-dummy-value', $this->getActiveTheme());
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }

    private function getCommandObject(): ThemeActivateCommand
    {
        return $this->get(ThemeActivateCommand::class);
    }

    private function getActiveTheme(): string
    {
        return Registry::getConfig()->getConfigParam('sTheme');
    }

    private function setShopFixtures(): void
    {
        Registry::getConfig()->reinitialize();
        Registry::getConfig()->setConfigParam('sShopDir', "$this->fixtureDirectory/shop/source/");
        Registry::getConfig()->setConfigParam('sTheme', 'absolute-dummy-value');
    }

    private function saveOriginalConfig(): void
    {
        $this->originalConfig = [
            'sShopDir' => Registry::getConfig()->getConfigParam('sShopDir'),
            'sTheme' => Registry::getConfig()->getConfigParam('sTheme')
        ];
    }

    private function restoreOriginalConfig(): void
    {
        Registry::getConfig()->reinitialize();
        Registry::getConfig()->setConfigParam('sShopDir', $this->originalConfig['sShopDir']);
        Registry::getConfig()->setConfigParam('sTheme', $this->originalConfig['sTheme']);
    }
}
