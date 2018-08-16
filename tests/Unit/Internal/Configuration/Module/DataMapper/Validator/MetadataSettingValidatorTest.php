<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper\Validator;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator\MetadataSettingValidator;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MetadataSettingValidatorTest extends TestCase
{
    /**
     * @expectedException OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator\MetadataVersionException
     */
    public function testWithInvalidMetadataVersion()
    {
        $validator = new MetadataSettingValidator();
        $validator->addAllowableMetadataSettings('v1.2', []);

        $validator->validate('v3.14', new ModuleSetting('blocks', []));
    }

    /**
     * @expectedException OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator\MetadataSettingException
     */
    public function testWithNonExistentSetting()
    {
        $validator = new MetadataSettingValidator();
        $validator->addAllowableMetadataSettings('v1.2', ['blocks']);

        $setting = new ModuleSetting('invalidSetting', []);

        $validator->validate('v1.2', $setting);
    }

    public function testValidCorrectMetadataSettings()
    {
        $validator = new MetadataSettingValidator();
        $validator->addAllowableMetadataSettings('v1.2', [
            'blocks',
            'controllers',
        ]);

        $setting = new ModuleSetting('blocks', []);

        $validator->validate('v1.2', $setting);
    }
}
