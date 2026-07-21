<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration as Configuration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;

final class ThemeConfigurationTest extends IntegrationTestCase
{
    private const THEME_ID = 'testTheme';
    private const SHOP_ID = 1;

    public function testRenderProvidesThemeConfigurationFormData(): void
    {
        $this->installTestTheme();

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);

        $this->assertSame('theme_config', $controller->render());

        $viewData = $controller->getViewData();
        $this->assertSame(self::THEME_ID, $viewData['themeId']);
        $this->assertSame('Test Theme', $viewData['themeTitle']);

        $settings = $this->getSettingsByName($viewData['settingGroups']['display']);
        $this->assertSame('str', $settings['testStringSetting']['type']);
        $this->assertSame('defaultValue', $settings['testStringSetting']['value']);
        $this->assertTrue($settings['testBoolSetting']['value']);
        $this->assertSame('option1', $settings['testSelectSetting']['value']);
        $this->assertSame(['option1', 'option2', 'option3'], $settings['testSelectSetting']['options']);
    }

    public function testRenderFallsBackToActiveThemeWithoutEditObjectId(): void
    {
        $this->installTestTheme();
        $this->activateTestTheme();

        $controller = $this->get(ThemeConfiguration::class);
        $controller->render();

        $this->assertSame(self::THEME_ID, $controller->getViewData()['themeId']);
    }

    public function testRenderDisplaysEnvironmentValueAndMarksSettingAsOverridden(): void
    {
        $this->installTestTheme();
        $this->saveEnvironmentConfiguration([
            'themeSettings' => [
                'testStringSetting' => ['value' => 'environmentValue'],
            ],
        ]);

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->render();

        $settings = $this->getSettingsByName($controller->getViewData()['settingGroups']['display']);

        $this->assertSame('environmentValue', $settings['testStringSetting']['value']);
        $this->assertTrue($settings['testStringSetting']['isEnvironmentOverridden']);
        $this->assertFalse($settings['testBoolSetting']['isEnvironmentOverridden']);
        $this->assertSame(
            'defaultValue',
            $this->getSavedConfiguration()->getSettingByName('testStringSetting')->getValue()
        );
    }

    public function testRenderOrdersSettingsByPosition(): void
    {
        $this->installTestTheme();

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->render();

        $this->assertSame(
            ['testBoolSetting', 'testSelectSetting', 'testStringSetting'],
            array_column($controller->getViewData()['settingGroups']['display'], 'name')
        );
    }

    public function testRenderDisplaysErrorForUnknownTheme(): void
    {
        $this->expectDisplayError('EXCEPTION_THEME_NOT_LOADED');

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId('unknownTheme');

        $this->assertSame('theme_config', $controller->render());
        $this->assertArrayNotHasKey('settingGroups', $controller->getViewData());
    }

    public function testSaveUpdatesThemeConfiguration(): void
    {
        $this->installTestTheme();
        $this->get(Request::class)->request->set('settings', [
            'testStringSetting' => 'changedValue',
            'testBoolSetting' => 'false',
            'testSelectSetting' => 'option2',
        ]);

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->save();

        $configuration = $this->getSavedConfiguration();
        $this->assertSame('changedValue', $configuration->getSettingByName('testStringSetting')->getValue());
        $this->assertFalse($configuration->getSettingByName('testBoolSetting')->getValue());
        $this->assertSame('option2', $configuration->getSettingByName('testSelectSetting')->getValue());
    }

    public function testSaveIgnoresUnknownSettingNames(): void
    {
        $this->installTestTheme();
        $this->get(Request::class)->request->set('settings', [
            'unknownSetting' => 'value',
            'testStringSetting' => 'changedValue',
        ]);

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->save();

        $configuration = $this->getSavedConfiguration();
        $this->assertSame('changedValue', $configuration->getSettingByName('testStringSetting')->getValue());
        $this->assertNull($configuration->getSettingByName('unknownSetting'));
    }

    public function testSaveRejectsForbiddenValueAndSavesRemainingSettings(): void
    {
        $this->installTestTheme();
        $this->expectInvalidValueError();
        $this->get(Request::class)->request->set('settings', [
            'testStringSetting' => '<script>alert(1)</script>',
            'testSelectSetting' => 'option3',
        ]);

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->save();

        $configuration = $this->getSavedConfiguration();
        $this->assertSame('defaultValue', $configuration->getSettingByName('testStringSetting')->getValue());
        $this->assertSame('option3', $configuration->getSettingByName('testSelectSetting')->getValue());
    }

    public function testSaveDisplaysErrorForUnknownTheme(): void
    {
        $this->expectDisplayError('EXCEPTION_THEME_NOT_LOADED');

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId('unknownTheme');
        $controller->save();
    }

    public function testSaveRejectsEnvironmentOverriddenSetting(): void
    {
        $this->installTestTheme();
        $this->saveEnvironmentConfiguration([
            'themeSettings' => [
                'testStringSetting' => ['value' => 'environmentValue'],
            ],
        ]);
        $this->get(Request::class)->request->set('settings', [
            'testStringSetting' => 'tamperedValue',
        ]);
        $this->expectDisplayError('THEME_SETTING_ENVIRONMENT_OVERRIDDEN_ERROR');

        $controller = $this->get(ThemeConfiguration::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->save();

        $this->assertSame(
            'defaultValue',
            $this->getSavedConfiguration()->getSettingByName('testStringSetting')->getValue()
        );
    }

    private function installTestTheme(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install(__DIR__ . '/Fixtures/testTheme');
    }

    private function activateTestTheme(): void
    {
        $this->get(ThemeActivationServiceInterface::class)->activate(self::THEME_ID, self::SHOP_ID);
    }

    private function getSavedConfiguration(): Configuration
    {
        return $this->get(ThemeConfigurationDaoInterface::class)->get(self::THEME_ID, self::SHOP_ID);
    }

    private function getSettingsByName(array $settingGroup): array
    {
        return array_column($settingGroup, null, 'name');
    }

    private function saveEnvironmentConfiguration(array $configuration): void
    {
        $path = Path::join(
            EnvUrlFormatter::toEnvUrl(
                $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory()
            ),
            'shops',
            (string) self::SHOP_ID,
            'themes',
            self::THEME_ID . '.yaml'
        );

        $this->get(FileStorageFactoryInterface::class)->create($path)->save($configuration);
    }

    private function expectDisplayError(string $translationKey): void
    {
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())->method('addErrorToDisplay')->with($translationKey);

        Registry::set(UtilsView::class, $utilsView);
    }

    private function expectInvalidValueError(): void
    {
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())->method('addErrorToDisplay')->with($this->isInstanceOf(DisplayError::class));

        Registry::set(UtilsView::class, $utilsView);
    }
}
