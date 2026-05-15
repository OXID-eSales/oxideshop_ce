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

    private string $initialThemeId = 'some-theme-id';
    private string $newThemeId = 'testTheme';
    private array $originalConfig;

    public function setUp(): void
    {
        parent::setUp();
        $this->setShopFixtures();
    }

    public function testThemeActivationOnSuccess(): void
    {
        $this->createCommandTester()
            ->execute(
                ['theme-id' => $this->newThemeId]
            );

        $this->assertSame($this->newThemeId, $this->getActiveTheme());
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testThemeAlreadyActivated(): void
    {
        $arguments = ['theme-id' => $this->newThemeId];
        $commandTester = $this->createCommandTester();

        $commandTester->execute($arguments);
        $commandTester->execute($arguments);

        $this->assertStringContainsString(
            \sprintf('Theme - "%s" is already active.', $this->newThemeId),
            $commandTester->getDisplay()
        );
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testNonExistingThemeActivation(): void
    {
        $nonExistingThemeId = 'some-theme-id';
        $commandTester = $this->createCommandTester();

        $commandTester->execute(['theme-id' => $nonExistingThemeId]);

        $this->assertStringContainsString(
            sprintf('Theme - "%s" not found.', $nonExistingThemeId),
            $commandTester->getDisplay()
        );
        $this->assertSame($this->initialThemeId, $this->getActiveTheme());
    }

    private function getActiveTheme(): string
    {
        return Registry::getConfig()->getConfigParam('sTheme');
    }

    private function setShopFixtures(): void
    {
        Registry::getConfig()->reinitialize();
        Registry::getConfig()->setConfigParam('sTheme', $this->initialThemeId);

        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");
    }

    private function createCommandTester(): CommandTester
    {
        return new CommandTester($this->get(ThemeActivateCommand::class));
    }
}
