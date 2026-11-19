<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\SettingsHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SettingsHandlerTest extends IntegrationTestCase
{
    private string $themeId = 'testSettingsHandlerTheme';
    private string $settingName = 'testSetting';

    public function testTypeChangeReplacesValueWithThemeValue(): void
    {
        $this->runSettingsHandler('bool', '1');

        $this->runSettingsHandler('select', 'none');

        $this->assertSame(['select', 'none'], $this->getStoredSetting());
    }

    public function testUnchangedTypeKeepsStoredValue(): void
    {
        $this->runSettingsHandler('str', 'theme-value');
        $this->storeSettingValue('customised-value');

        $this->runSettingsHandler('str', 'theme-value');

        $this->assertSame(['str', 'customised-value'], $this->getStoredSetting());
    }

    private function runSettingsHandler(string $type, string $value): void
    {
        $theme = new class ($this->themeId, $this->settingName, $type, $value) {
            public function __construct(
                private readonly string $id,
                private readonly string $settingName,
                private readonly string $type,
                private readonly string $value
            ) {
            }

            public function getId(): string
            {
                return $this->id;
            }

            public function getInfo(string $name): array
            {
                return [
                    [
                        'group' => 'display',
                        'name' => $this->settingName,
                        'type' => $this->type,
                        'value' => $this->value,
                    ],
                ];
            }
        };

        oxNew(SettingsHandler::class)->setModuleType('theme')->run($theme);
    }

    private function storeSettingValue(string $value): void
    {
        $this->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->update('oxconfig')
            ->set('oxvarvalue', ':value')
            ->where('oxmodule = :module')
            ->andWhere('oxvarname = :name')
            ->setParameters(['value' => $value, 'module' => "theme:$this->themeId", 'name' => $this->settingName])
            ->execute();
    }

    /**
     * @return string[]
     */
    private function getStoredSetting(): array
    {
        return array_values(
            $this->get(QueryBuilderFactoryInterface::class)
                ->create()
                ->select('oxvartype', 'oxvarvalue')
                ->from('oxconfig')
                ->where('oxmodule = :module')
                ->andWhere('oxvarname = :name')
                ->setParameters(['module' => "theme:$this->themeId", 'name' => $this->settingName])
                ->execute()
                ->fetchAssociative()
        );
    }
}
