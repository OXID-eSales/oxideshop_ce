<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplatesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use PHPUnit\Framework\TestCase;

final class TemplatesDataMapperTest extends TestCase
{
    public function testMappingWithThemedTemplates(): void
    {
        $initialData = [
            'templates' => [
                '01.tpl' => '01,ext.tpl',
                'some-theme' => [
                    '01.tpl' => '01.theme.ext.tpl',
                    '02.tpl' => '02.theme.ext.tpl',
                ],
                '02.tpl' => '02.ext.tpl',
            ],
        ];
        $templatesDataMapper = new TemplatesDataMapper();

        $moduleConfigurationObject = $templatesDataMapper->fromData(new ModuleConfiguration(), $initialData);
        $moduleConfigurationArray = $templatesDataMapper->toData($moduleConfigurationObject);

        $this->assertSame($initialData, $moduleConfigurationArray);
    }
}
