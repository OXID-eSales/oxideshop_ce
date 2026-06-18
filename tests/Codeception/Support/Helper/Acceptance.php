<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Support\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use OxidEsales\Codeception\Module\Oxideshop;
use OxidEsales\Codeception\Module\ProjectConfiguration;
use OxidEsales\Codeception\Module\ThemeSettingTrait;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
final class Acceptance extends Module
{
    use ThemeSettingTrait;

    public function _beforeSuite(array $settings = []): void
    {
        parent::_beforeSuite($settings);
        $this->installThemeConfiguration();
        $this->backupThemeConfiguration();
    }

    public function _after(TestInterface $test): void
    {
        $this->restoreThemeConfiguration();
        parent::_after($test);
    }

    public function _afterSuite(): void
    {
        $this->cleanupThemeConfigurationBackup();
        parent::_afterSuite();
    }

    public function getCurrentURL(): string
    {
        return $this->getModule('WebDriver')->webDriver->getCurrentURL();
    }

    public function updateProjectConfigurations(array $parameters, array $services): void
    {
        $module = $this->getModule(ProjectConfiguration::class);
        $module->_reconfigure([
            'parameters' => $parameters,
            'services' => $services,
        ]);
        $module->dumpProjectConfigurations();
        $this->getModule(Oxideshop::class)->clearShopCache();
    }

    public function restoreProjectConfigurations(): void
    {
        $module = $this->getModule(ProjectConfiguration::class);
        $module->_resetConfig();
        $module->dumpProjectConfigurations();
        $this->getModule(Oxideshop::class)->clearShopCache();
    }
}
