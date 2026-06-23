<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Support\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use OxidEsales\Codeception\Module\CommandTrait;
use OxidEsales\Codeception\Module\Oxideshop;
use OxidEsales\Codeception\Module\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
final class Acceptance extends Module
{
    use CommandTrait;

    private ?string $themeConfigurationBaseline = null;

    public function _before(TestInterface $test): void
    {
        $file = $this->themeConfigurationFilePath();
        if (!is_file($file)) {
            return;
        }

        if ($this->themeConfigurationBaseline === null) {
            $this->themeConfigurationBaseline = (string) file_get_contents($file);
            return;
        }

        if ((string) file_get_contents($file) !== $this->themeConfigurationBaseline) {
            file_put_contents($file, $this->themeConfigurationBaseline);
            $this->processConsoleCommand('oe:cache:clear');
        }
    }

    public function getCurrentURL(): string
    {
        return $this->getModule('WebDriver')->webDriver->getCurrentURL();
    }

    public function updateThemeSetting(string $name, mixed $value, string $themeId = 'apex', int $shopId = 1): void
    {
        $file = $this->themeConfigurationFilePath($themeId, $shopId);

        $data = Yaml::parseFile($file);
        if (isset($data['themeSettings'][$name])) {
            $data['themeSettings'][$name]['value'] = $value;
            file_put_contents($file, Yaml::dump($data, 8));
        }

        $this->processConsoleCommand('oe:cache:clear');
    }

    public function setThemeActivated(bool $activated, string $themeId = 'apex', int $shopId = 1): void
    {
        $file = $this->themeConfigurationFilePath($themeId, $shopId);

        $data = Yaml::parseFile($file);
        $data['activated'] = $activated;
        file_put_contents($file, Yaml::dump($data, 8));

        $this->processConsoleCommand('oe:cache:clear');
    }

    private function themeConfigurationFilePath(string $themeId = 'apex', int $shopId = 1): string
    {
        $context = ContainerFactory::getInstance()->getContainer()->get(BasicContextInterface::class);

        return Path::join($context->getShopConfigurationDirectory($shopId), 'themes', $themeId . '.yaml');
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
